<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    /**
     * 갤러리 목록 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "05";
        $gName = "Gallery";
        $sName = "";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 갤러리 데이터 조회
        $galleries = $this->getGalleriesByProjectTerm($memberProjectInfo, $request);

        return view('gallery.index', compact('gNum', 'gName', 'sName', 'galleries', 'memberProjectInfo'));
    }

    /**
     * 갤러리 상세 페이지
     */
    public function show($id)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "05";
        $gName = "Gallery";
        $sName = "";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 갤러리 상세 조회
        $gallery = $this->getGalleryById($id, $memberProjectInfo);

        if (!$gallery) {
            abort(404, '갤러리를 찾을 수 없습니다.');
        }

        // 조회수 증가
        DB::table('board_gallerys')
            ->where('id', $id)
            ->increment('view_count');

        return view('gallery.show', compact('gNum', 'gName', 'sName', 'gallery', 'memberProjectInfo'));
    }

    /**
     * 프로젝트 기수에 해당하는 갤러리 조회
     */
    private function getGalleriesByProjectTerm($memberProjectInfo, Request $request)
    {
        try {
            $tableName = 'board_gallerys';

            // 테이블 존재 여부 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("갤러리 테이블이 존재하지 않음: " . $tableName);
                return collect();
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'thumbnail', 'attachments', 'custom_fields', 'created_at')
                ->whereNull('deleted_at');

            // 프로젝트 관련 정보 필터링 (모든 필드 일치해야 함)
            if ($memberProjectInfo['project_term_id']) {
                $query->where(function($q) use ($memberProjectInfo) {
                    $projectTermId = $memberProjectInfo['project_term_id'];
                    $projectTermIdStr = (string)$projectTermId;
                    
                    // project_term_id 필터링
                    $q->where(function($subQ) use ($projectTermId, $projectTermIdStr) {
                        $subQ->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_term_id":' . $projectTermId . '%'])
                            ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_term_id":"' . $projectTermIdStr . '"%'])
                            ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_term_id') = ?", [$projectTermId])
                            ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_term_id') = ?", [$projectTermIdStr])
                            ->orWhereRaw("custom_fields LIKE ?", ['%"project_term_id":' . $projectTermId . '%'])
                            ->orWhereRaw("custom_fields LIKE ?", ['%"project_term_id":"' . $projectTermIdStr . '"%']);
                    });
                });
            }

            // 검색 필터 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }

            // 정렬: 최신순
            $galleries = $query->orderBy('created_at', 'desc')
                ->get();

            // 필터링 후 추가 검증: PHP 레벨에서 모든 프로젝트 관련 필드가 일치하는 것만 필터링
            $filteredGalleries = $galleries->filter(function ($gallery) use ($memberProjectInfo) {
                $customFields = json_decode($gallery->custom_fields ?? '{}', true);
                
                if (!is_array($customFields)) {
                    return false;
                }
                
                // project_term이 JSON 문자열로 저장된 경우 파싱
                if (isset($customFields['project_term']) && is_string($customFields['project_term'])) {
                    $projectTermParsed = json_decode($customFields['project_term'], true);
                    if (is_array($projectTermParsed)) {
                        $customFields['project_term'] = $projectTermParsed;
                    }
                }
                
                // project_term 데이터 추출
                $galleryProjectInfo = [];
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $galleryProjectInfo = [
                            'project_term_id' => $projectTermData['project_term_id'] ?? null,
                            'course_id' => $projectTermData['course_id'] ?? null,
                            'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                            'project_period_id' => $projectTermData['project_period_id'] ?? null,
                            'country_id' => $projectTermData['country_id'] ?? null,
                        ];
                    }
                }
                
                // 모든 필드가 일치하는지 확인
                $matches = true;
                foreach ($memberProjectInfo as $key => $memberValue) {
                    if ($memberValue === null) {
                        continue; // 회원 정보가 없으면 비교하지 않음
                    }
                    
                    $galleryValue = $galleryProjectInfo[$key] ?? null;
                    if ($galleryValue === null) {
                        $matches = false;
                        break;
                    }
                    
                    // 숫자/문자열 모두 비교
                    if (($galleryValue != $memberValue) && ((string)$galleryValue !== (string)$memberValue)) {
                        $matches = false;
                        break;
                    }
                }
                
                return $matches;
            });

            // 데이터 변환 (영문 필드 추출)
            $transformedGalleries = $filteredGalleries->map(function ($gallery) {
                $attachments = [];
                if ($gallery->attachments) {
                    $attachments = json_decode($gallery->attachments, true);
                    if (!is_array($attachments)) {
                        $attachments = [];
                    }
                }

                // custom_fields에서 영문 제목/내용 추출
                $customFields = json_decode($gallery->custom_fields ?? '{}', true);
                if (!is_array($customFields)) {
                    $customFields = [];
                }
                $englishTitle = $customFields['title_en'] ?? $gallery->title;
                $englishContent = $customFields['content_en'] ?? $gallery->content;

                return (object) [
                    'id' => $gallery->id,
                    'title' => $englishTitle,
                    'content' => $englishContent,
                    'thumbnail' => $gallery->thumbnail,
                    'attachments' => $attachments,
                    'created_at' => $gallery->created_at,
                ];
            });

            // 페이지네이션 처리
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
            
            $currentPage = $request->get('page', 1);
            $total = $transformedGalleries->count();
            $offset = ($currentPage - 1) * $perPage;
            $items = $transformedGalleries->slice($offset, $perPage)->values();

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
            Log::error("갤러리 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * 갤러리 상세 조회
     */
    private function getGalleryById($id, $memberProjectInfo)
    {
        try {
            $tableName = 'board_gallerys';

            // 테이블 존재 여부 확인
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

            // 프로젝트 기수 필터링 확인
            $customFields = json_decode($gallery->custom_fields ?? '{}', true);
            
            if (!is_array($customFields)) {
                return null;
            }
            
            // project_term이 JSON 문자열로 저장된 경우 파싱
            if (isset($customFields['project_term']) && is_string($customFields['project_term'])) {
                $projectTermParsed = json_decode($customFields['project_term'], true);
                if (is_array($projectTermParsed)) {
                    $customFields['project_term'] = $projectTermParsed;
                }
            }
            
            // project_term 데이터 추출
            $galleryProjectInfo = [];
            if (isset($customFields['project_term'])) {
                $projectTermData = $customFields['project_term'];
                if (is_string($projectTermData)) {
                    $projectTermData = json_decode($projectTermData, true);
                }
                if (is_array($projectTermData)) {
                    $galleryProjectInfo = [
                        'project_term_id' => $projectTermData['project_term_id'] ?? null,
                        'course_id' => $projectTermData['course_id'] ?? null,
                        'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                        'project_period_id' => $projectTermData['project_period_id'] ?? null,
                        'country_id' => $projectTermData['country_id'] ?? null,
                    ];
                }
            }
            
            // 모든 필드가 일치하는지 확인
            foreach ($memberProjectInfo as $key => $memberValue) {
                if ($memberValue === null) {
                    continue; // 회원 정보가 없으면 비교하지 않음
                }
                
                $galleryValue = $galleryProjectInfo[$key] ?? null;
                if ($galleryValue === null) {
                    return null; // 일치하지 않으면 null 반환
                }
                
                // 숫자/문자열 모두 비교
                if (($galleryValue != $memberValue) && ((string)$galleryValue !== (string)$memberValue)) {
                    return null; // 일치하지 않으면 null 반환
                }
            }

            // 첨부파일 파싱
            $attachments = [];
            if ($gallery->attachments) {
                $attachments = json_decode($gallery->attachments, true);
                if (!is_array($attachments)) {
                    $attachments = [];
                }
            }

            // custom_fields에서 영문 제목/내용 추출
            $customFields = json_decode($gallery->custom_fields ?? '{}', true);
            if (!is_array($customFields)) {
                $customFields = [];
            }
            $englishTitle = $customFields['title_en'] ?? $gallery->title;
            $englishContent = $customFields['content_en'] ?? $gallery->content;

            return (object) [
                'id' => $gallery->id,
                'title' => $englishTitle,
                'content' => $englishContent,
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
