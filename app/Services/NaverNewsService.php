<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class NaverNewsService
{
    private const API_URL = 'https://openapi.naver.com/v1/search/news.json';
    private const CACHE_KEY_PREFIX = 'naver_news_';
    private const CACHE_DURATION = 600; // 10분

    protected $datalabService;

    public function __construct(NaverDatalabService $datalabService)
    {
        $this->datalabService = $datalabService;
    }

    /**
     * 키워드로 뉴스를 검색합니다.
     * 
     * @param string $keyword 검색 키워드
     * @param int $display 검색 결과 수 (최대 100)
     * @param string $sort 정렬 기준 (sim: 유사도순, date: 최신순)
     * @return array 뉴스 데이터
     */
    public function searchNews(string $keyword, int $display = 10, string $sort = 'date'): array
    {
        $cacheKey = self::CACHE_KEY_PREFIX . md5($keyword . $display . $sort);
        
        // 캐시에서 먼저 확인
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $clientId = config('services.naver.search.client_id');
        $clientSecret = config('services.naver.search.client_secret');
        
        // API 키가 없으면 빈 데이터 반환
        if (empty($clientId) || empty($clientSecret)) {
            Log::warning('네이버 검색 API 키가 설정되지 않았습니다.');
            return [];
        }

        // display 값 검증 (1~100)
        $display = max(1, min(100, $display));
        $sort = in_array($sort, ['sim', 'date']) ? $sort : 'date';

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Naver-Client-Id' => $clientId,
                    'X-Naver-Client-Secret' => $clientSecret,
                ])
                ->get(self::API_URL, [
                    'query' => $keyword,
                    'display' => $display,
                    'sort' => $sort,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $newsItems = $this->parseNewsData($data, $keyword);
                
                // 캐시에 저장
                Cache::put($cacheKey, $newsItems, self::CACHE_DURATION);
                
                return $newsItems;
            } else {
                Log::error('네이버 뉴스 검색 API 호출 실패: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('네이버 뉴스 검색 API 호출 실패: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * 여러 키워드로 뉴스를 검색합니다.
     * 
     * @param array $keywords 키워드 배열
     * @param int $perKeyword 키워드당 뉴스 수
     * @return array 통합된 뉴스 데이터
     */
    public function searchNewsByKeywords(array $keywords, int $perKeyword = 5): array
    {
        $allNews = [];
        $seenLinks = []; // 중복 제거용

        foreach ($keywords as $keywordInfo) {
            $keyword = is_array($keywordInfo) ? $keywordInfo['keyword'] : $keywordInfo;
            $category = is_array($keywordInfo) ? ($keywordInfo['category'] ?? 'all') : 'all';
            
            $newsItems = $this->searchNews($keyword, $perKeyword, 'date');
            
            foreach ($newsItems as $news) {
                // 중복 제거 (originallink 기준)
                $link = $news['originallink'] ?? $news['link'] ?? '';
                if (empty($link) || isset($seenLinks[$link])) {
                    continue;
                }
                
                $seenLinks[$link] = true;
                
                // 키워드 및 카테고리 정보 추가
                $news['keyword'] = $keyword;
                $news['category'] = $category;
                
                $allNews[] = $news;
            }
        }

        // 발행일 기준 최신순 정렬
        usort($allNews, function ($a, $b) {
            $dateA = strtotime($a['pubDate'] ?? '1970-01-01');
            $dateB = strtotime($b['pubDate'] ?? '1970-01-01');
            return $dateB <=> $dateA;
        });

        return $allNews;
    }

    /**
     * 트렌드 키워드로 뉴스를 가져옵니다.
     * 
     * @param string|null $category 카테고리 필터
     * @param int $keywordLimit 트렌드 키워드 수 (기본 10개)
     * @param int $perKeyword 키워드당 뉴스 수 (기본 5개)
     * @return array 통합된 뉴스 데이터
     */
    public function getTrendingNews(?string $category = null, int $keywordLimit = 10, int $perKeyword = 5): array
    {
        // 1단계: 트렌드 API로 키워드 선별
        $trendKeywords = $this->datalabService->getTrendKeywords($category, $keywordLimit);
        
        if (empty($trendKeywords)) {
            return [];
        }

        // 2단계: 선별된 키워드로 뉴스 검색
        $newsItems = $this->searchNewsByKeywords($trendKeywords, $perKeyword);

        // 3단계: ID 추가
        $newsWithId = [];
        $id = 1;
        foreach ($newsItems as $news) {
            $news['id'] = $id++;
            $newsWithId[] = $news;
        }

        return $newsWithId;
    }

    /**
     * API 응답을 파싱하여 뉴스 데이터를 추출합니다.
     * 
     * @param array $data API 응답 데이터
     * @param string $keyword 검색 키워드
     * @return array 뉴스 아이템 배열
     */
    private function parseNewsData(array $data, string $keyword): array
    {
        $newsItems = [];

        if (!isset($data['items']) || !is_array($data['items'])) {
            return $newsItems;
        }

        foreach ($data['items'] as $item) {
            $title = $this->cleanHtml($item['title'] ?? '');
            $description = $this->cleanHtml($item['description'] ?? '');
            $pubDate = $item['pubDate'] ?? '';
            
            // pubDate 파싱 (예: "Mon, 15 Jan 2024 12:00:00 +0900")
            $parsedDate = null;
            if ($pubDate) {
                try {
                    $parsedDate = Carbon::parse($pubDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $parsedDate = Carbon::now()->format('Y-m-d');
                }
            } else {
                $parsedDate = Carbon::now()->format('Y-m-d');
            }

            $newsItems[] = [
                'title' => $title,
                'link' => $item['link'] ?? '',
                'originallink' => $item['originallink'] ?? '',
                'description' => $description,
                'pubDate' => $pubDate,
                'date' => $parsedDate,
                'keyword' => $keyword,
            ];
        }

        return $newsItems;
    }

    /**
     * HTML 태그와 특수 문자를 제거합니다.
     * 
     * @param string $text
     * @return string
     */
    private function cleanHtml(string $text): string
    {
        // HTML 태그 제거
        $text = strip_tags($text);
        
        // 네이버 특수 문자 제거 (&lt;b&gt; 등)
        $text = str_replace(['&lt;', '&gt;', '&amp;', '&quot;', '&apos;'], ['<', '>', '&', '"', "'"], $text);
        $text = strip_tags($text);
        
        // HTML 엔티티 디코드
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        return trim($text);
    }
}
