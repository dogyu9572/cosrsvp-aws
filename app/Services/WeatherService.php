<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WeatherService
{
    private const API_URL = 'https://apis.data.go.kr/1360000/VilageFcstInfoService_2.0/getVilageFcst';
    private const CACHE_KEY = 'weather_info';
    private const CACHE_DURATION = 1800; // 30분

    // 서울시청 좌표 (기본값)
    private const DEFAULT_LAT = 37.5665;
    private const DEFAULT_LNG = 126.9780;

    /**
     * 날씨 정보를 가져옵니다.
     * 
     * @param float|null $lat 위도
     * @param float|null $lng 경도
     * @return array 기온, 하늘상태, 강수형태 정보
     */
    public function getWeatherInfo(?float $lat = null, ?float $lng = null): array
    {
        // 캐시에서 먼저 확인
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        $apiKey = env('WEATHER_API_KEY');
        
        // API 키가 없으면 기본값 반환
        if (empty($apiKey)) {
            return $this->getDefaultWeather();
        }

        // 좌표 설정 (없으면 서울시청)
        $latitude = $lat ?? self::DEFAULT_LAT;
        $longitude = $lng ?? self::DEFAULT_LNG;

        // 격자 좌표 변환
        $grid = $this->convertLatLngToGrid($latitude, $longitude);
        
        // base_date, base_time 계산
        $baseDateTime = $this->getBaseDateTime();
        
        try {
            $response = Http::timeout(10)->get(self::API_URL, [
                'serviceKey' => $apiKey,
                'pageNo' => 1,
                'numOfRows' => 10,
                'dataType' => 'JSON',
                'base_date' => $baseDateTime['date'],
                'base_time' => $baseDateTime['time'],
                'nx' => $grid['x'],
                'ny' => $grid['y'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $weather = $this->parseWeatherData($data);
                
                if ($weather) {
                    // 캐시에 저장
                    Cache::put(self::CACHE_KEY, $weather, self::CACHE_DURATION);
                    return $weather;
                }
            }
        } catch (\Exception $e) {
            Log::error('날씨 API 호출 실패: ' . $e->getMessage());
        }

        // API 호출 실패 시 기본값 반환
        return $this->getDefaultWeather();
    }

    /**
     * 위도/경도를 기상청 격자 좌표로 변환합니다.
     * 
     * @param float $lat 위도
     * @param float $lng 경도
     * @return array ['x' => nx, 'y' => ny]
     */
    private function convertLatLngToGrid(float $lat, float $lng): array
    {
        // 기상청 격자 좌표 변환 공식
        $RE = 6371.00877; // 지구 반경(km)
        $GRID = 5.0; // 격자 간격(km)
        $SLAT1 = 30.0; // 투영 위도1(degree)
        $SLAT2 = 60.0; // 투영 위도2(degree)
        $OLON = 126.0; // 기준점 경도(degree)
        $OLAT = 38.0; // 기준점 위도(degree)
        $XO = 43; // 기준점 X좌표(GRID)
        $YO = 136; // 기준점 Y좌표(GRID)

        $DEGRAD = M_PI / 180.0;
        $RADDEG = 180.0 / M_PI;

        $re = $RE / $GRID;
        $slat1 = $SLAT1 * $DEGRAD;
        $slat2 = $SLAT2 * $DEGRAD;
        $olon = $OLON * $DEGRAD;
        $olat = $OLAT * $DEGRAD;

        $sn = tan(M_PI * 0.25 + $slat2 * 0.5) / tan(M_PI * 0.25 + $slat1 * 0.5);
        $sn = log(cos($slat1) / cos($slat2)) / log($sn);
        $sf = tan(M_PI * 0.25 + $slat1 * 0.5);
        $sf = pow($sf, $sn) * cos($slat1) / $sn;
        $ro = tan(M_PI * 0.25 + $olat * 0.5);
        $ro = $re * $sf / pow($ro, $sn);

        $ra = tan(M_PI * 0.25 + ($lat) * $DEGRAD * 0.5);
        $ra = $re * $sf / pow($ra, $sn);
        $theta = $lng * $DEGRAD - $olon;
        if ($theta > M_PI) $theta -= 2.0 * M_PI;
        if ($theta < -M_PI) $theta += 2.0 * M_PI;
        $theta *= $sn;

        $x = floor($ra * sin($theta) + $XO + 0.5);
        $y = floor($ro - $ra * cos($theta) + $YO + 0.5);

        return [
            'x' => (int)$x,
            'y' => (int)$y,
        ];
    }

    /**
     * base_date와 base_time을 계산합니다.
     * 기상청은 매일 02, 05, 08, 11, 14, 17, 20, 23시에 발표합니다.
     * 
     * @return array ['date' => YYYYMMDD, 'time' => HHMM]
     */
    private function getBaseDateTime(): array
    {
        $now = Carbon::now('Asia/Seoul');
        $hour = (int)$now->format('H');
        
        // 발표 시각 배열 (02, 05, 08, 11, 14, 17, 20, 23)
        $baseTimes = [2, 5, 8, 11, 14, 17, 20, 23];
        
        // 현재 시간보다 작거나 같은 가장 가까운 발표 시각 찾기
        $baseTime = 23; // 기본값 (전날 23시)
        $baseDate = $now->copy()->subDay();
        
        foreach ($baseTimes as $time) {
            if ($hour >= $time) {
                $baseTime = $time;
                $baseDate = $now->copy();
            }
        }
        
        // 현재 시간이 02시 이전이면 전날 23시 데이터 사용
        if ($hour < 2) {
            $baseTime = 23;
            $baseDate = $now->copy()->subDay();
        }
        
        return [
            'date' => $baseDate->format('Ymd'),
            'time' => str_pad($baseTime, 2, '0', STR_PAD_LEFT) . '00',
        ];
    }

    /**
     * API 응답을 파싱하여 날씨 정보를 추출합니다.
     * 
     * @param array $data
     * @return array|null
     */
    private function parseWeatherData(array $data): ?array
    {
        try {
            if (!isset($data['response']['body']['items']['item'])) {
                return null;
            }

            $items = $data['response']['body']['items']['item'];
            if (!is_array($items)) {
                return null;
            }

            $weather = [
                'temperature' => null,
                'sky' => null,
                'pty' => null,
            ];

            // 현재 시간 기준 가장 가까운 데이터 찾기
            $now = Carbon::now('Asia/Seoul');
            $currentDate = $now->format('Ymd');
            $currentTime = $now->format('H') . '00';

            foreach ($items as $item) {
                $fcstDate = $item['fcstDate'] ?? '';
                $fcstTime = $item['fcstTime'] ?? '';
                
                // 현재 시간과 가장 가까운 데이터 찾기
                if ($fcstDate === $currentDate && $fcstTime <= $currentTime) {
                    $category = $item['category'] ?? '';
                    $value = $item['fcstValue'] ?? '';

                    switch ($category) {
                        case 'TMP': // 기온
                            $weather['temperature'] = (int)$value;
                            break;
                        case 'SKY': // 하늘상태
                            $weather['sky'] = (int)$value;
                            break;
                        case 'PTY': // 강수형태
                            $weather['pty'] = (int)$value;
                            break;
                    }
                }
            }

            // 값이 없으면 첫 번째 데이터 사용
            if ($weather['temperature'] === null || $weather['sky'] === null) {
                foreach ($items as $item) {
                    $category = $item['category'] ?? '';
                    $value = $item['fcstValue'] ?? '';

                    if ($category === 'TMP' && $weather['temperature'] === null) {
                        $weather['temperature'] = (int)$value;
                    }
                    if ($category === 'SKY' && $weather['sky'] === null) {
                        $weather['sky'] = (int)$value;
                    }
                    if ($category === 'PTY' && $weather['pty'] === null) {
                        $weather['pty'] = (int)$value;
                    }
                }
            }

            // 기본값 설정하지 않음 (null 유지)
            return $weather;

        } catch (\Exception $e) {
            Log::error('날씨 데이터 파싱 실패: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 기본 날씨 값을 반환합니다.
     * 
     * @return array
     */
    private function getDefaultWeather(): array
    {
        return [
            'temperature' => null,
            'sky' => null,
            'pty' => null,
        ];
    }

    /**
     * SKY 코드에 따른 아이콘 경로를 반환합니다.
     * 기획 문서의 매핑 규칙에 따라 SKY와 PTY 조합으로 아이콘 결정
     * 
     * @param int|null $sky SKY 코드 (1: 맑음, 3: 구름많음, 4: 흐림)
     * @param int|null $pty PTY 코드 (0: 없음, 1: 비, 2: 비/눈, 3: 눈, 4: 소나기)
     * @return string|null 아이콘 경로
     */
    public function getWeatherIcon(?int $sky, ?int $pty): ?string
    {
        // 값이 없으면 null 반환
        if ($sky === null || $pty === null) {
            return null;
        }
        
        // 강수형태가 있으면 강수 아이콘 우선
        if ($pty > 0) {
            if ($pty == 3) {
                // 눈
                return '/pub/images/Icon ionic-ios-snow.png';
            }
            // 비, 비/눈, 소나기
            // 소나기(4)이고 맑음(1)이면 태양+비 아이콘, 아니면 비 구름 아이콘
            if ($pty == 4 && $sky == 1) {
                return '/pub/images/Icon weather-day-rain.png'; // 맑음 + 소나기
            }
            return '/pub/images/Icon feather-cloud-rain.png'; // 비, 비/눈
        }

        // 하늘상태에 따른 아이콘 (강수 없을 때)
        switch ($sky) {
            case 1: // 맑음
                return '/pub/images/Icon feather-sun.png';
            case 3: // 구름많음
            case 4: // 흐림
                return '/pub/images/Icon feather-cloud.png';
            default:
                return '/pub/images/Icon feather-sun.png';
        }
    }
}
