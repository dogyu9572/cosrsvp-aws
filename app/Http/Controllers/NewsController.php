<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NaverNewsService;
use Illuminate\Support\Collection;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NaverNewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * 뉴스 목록 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "06";
        $gName = "Latest News";
        $sName = "";

        // 카테고리 필터 (all, main_news, lifestyle, fashion, entertainment)
        $category = $request->get('category', 'all');
        $category = in_array($category, ['all', 'main_news', 'lifestyle', 'fashion', 'entertainment']) 
            ? $category 
            : 'all';

        // 트렌드 키워드로 뉴스 가져오기
        $newsItems = $this->newsService->getTrendingNews(
            $category === 'all' ? null : $category,
            10, // 트렌드 키워드 수
            5   // 키워드당 뉴스 수
        );

        // 카테고리별 필터링
        $items = collect($newsItems);
        
        if ($category !== 'all') {
            $items = $items->filter(function ($item) use ($category) {
                return ($item['category'] ?? 'all') === $category;
            });
        }

        // 검색 필터 적용
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $items = $items->filter(function ($item) use ($keyword) {
                return stripos($item['title'] ?? '', $keyword) !== false 
                    || stripos($item['description'] ?? '', $keyword) !== false;
            });
        }

        // 페이지네이션 처리
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
        
        $currentPage = $request->get('page', 1);
        $total = $items->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = $items->slice($offset, $perPage)->values();

        // 페이지네이션 정보 생성
        $news = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('news.index', compact('gNum', 'gName', 'sName', 'news', 'category'));
    }

    /**
     * 뉴스 상세 페이지
     */
    public function show($id, Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "06";
        $gName = "Latest News";
        $sName = "";

        // 카테고리 필터
        $category = $request->get('category', 'all');
        $category = in_array($category, ['all', 'main_news', 'lifestyle', 'fashion', 'entertainment']) 
            ? $category 
            : 'all';

        // 트렌드 키워드로 뉴스 가져오기
        $newsItems = $this->newsService->getTrendingNews(
            $category === 'all' ? null : $category,
            10, // 트렌드 키워드 수
            5   // 키워드당 뉴스 수
        );
        $items = collect($newsItems);

        // ID로 아이템 찾기
        $newsItem = $items->firstWhere('id', (int)$id);

        if (!$newsItem) {
            abort(404, '뉴스를 찾을 수 없습니다.');
        }

        // 이전/다음 아이템 찾기 (발행일 기준 최신순)
        $sortedItems = $items->sortByDesc(function ($item) {
            return strtotime($item['pubDate'] ?? '1970-01-01');
        })->values();
        
        $currentIndex = $sortedItems->search(function ($item) use ($id) {
            return $item['id'] == $id;
        });

        $prev = null;
        $next = null;

        if ($currentIndex !== false) {
            // 이전 아이템
            if ($currentIndex > 0) {
                $prev = $sortedItems[$currentIndex - 1];
            }

            // 다음 아이템
            if ($currentIndex < $sortedItems->count() - 1) {
                $next = $sortedItems[$currentIndex + 1];
            }
        }

        return view('news.show', compact('gNum', 'gName', 'sName', 'newsItem', 'prev', 'next', 'category'));
    }
}
