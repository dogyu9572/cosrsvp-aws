<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FaqController extends Controller
{
    /**
     * FAQ 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "07";
        $sNum = "02";
        $gName = "Contact Us";
        $sName = "FAQ";

        // FAQ 데이터 조회 (프로젝트 기수 필터링 없음)
        $faqs = $this->getFaqs($request);

        return view('faq.index', compact('gNum', 'sNum', 'gName', 'sName', 'faqs'));
    }

    /**
     * FAQ 조회 (프로젝트 기수 필터링 없음)
     */
    private function getFaqs(Request $request)
    {
        try {
            // FAQ 테이블명 확인 (board_faqs 또는 faqs)
            $tableName = 'board_faq';
            
            // 테이블이 없으면 faqs 테이블 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                $tableName = 'faq';
                if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                    Log::warning("FAQ 테이블이 존재하지 않음");
                    // 빈 페이지네이터 반환
                    return new \Illuminate\Pagination\LengthAwarePaginator(
                        collect(),
                        0,
                        10,
                        1,
                        ['path' => $request->url(), 'query' => $request->query()]
                    );
                }
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'custom_fields', 'created_at', 'sort_order')
                ->whereNull('deleted_at');

            // 정렬: sort_order 우선, 그 다음 최신순
            $faqs = $query->orderBy('sort_order', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // 데이터 변환
            $transformedFaqs = $faqs->map(function ($faq) {
                return (object) [
                    'id' => $faq->id,
                    'title' => $faq->title,
                    'content' => $faq->content,
                    'created_at' => $faq->created_at,
                ];
            });

            // 페이지네이션 처리
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
            
            $currentPage = $request->get('page', 1);
            $total = $transformedFaqs->count();
            $offset = ($currentPage - 1) * $perPage;
            $items = $transformedFaqs->slice($offset, $perPage)->values();

            // 페이지네이션 정보 생성
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return $paginator;

        } catch (\Exception $e) {
            Log::error("FAQ 데이터 조회 오류: " . $e->getMessage());
            // 빈 페이지네이터 반환
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }
    }
}
