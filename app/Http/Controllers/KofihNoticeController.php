<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KofihNoticeController extends Controller
{
    /**
     * 공지사항 목록 페이지
     */
    public function index(Request $request)
    {
        $notices = $this->getNotices($request);
        
        return view('kofih.notices.index', compact('notices'));
    }
    
    /**
     * 공지사항 상세 페이지
     */
    public function show($id)
    {
        $notice = $this->getNoticeById($id);
        
        if (!$notice) {
            abort(404, '공지사항을 찾을 수 없습니다.');
        }
        
        // 조회수 증가
        DB::table('board_notices')
            ->where('id', $id)
            ->increment('view_count');
        
        // 이전/다음 게시글 조회
        $allNotices = DB::table('board_notices')
            ->select('id', 'title', 'custom_fields', 'is_notice', 'created_at')
            ->whereNull('deleted_at')
            ->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        $currentIndex = $allNotices->search(function ($item) use ($id) {
            return $item->id == $id;
        });
        
        $prevNotice = null;
        $nextNotice = null;
        
        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $prevNotice = $allNotices[$currentIndex - 1];
            }
            if ($currentIndex < $allNotices->count() - 1) {
                $nextNotice = $allNotices[$currentIndex + 1];
            }
        }
        
        return view('kofih.notices.show', compact('notice', 'prevNotice', 'nextNotice'));
    }
    
    /**
     * 공지사항 목록 조회
     */
    private function getNotices(Request $request)
    {
        try {
            $tableName = 'board_notices';
            
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("공지사항 테이블이 존재하지 않음: " . $tableName);
                return collect();
            }
            
            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'author_name', 'attachments', 'view_count', 'is_notice', 'created_at', 'custom_fields')
                ->whereNull('deleted_at');
            
            // 검색 필터 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }
            
            // 정렬: 공지글 우선, 최신순
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
            
            $notices = $query->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
            
            // 데이터 변환 (attachments 파싱, 한글/영문 필드 추출)
            $notices->getCollection()->transform(function ($notice) {
                $attachments = [];
                if ($notice->attachments) {
                    $attachments = json_decode($notice->attachments, true);
                    if (!is_array($attachments)) {
                        $attachments = [];
                    }
                }
                
                // custom_fields에서 한글/영문 제목/내용 추출
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                if (!is_array($customFields)) {
                    $customFields = [];
                }
                
                $koreanTitle = $notice->title;
                $koreanContent = $notice->content;
                $englishTitle = $customFields['title_en'] ?? $notice->title;
                $englishContent = $customFields['content_en'] ?? $notice->content;
                
                return (object) [
                    'id' => $notice->id,
                    'korean_title' => $koreanTitle,
                    'korean_content' => $koreanContent,
                    'english_title' => $englishTitle,
                    'english_content' => $englishContent,
                    'title' => $koreanTitle ?: $englishTitle,
                    'content' => $koreanContent ?: $englishContent,
                    'author_name' => $notice->author_name,
                    'attachments' => $attachments,
                    'view_count' => $notice->view_count,
                    'is_notice' => $notice->is_notice,
                    'created_at' => $notice->created_at,
                ];
            });
            
            // 공지글 개수 계산 (번호 표시용)
            $totalNormal = DB::table($tableName)
                ->whereNull('deleted_at')
                ->where('is_notice', false);
            
            // 검색 필터가 있으면 동일하게 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $totalNormal->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }
            
            $totalNormalCount = $totalNormal->count();
            
            // Paginator에 동적 속성 추가
            $notices->totalNormal = $totalNormalCount;
            
            return $notices;
            
        } catch (\Exception $e) {
            Log::error("공지사항 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * 공지사항 상세 조회
     */
    private function getNoticeById($id)
    {
        try {
            $tableName = 'board_notices';
            
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                return null;
            }
            
            $notice = DB::table($tableName)
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();
            
            if (!$notice) {
                return null;
            }
            
            // 첨부파일 파싱
            $attachments = [];
            if ($notice->attachments) {
                $attachments = json_decode($notice->attachments, true);
                if (!is_array($attachments)) {
                    $attachments = [];
                }
            }
            
            // custom_fields에서 한글/영문 제목/내용 추출
            $customFields = json_decode($notice->custom_fields ?? '{}', true);
            if (!is_array($customFields)) {
                $customFields = [];
            }
            
            $koreanTitle = $notice->title;
            $koreanContent = $notice->content;
            $englishTitle = $customFields['title_en'] ?? $notice->title;
            $englishContent = $customFields['content_en'] ?? $notice->content;
            
            return (object) [
                'id' => $notice->id,
                'korean_title' => $koreanTitle,
                'korean_content' => $koreanContent,
                'english_title' => $englishTitle,
                'english_content' => $englishContent,
                'title' => $koreanTitle ?: $englishTitle,
                'content' => $koreanContent ?: $englishContent,
                'author_name' => $notice->author_name,
                'attachments' => $attachments,
                'view_count' => $notice->view_count,
                'is_notice' => $notice->is_notice,
                'created_at' => $notice->created_at,
            ];
            
        } catch (\Exception $e) {
            Log::error("공지사항 상세 조회 오류: " . $e->getMessage());
            return null;
        }
    }
}
