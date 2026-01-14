<?php

namespace App\Services\Backoffice;

use App\Models\Board;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoardPostService
{
    /**
     * 동적 테이블명 생성
     */
    public function getTableName(string $slug): string
    {
        return 'board_' . $slug;
    }

    /**
     * 게시글 목록 조회
     */
    public function getPosts(string $slug, Request $request)
    {
        $query = DB::table($this->getTableName($slug));
        
        $this->applySearchFilters($query, $request, $slug);
        
        // 목록 개수 설정
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 15;
        
        // 정렬 기능이 활성화된 게시판인지 확인
        $board = Board::where('slug', $slug)->first();
        if ($board && $board->enable_sorting) {
            // 정렬 기능 활성화: sort_order 내림차순 (큰 값이 위), 공지글, 최신순
            $posts = $query->orderBy('sort_order', 'desc')
                ->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        } else {
            // 정렬 기능 비활성화: 공지글, 최신순
            $posts = $query->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();
        }

        $this->transformDates($posts);
        $this->transformPostsForList($posts, $slug);
        
        return $posts;
    }

    /**
     * 검색 필터 적용
     */
    private function applySearchFilters($query, Request $request, string $slug): void
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('keyword')) {
            $this->applyKeywordSearch($query, $request->keyword, $request->search_type);
        }
        
        // 프로젝트 기수 필터링 (JSON 필드 - project_term 내부의 값들)
        $this->applyProjectTermFilter($query, $request, 'filter_project_term_id', 'project_term_id');
        $this->applyProjectTermFilter($query, $request, 'filter_course_id', 'course_id');
        $this->applyProjectTermFilter($query, $request, 'filter_operating_institution_id', 'operating_institution_id');
        $this->applyProjectTermFilter($query, $request, 'filter_project_period_id', 'project_period_id');
        $this->applyProjectTermFilter($query, $request, 'filter_country_id', 'country_id');
    }

    /**
     * 프로젝트 기수 관련 필터 적용 (공통 메서드)
     */
    private function applyProjectTermFilter($query, Request $request, string $requestKey, string $jsonKey): void
    {
        if (!$request->filled($requestKey)) {
            return;
        }
        
        $value = $request->get($requestKey);
        
        $query->where(function($q) use ($value, $jsonKey) {
            $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"' . $jsonKey . '":' . $value . '%'])
              ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$." . $jsonKey . "') = ?", [$value])
              ->orWhereRaw("custom_fields LIKE ?", ['%"' . $jsonKey . '":' . $value . '%'])
              ->orWhereRaw("custom_fields LIKE ?", ['%"' . $jsonKey . '":"' . $value . '"%']);
        });
    }

    /**
     * 키워드 검색 적용
     */
    private function applyKeywordSearch($query, string $keyword, ?string $searchType): void
    {
        if ($searchType === 'title') {
            $query->where('title', 'like', "%{$keyword}%");
        } elseif ($searchType === 'content') {
            $query->where('content', 'like', "%{$keyword}%");
        } else {
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
            });
        }
    }

    /**
     * 날짜 변환
     */
    private function transformDates($posts): void
    {
        $posts->getCollection()->transform(function ($post) {
            foreach (['created_at', 'updated_at'] as $dateField) {
                if (isset($post->$dateField) && is_string($post->$dateField)) {
                    $post->$dateField = Carbon::parse($post->$dateField);
                }
            }
            return $post;
        });
    }

    /**
     * 게시글 목록 표시용 데이터 변환 (띠공지 전용)
     */
    private function transformPostsForList($posts, string $slug): void
    {
        $posts->getCollection()->transform(function ($post) use ($slug) {
            // 프로젝트 기수 텍스트 (top-notices, notices 공통)
            $post->project_term_display_text = $this->getProjectTermDisplayText($post->custom_fields ?? null);
            
            // top-notices 게시판인 경우
            if ($slug === 'top-notices') {
                // 표출일자 텍스트
                $post->display_date_text = $this->getDisplayDateText($post->custom_fields ?? null);
                
                // 내용 미리보기 (HTML 태그 제거, 100자 제한)
                $contentText = strip_tags($post->content ?? '');
                $post->content_preview = mb_strlen($contentText) > 100 
                    ? mb_substr($contentText, 0, 100) . '...' 
                    : $contentText;
            }
            
            // notices 게시판인 경우
            if ($slug === 'notices') {
                // 학생 정보 텍스트
                $post->students_display_text = $this->getStudentsDisplayText($post->custom_fields ?? null);
            }
            
            return $post;
        });
    }

    /**
     * 게시글 저장
     */
    public function storePost(string $slug, array $validated, Request $request, $board): int
    {
        $data = $this->preparePostData($validated, $request, $slug, $board);
        
        return DB::table($this->getTableName($slug))->insertGetId($data);
    }

    /**
     * 게시글 데이터 준비
     */
    private function preparePostData(array $validated, Request $request, string $slug, $board): array
    {
        // 정렬 기능 활성화된 게시판인 경우 자동으로 sort_order 설정
        $sortOrder = 0;
        if ($board && $board->enable_sorting) {
            $sortOrder = $this->getNextSortOrder($slug);
        }
        
        // is_active 필드 처리: 필드가 활성화되어 있고 요청에 있으면 사용, 없으면 기본값 true
        $isActive = true;
        if ($board && $board->isFieldEnabled('is_active')) {
            $isActive = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        }

        return [
            'user_id' => null,
            'author_name' => $validated['author_name'] ?? '관리자',
            'title' => $validated['title'],
            'content' => $this->sanitizeContent($validated['content']),
            'category' => $validated['category'] ?? null,
            'is_notice' => $request->has('is_notice'),
            'is_secret' => $request->has('is_secret'),
            'is_active' => $isActive,
            'password' => $validated['password'] ?? null,
            'thumbnail' => $this->handleThumbnail($request, $slug),
            'attachments' => json_encode($this->handleAttachments($request, $slug)),
            'custom_fields' => $this->getCustomFieldsJson($request, $board),
            'view_count' => 0,
            'sort_order' => $sortOrder,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * 다음 정렬 순서 값을 계산합니다 (외부에서 호출 가능)
     */
    public function calculateNextSortOrder(string $slug): int
    {
        $maxSortOrder = DB::table($this->getTableName($slug))
            ->max('sort_order');
        
        return ($maxSortOrder ?? 0) + 1;
    }

    /**
     * 다음 정렬 순서 값을 가져옵니다 (정렬 기능 활성화된 게시판에만 사용)
     */
    private function getNextSortOrder(string $slug): int
    {
        return $this->calculateNextSortOrder($slug);
    }

    /**
     * HTML 내용 정리
     */
    private function sanitizeContent(string $content): string
    {
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><table><thead><tbody><tr><td><th><a><img><div><span><iframe><video><source>';
        return strip_tags($content, $allowedTags);
    }

    /**
     * 썸네일 처리
     */
    private function handleThumbnail(Request $request, string $slug): ?string
    {
        // 썸네일 제거 요청이 있는 경우
        if ($request->has('remove_thumbnail')) {
            return null;
        }
        
        // 새 썸네일이 업로드된 경우
        if ($request->hasFile('thumbnail')) {
            return $request->file('thumbnail')->store('thumbnails/' . $slug, 'public');
        }
        
        // 기존 썸네일이 있는 경우 보존
        if ($request->has('existing_thumbnail')) {
            return $request->input('existing_thumbnail');
        }
        
        return null;
    }

    /**
     * 첨부파일 처리
     */
    private function handleAttachments(Request $request, string $slug): array
    {
        $attachments = [];
        $removeIndices = $request->input('remove_attachments', []);
        
        // 기존 첨부파일 보존 (제거 요청이 없는 것만)
        if ($request->has('existing_attachments')) {
            $existingAttachments = $request->input('existing_attachments', []);
            foreach ($existingAttachments as $index => $attachment) {
                // 제거 요청이 있는 인덱스는 제외
                if (in_array($index, $removeIndices)) {
                    continue;
                }
                
                if (is_string($attachment)) {
                    $attachment = json_decode($attachment, true);
                }
                if (is_array($attachment) && isset($attachment['name'], $attachment['path'])) {
                    $attachments[] = $attachment;
                }
            }
        }
        
        // 새 첨부파일 추가
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $file->store('uploads/' . $slug, 'public'),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        return $attachments;
    }

    /**
     * 커스텀 필드 JSON 생성
     */
    private function getCustomFieldsJson(Request $request, $board): ?string
    {
        $customFields = $this->processCustomFields($request, $board);
        return !empty($customFields) ? json_encode($customFields) : null;
    }

    /**
     * 커스텀 필드 처리
     */
    private function processCustomFields(Request $request, $board): array
    {
        $customFields = [];
        
        // notices 게시판인 경우 영문 필드 자동 처리
        $boardSlug = $board->slug ?? '';
        if ($boardSlug === 'notices') {
            if ($request->has('custom_field_title_en')) {
                $customFields['title_en'] = $request->input('custom_field_title_en');
            }
            if ($request->has('custom_field_content_en')) {
                $customFields['content_en'] = $request->input('custom_field_content_en');
            }
        }
        
        // 기존 커스텀 필드 처리
        if ($board->custom_fields_config && is_array($board->custom_fields_config)) {
            foreach ($board->custom_fields_config as $fieldConfig) {
                $fieldName = $fieldConfig['name'];
                $customFields[$fieldName] = $request->input("custom_field_{$fieldName}");
            }
        }
        
        return $customFields;
    }

    /**
     * 게시글 조회
     */
    public function getPost(string $slug, int $postId)
    {
        $post = DB::table($this->getTableName($slug))->where('id', $postId)->first();
        
        if (!$post) {
            return null;
        }

        $this->transformSinglePostDates($post);
        return $post;
    }

    /**
     * 단일 게시글 날짜 변환
     */
    private function transformSinglePostDates($post): void
    {
        foreach (['created_at', 'updated_at'] as $dateField) {
            if (isset($post->$dateField) && is_string($post->$dateField)) {
                $post->$dateField = Carbon::parse($post->$dateField);
            }
        }
    }

    /**
     * 게시글 수정
     */
    public function updatePost(string $slug, int $postId, array $validated, Request $request, $board): bool
    {
        // 기존 게시물 조회
        $existingPost = $this->getPost($slug, $postId);
        
        if (!$existingPost) {
            return false;
        }
        
        $data = $this->prepareUpdateData($validated, $request, $slug, $board, $existingPost);
        
        // update()는 영향받은 행 수를 반환하므로, 게시글이 존재하면 성공으로 처리
        DB::table($this->getTableName($slug))
            ->where('id', $postId)
            ->update($data);
        
        return true;
    }

    /**
     * 수정 데이터 준비
     */
    private function prepareUpdateData(array $validated, Request $request, string $slug, $board, $existingPost = null): array
    {
        // is_active 필드 처리
        // 필드가 활성화되어 있고 요청에 있으면 사용
        // 필드가 비활성화되어 있으면 기존 값 유지 (수정 시)
        // 필드가 활성화되어 있지만 요청에 없으면 기본값 true
        $isActive = true;
        if ($board && $board->isFieldEnabled('is_active')) {
            $isActive = $request->has('is_active') ? (bool)$request->input('is_active') : true;
        } elseif ($existingPost && isset($existingPost->is_active)) {
            // 필드가 비활성화되어 있으면 기존 값 유지
            $isActive = (bool)$existingPost->is_active;
        }

        // author_name 필드 처리
        // null이거나 빈 문자열일 경우 기존 값 유지 (수정 시)
        $authorName = $validated['author_name'] ?? null;
        if (empty($authorName) && $existingPost && isset($existingPost->author_name)) {
            $authorName = $existingPost->author_name;
        }

        // password 필드 처리
        // 비밀번호가 변경되지 않았을 경우 기존 값 유지 (수정 시)
        $password = $validated['password'] ?? null;
        if (empty($password) && $existingPost && isset($existingPost->password)) {
            $password = $existingPost->password;
        }

        return [
            'title' => $validated['title'],
            'content' => $this->sanitizeContent($validated['content']),
            'category' => $validated['category'] ?? null,
            'is_notice' => $request->has('is_notice'),
            'is_secret' => $request->has('is_secret'),
            'is_active' => $isActive,
            'author_name' => $authorName,
            'password' => $password,
            'thumbnail' => $this->handleThumbnail($request, $slug),
            'attachments' => json_encode($this->handleAttachments($request, $slug)),
            'custom_fields' => $this->getCustomFieldsJson($request, $board),
            'sort_order' => $request->input('sort_order', 0),
            'updated_at' => now()
        ];
    }

    /**
     * 게시글 삭제
     */
    public function deletePost(string $slug, int $postId): bool
    {
        $post = DB::table($this->getTableName($slug))->where('id', $postId)->first();
        
        if (!$post) {
            return false;
        }

        $this->deleteAttachments($post);
        
        return DB::table($this->getTableName($slug))->where('id', $postId)->delete();
    }

    /**
     * 첨부파일 삭제
     */
    private function deleteAttachments($post): void
    {
        if (!$post->attachments) {
            return;
        }

        $attachments = json_decode($post->attachments, true);
        if (!is_array($attachments)) {
            return;
        }

        foreach ($attachments as $attachment) {
            if (isset($attachment['path'])) {
                $filePath = storage_path('app/public/' . $attachment['path']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }

    /**
     * 일괄 삭제
     */
    public function bulkDelete(string $slug, array $postIds): int
    {
        return DB::table($this->getTableName($slug))->whereIn('id', $postIds)->delete();
    }

    /**
     * 프로젝트 기수 정보 표시 텍스트 생성
     */
    public function getProjectTermDisplayText(?string $customFieldsJson): string
    {
        if (!$customFieldsJson) {
            return '전체';
        }

        $customFields = json_decode($customFieldsJson, true);
        if (!is_array($customFields)) {
            return '전체';
        }

        // project_term 필드에서 데이터 가져오기 (JSON 문자열일 수 있음)
        $projectTermData = null;
        if (isset($customFields['project_term'])) {
            $projectTermValue = $customFields['project_term'];
            if (is_string($projectTermValue)) {
                $projectTermData = json_decode($projectTermValue, true);
            } elseif (is_array($projectTermValue)) {
                $projectTermData = $projectTermValue;
            }
        }

        // project_term 필드가 없으면 직접 customFields에서 찾기
        if (!$projectTermData) {
            $projectTermData = $customFields;
        }

        if (!is_array($projectTermData)) {
            return '전체';
        }

        $parts = [];

        // 기수
        if (!empty($projectTermData['project_term_id'])) {
            $term = ProjectTerm::find($projectTermData['project_term_id']);
            if ($term) {
                $parts[] = $term->name;
            }
        }

        // 과정
        if (!empty($projectTermData['course_id'])) {
            $course = Course::find($projectTermData['course_id']);
            if ($course) {
                $courseName = $course->name_ko;
                if ($course->name_en) {
                    $courseName .= ' / ' . $course->name_en;
                }
                $parts[] = $courseName;
            }
        }

        // 운영기관
        if (!empty($projectTermData['operating_institution_id'])) {
            $institution = OperatingInstitution::find($projectTermData['operating_institution_id']);
            if ($institution) {
                $institutionName = $institution->name_ko;
                if ($institution->name_en) {
                    $institutionName .= ' / ' . $institution->name_en;
                }
                $parts[] = $institutionName;
            }
        }

        // 프로젝트기간
        if (!empty($projectTermData['project_period_id'])) {
            $period = ProjectPeriod::find($projectTermData['project_period_id']);
            if ($period) {
                $periodName = $period->name_ko;
                if ($period->name_en) {
                    $periodName .= ' / ' . $period->name_en;
                }
                $parts[] = $periodName;
            }
        }

        // 국가
        if (!empty($projectTermData['country_id'])) {
            $country = Country::find($projectTermData['country_id']);
            if ($country) {
                $countryName = $country->name_ko;
                if ($country->name_en) {
                    $countryName .= ' / ' . $country->name_en;
                }
                $parts[] = $countryName;
            }
        }

        return !empty($parts) ? implode(' / ', $parts) : '전체';
    }

    /**
     * 표출일자 표시 텍스트 생성
     */
    public function getDisplayDateText(?string $customFieldsJson): string
    {
        if (!$customFieldsJson) {
            return '';
        }

        $customFields = json_decode($customFieldsJson, true);
        if (!is_array($customFields) || empty($customFields['display_date'])) {
            return '';
        }

        $displayDateData = is_string($customFields['display_date']) 
            ? json_decode($customFields['display_date'], true) 
            : $customFields['display_date'];

        if (!is_array($displayDateData) || empty($displayDateData['use_display_date'])) {
            return '';
        }

        $startDate = $displayDateData['start_date'] ?? '';
        $endDate = $displayDateData['end_date'] ?? '';

        if (empty($startDate) && empty($endDate)) {
            return '';
        }

        if (!empty($startDate) && !empty($endDate)) {
            try {
                $start = Carbon::parse($startDate)->format('Y.m.d');
                $end = Carbon::parse($endDate)->format('Y.m.d');
                return $start . ' ~ ' . $end;
            } catch (\Exception $e) {
                return '';
            }
        }

        if (!empty($startDate)) {
            try {
                return Carbon::parse($startDate)->format('Y.m.d');
            } catch (\Exception $e) {
                return '';
            }
        }

        if (!empty($endDate)) {
            try {
                return Carbon::parse($endDate)->format('Y.m.d');
            } catch (\Exception $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * 학생 정보 표시 텍스트 생성
     */
    private function getStudentsDisplayText(?string $customFieldsJson): string
    {
        if (empty($customFieldsJson)) {
            return '전체';
        }

        $customFields = json_decode($customFieldsJson, true);
        if (!is_array($customFields) || empty($customFields['students'])) {
            return '전체';
        }

        $studentsData = is_string($customFields['students']) 
            ? json_decode($customFields['students'], true) 
            : $customFields['students'];

        if (!is_array($studentsData) || empty($studentsData['student_ids'])) {
            return '전체';
        }

        $studentIds = $studentsData['student_ids'];
        if (empty($studentIds) || !is_array($studentIds)) {
            return '전체';
        }

        // Student 모델이 있는지 확인하고 학생 이름 가져오기
        try {
            if (class_exists(\App\Models\Student::class)) {
                $studentModel = new \App\Models\Student();
                $students = $studentModel::whereIn('id', $studentIds)->get();
                if ($students->isEmpty()) {
                    return '전체';
                }
                return $students->pluck('name')->join(', ');
            }
        } catch (\Exception $e) {
            // Student 모델이 없거나 오류 발생 시 ID만 표시
        }

        // Student 모델이 없으면 ID만 표시
        return implode(', ', $studentIds);
    }
}
