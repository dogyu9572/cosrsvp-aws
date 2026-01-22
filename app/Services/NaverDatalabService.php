<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NaverDatalabService
{
    private const API_URL = 'https://openapi.naver.com/v1/datalab/search';
    private const CACHE_KEY_PREFIX = 'naver_datalab_';
    private const CACHE_DURATION = 1800; // 30분

    /**
     * 네이버 데이터랩 검색어 트렌드 데이터를 가져옵니다.
     * 
     * @param string|null $category 카테고리 필터 (all, main_news, lifestyle, fashion, entertainment)
     * @return array 트렌드 데이터
     */
    public function getTrendData(?string $category = null): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX . ($category ?? 'all');
        
        // 캐시에서 먼저 확인
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $clientId = config('services.naver.datalab.client_id');
        $clientSecret = config('services.naver.datalab.client_secret');
        
        // API 키가 없으면 빈 데이터 반환
        if (empty($clientId) || empty($clientSecret)) {
            Log::warning('네이버 데이터랩 API 키가 설정되지 않았습니다.');
            return $this->getEmptyData();
        }

        // 조회 기간 설정 (최근 1개월)
        $endDate = Carbon::now('Asia/Seoul')->format('Y-m-d');
        $startDate = Carbon::now('Asia/Seoul')->subMonth()->format('Y-m-d');

        // 키워드 그룹 설정
        $keywordGroups = $this->getKeywordGroups($category);

        // API 요청 본문
        $requestBody = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'timeUnit' => 'date',
            'keywordGroups' => $keywordGroups,
            'ages' => ['2', '3', '4', '10', '11'], // 13~18세, 19~24세, 25~29세, 55~59세, 60세 이상
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Naver-Client-Id' => $clientId,
                    'X-Naver-Client-Secret' => $clientSecret,
                    'Content-Type' => 'application/json',
                ])
                ->post(self::API_URL, $requestBody);

            if ($response->successful()) {
                $data = $response->json();
                $trendData = $this->parseTrendData($data, $category);
                
                // 캐시에 저장
                Cache::put($cacheKey, $trendData, self::CACHE_DURATION);
                
                return $trendData;
            } else {
                Log::error('네이버 데이터랩 API 호출 실패: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('네이버 데이터랩 API 호출 실패: ' . $e->getMessage());
        }

        // API 호출 실패 시 빈 데이터 반환
        return $this->getEmptyData();
    }

    /**
     * 카테고리별 키워드 그룹을 반환합니다.
     * 
     * @param string|null $category
     * @return array
     */
    private function getKeywordGroups(?string $category): array
    {
        $allGroups = [
            [
                'groupName' => '주요 뉴스 및 경제',
                'keywords' => [
                    '환율', '금리', '코스피', '나스닥', '부동산', '아파트 청약', '유가', '물가', '세금', '연금',
                    '고용 지표', '국제 정세', '국회', '정책', '한국은행', '반도체 산업', '수출입', '스타트업', '재테크', '금융'
                ]
            ],
            [
                'groupName' => '라이프스타일',
                'keywords' => [
                    '추천 여행지', '국내 여행지', '해외 여행지', '항공권 예약', '숙소 예약', '맛집 추천', '카페 투어', '호캉스', '캠핑장', '제주도 여행',
                    '주말 나들이', '드라이브 코스', '건강 검진', '영양제 추천', '인테리어 소품', '자취 필수템', '다이어트 식단', '명상법', '요가 수업', '반려동물 용품'
                ]
            ],
            [
                'groupName' => '패션',
                'keywords' => [
                    '패션 트렌드', '데일리룩', '오피스룩', '계절별 코디', '신발 추천', '명품 브랜드', '스트릿 패션', '잡화 추천', '향수 순위', '메이크업 트렌드',
                    '헤어스타일 추천', '운동화 브랜드', '숏패딩', '롱코트', '셋업 코디', '가방 추천', '액세서리', '선글라스', '워크웨어', '빈티지 패션'
                ]
            ],
            [
                'groupName' => '엔터테인먼트 및 문화',
                'keywords' => [
                    'K-POP', '한국 드라마', '영화 예매', '웹툰 추천', '공연 정보', '음원 차트', '아이돌 뉴스', '전시회 일정', '뮤지컬 추천', '연극 예매',
                    '넷플릭스 추천', '유튜브 트렌드', '연예 뉴스', '페스티벌', '도서 베스트셀러', '애니메이션', '게임 업데이트', '이스포츠', '시상식', '팬미팅 정보'
                ]
            ]
        ];

        // 카테고리별 필터링
        if ($category === 'main_news') {
            return [$allGroups[0]]; // 주요 뉴스 및 경제
        } elseif ($category === 'lifestyle') {
            return [$allGroups[1]]; // 라이프스타일
        } elseif ($category === 'fashion') {
            return [$allGroups[2]]; // 패션
        } elseif ($category === 'entertainment') {
            return [$allGroups[3]]; // 엔터테인먼트 및 문화
        }

        // all 또는 null인 경우 모든 그룹 반환
        return $allGroups;
    }

    /**
     * API 응답을 파싱하여 뉴스 리스트 형태로 변환합니다.
     * 
     * @param array $data API 응답 데이터
     * @param string|null $category
     * @return array
     */
    private function parseTrendData(array $data, ?string $category): array
    {
        $newsItems = [];
        $itemId = 1;

        if (!isset($data['results']) || !is_array($data['results'])) {
            return $this->getEmptyData();
        }

        foreach ($data['results'] as $result) {
            $groupName = $result['title'] ?? '';
            $keywords = $result['keywords'] ?? [];
            $trendData = $result['data'] ?? [];

            if (empty($trendData) || !is_array($trendData)) {
                continue;
            }

            // 트렌드 데이터를 날짜순으로 정렬 (최신순)
            usort($trendData, function ($a, $b) {
                $periodA = $a['period'] ?? '';
                $periodB = $b['period'] ?? '';
                return strcmp($periodB, $periodA); // 내림차순 (최신이 먼저)
            });

            // 각 키워드별로 뉴스 아이템 생성
            // 그룹의 트렌드 데이터를 각 키워드에 할당 (최신 데이터 우선)
            foreach ($keywords as $keyword) {
                // 최신 기간의 데이터 사용
                $latestData = !empty($trendData) ? $trendData[0] : null;

                if ($latestData) {
                    $period = $latestData['period'] ?? '';
                    $ratio = (float) ($latestData['ratio'] ?? 0);

                    // 뉴스 아이템으로 변환
                    $newsItems[] = [
                        'id' => $itemId++,
                        'title' => $keyword,
                        'category' => $this->mapCategory($groupName),
                        'group_name' => $groupName,
                        'ratio' => $ratio,
                        'period' => $period,
                        'date' => $period ? Carbon::parse($period)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                    ];
                }
            }
        }

        // ratio 기준으로 내림차순 정렬
        usort($newsItems, function ($a, $b) {
            return $b['ratio'] <=> $a['ratio'];
        });

        return [
            'items' => $newsItems,
            'total' => count($newsItems),
        ];
    }

    /**
     * 그룹명을 카테고리 코드로 매핑합니다.
     * 
     * @param string $groupName
     * @return string
     */
    private function mapCategory(string $groupName): string
    {
        $mapping = [
            '주요 뉴스 및 경제' => 'main_news',
            '라이프스타일' => 'lifestyle',
            '패션' => 'fashion',
            '엔터테인먼트 및 문화' => 'entertainment',
        ];

        return $mapping[$groupName] ?? 'all';
    }

    /**
     * 빈 데이터를 반환합니다.
     * 
     * @return array
     */
    private function getEmptyData(): array
    {
        return [
            'items' => [],
            'total' => 0,
        ];
    }

    /**
     * 특정 키워드의 상세 트렌드 데이터를 가져옵니다.
     * 
     * @param string $keyword
     * @param string|null $category
     * @return array|null
     */
    public function getKeywordDetail(string $keyword, ?string $category = null): ?array
    {
        $trendData = $this->getTrendData($category);
        
        foreach ($trendData['items'] as $item) {
            if ($item['title'] === $keyword) {
                return $item;
            }
        }

        return null;
    }

    /**
     * 트렌드가 높은 키워드만 반환합니다.
     * 
     * @param string|null $category 카테고리 필터
     * @param int $limit 상위 N개 키워드 (기본 10개)
     * @return array 키워드 배열 [['keyword' => '환율', 'ratio' => 95.5, 'category' => 'main_news'], ...]
     */
    public function getTrendKeywords(?string $category = null, int $limit = 10): array
    {
        $trendData = $this->getTrendData($category);
        $items = $trendData['items'] ?? [];

        // ratio 기준으로 내림차순 정렬되어 있으므로 상위 N개만 추출
        $keywords = [];
        foreach (array_slice($items, 0, $limit) as $item) {
            // 원래 요청한 카테고리를 유지 (entertainment와 fashion은 동일한 그룹이므로)
            $finalCategory = $item['category'];
            if ($category === 'entertainment' && $finalCategory === 'fashion') {
                $finalCategory = 'entertainment';
            }
            
            $keywords[] = [
                'keyword' => $item['title'],
                'ratio' => $item['ratio'],
                'category' => $finalCategory,
                'group_name' => $item['group_name'] ?? '',
            ];
        }

        return $keywords;
    }
}
