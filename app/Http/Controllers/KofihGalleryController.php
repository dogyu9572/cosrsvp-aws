<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KofihGalleryController extends Controller
{
    /**
     * 갤러리 목록 페이지
     */
    public function index(Request $request)
    {
        $galleries = $this->getGalleries($request);
        
        return view('kofih.gallery.index', compact('galleries'));
    }
    
    /**
     * 갤러리 상세 페이지
     */
    public function show($id)
    {
        $gallery = $this->getGalleryById($id);
        
        if (!$gallery) {
            abort(404, '갤러리를 찾을 수 없습니다.');
        }
        
        // 조회수 증가
        DB::table('board_gallerys')
            ->where('id', $id)
            ->increment('view_count');
        
        return view('kofih.gallery.show', compact('gallery'));
    }
    
    /**
     * 갤러리 목록 조회
     */
    private function getGalleries(Request $request)
    {
        try {
            $tableName = 'board_gallerys';
            
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("갤러리 테이블이 존재하지 않음: " . $tableName);
                return collect();
            }
            
            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'thumbnail', 'attachments', 'custom_fields', 'created_at')
                ->whereNull('deleted_at');
            
            // 검색 필터 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }
            
            // 정렬: 최신순
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
            
            $galleries = $query->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
            
            // 데이터 변환 (attachments 파싱, 한글/영문 필드 추출)
            $galleries->getCollection()->transform(function ($gallery) {
                $attachments = [];
                if ($gallery->attachments) {
                    $attachments = json_decode($gallery->attachments, true);
                    if (!is_array($attachments)) {
                        $attachments = [];
                    }
                }
                
                // custom_fields에서 한글/영문 제목/내용 추출
                $customFields = json_decode($gallery->custom_fields ?? '{}', true);
                if (!is_array($customFields)) {
                    $customFields = [];
                }
                
                $koreanTitle = $gallery->title;
                $koreanContent = $gallery->content;
                $englishTitle = $customFields['title_en'] ?? $gallery->title;
                $englishContent = $customFields['content_en'] ?? $gallery->content;
                
                return (object) [
                    'id' => $gallery->id,
                    'korean_title' => $koreanTitle,
                    'korean_content' => $koreanContent,
                    'english_title' => $englishTitle,
                    'english_content' => $englishContent,
                    'title' => $koreanTitle ?: $englishTitle,
                    'content' => $koreanContent ?: $englishContent,
                    'thumbnail' => $gallery->thumbnail,
                    'attachments' => $attachments,
                    'created_at' => $gallery->created_at,
                ];
            });
            
            return $galleries;
            
        } catch (\Exception $e) {
            Log::error("갤러리 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * 갤러리 상세 조회
     */
    private function getGalleryById($id)
    {
        try {
            $tableName = 'board_gallerys';
            
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                return null;
            }
            
            $gallery = DB::table($tableName)
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();
            
            if (!$gallery) {
                return null;
            }
            
            // 첨부파일 파싱
            $attachments = [];
            if ($gallery->attachments) {
                $attachments = json_decode($gallery->attachments, true);
                if (!is_array($attachments)) {
                    $attachments = [];
                }
            }
            
            // custom_fields에서 한글/영문 제목/내용 추출
            $customFields = json_decode($gallery->custom_fields ?? '{}', true);
            if (!is_array($customFields)) {
                $customFields = [];
            }
            
            $koreanTitle = $gallery->title;
            $koreanContent = $gallery->content;
            $englishTitle = $customFields['title_en'] ?? $gallery->title;
            $englishContent = $customFields['content_en'] ?? $gallery->content;
            
            return (object) [
                'id' => $gallery->id,
                'korean_title' => $koreanTitle,
                'korean_content' => $koreanContent,
                'english_title' => $englishTitle,
                'english_content' => $englishContent,
                'title' => $koreanTitle ?: $englishTitle,
                'content' => $koreanContent ?: $englishContent,
                'thumbnail' => $gallery->thumbnail,
                'attachments' => $attachments,
                'view_count' => $gallery->view_count,
                'created_at' => $gallery->created_at,
            ];
            
        } catch (\Exception $e) {
            Log::error("갤러리 상세 조회 오류: " . $e->getMessage());
            return null;
        }
    }
}
