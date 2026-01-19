<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    private const API_URL = 'https://apis.data.go.kr/1220000/retrieveTrifFxrtInfo/getRetrieveTrifFxrtInfo';
    private const CACHE_KEY = 'exchange_rates';
    private const CACHE_DURATION = 86400; // 1일 (관세환율은 주 1회 갱신)

    /**
     * 환율 정보를 가져옵니다.
     * 
     * @return array USD, EUR, GBP 환율 정보
     */
    public function getExchangeRates(): array
    {
        // 캐시에서 먼저 확인
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        $apiKey = env('CUSTOMS_EXCHANGE_API_KEY', env('KOREAEXIM_API_KEY'));
        
        // API 키가 없으면 기본값 반환
        if (empty($apiKey)) {
            return $this->getDefaultRates();
        }

        // 적용개시일자 (오늘 날짜, YYYYMMDD 형식)
        $aplyBgnDt = now('Asia/Seoul')->format('Ymd');
        
        // 주간환율구분코드 (2: 수입)
        $weekFxrtTpcd = 2;

        try {
            $response = Http::timeout(10)->get(self::API_URL, [
                'serviceKey' => $apiKey,
                'aplyBgnDt' => $aplyBgnDt,
                'weekFxrtTpcd' => $weekFxrtTpcd,
            ]);

            if ($response->successful()) {
                $xml = $response->body();
                $rates = $this->parseXml($xml);
                
                // 캐시에 저장
                Cache::put(self::CACHE_KEY, $rates, self::CACHE_DURATION);
                
                return $rates;
            }
        } catch (\Exception $e) {
            Log::error('환율 API 호출 실패: ' . $e->getMessage());
        }

        // API 호출 실패 시 기본값 반환
        return $this->getDefaultRates();
    }

    /**
     * XML 응답을 파싱하여 환율 정보를 추출합니다.
     * 
     * @param string $xml
     * @return array
     */
    private function parseXml(string $xml): array
    {
        $rates = $this->getDefaultRates();
        
        try {
            $xmlObject = simplexml_load_string($xml);
            if ($xmlObject === false) {
                return $rates;
            }

            // 관세청 API 응답 구조: <response><body><items><item>...</item></items></body></response>
            if (isset($xmlObject->body) && isset($xmlObject->body->items) && isset($xmlObject->body->items->item)) {
                foreach ($xmlObject->body->items->item as $item) {
                    $currSgn = (string) ($item->currSgn ?? ''); // 통화부호 (USD, EUR, GBP)
                    $fxrt = (string) ($item->fxrt ?? ''); // 환율
                    
                    // 통화 코드 정규화
                    $currSgn = strtoupper(trim($currSgn));
                    
                    if (empty($fxrt)) {
                        continue;
                    }
                    
                    // USD, EUR, GBP 환율 추출
                    if ($currSgn === 'USD') {
                        $rates['usd'] = $this->formatRate($fxrt);
                    } elseif ($currSgn === 'EUR') {
                        $rates['eur'] = $this->formatRate($fxrt);
                    } elseif ($currSgn === 'GBP') {
                        $rates['gbp'] = $this->formatRate($fxrt);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('환율 XML 파싱 실패: ' . $e->getMessage());
        }

        return $rates;
    }

    /**
     * 환율 값을 포맷팅합니다.
     * 
     * @param string $rate
     * @return string
     */
    private function formatRate(string $rate): string
    {
        // 쉼표 제거 후 숫자로 변환
        $numericRate = (float) str_replace(',', '', $rate);
        
        // 소수점 둘째 자리까지 표시
        return number_format($numericRate, 2, '.', ',');
    }

    /**
     * 기본 환율 값을 반환합니다.
     * 
     * @return array
     */
    private function getDefaultRates(): array
    {
        return [
            'usd' => null,
            'eur' => null,
            'gbp' => null,
        ];
    }
}
