<?php

namespace App\Services\Backoffice;

use App\Models\Inquiry;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InquiryService
{
    /**
     * 문의 목록 조회
     */
    public function getInquiries(Request $request)
    {
        $query = Inquiry::with(['user', 'projectTerm', 'course', 'operatingInstitution', 'projectPeriod', 'country']);
        
        // 필터링 적용
        $this->applyFilters($query, $request);
        
        // 목록 개수 설정
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 10;
        
        // 정렬: 최신순
        $inquiries = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        
        // 프로젝트 기수 표시 텍스트 추가
        $this->transformInquiriesForList($inquiries);
        
        return $inquiries;
    }

    /**
     * 필터링 적용
     */
    private function applyFilters($query, Request $request): void
    {
        // 등록일 필터
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 답변일 필터
        if ($request->filled('reply_start_date')) {
            $query->whereDate('replied_at', '>=', $request->reply_start_date);
        }

        if ($request->filled('reply_end_date')) {
            $query->whereDate('replied_at', '<=', $request->reply_end_date);
        }

        // 답변여부 필터
        if ($request->filled('reply_status')) {
            $query->where('reply_status', $request->reply_status);
        }

        // 프로젝트 기수 필터링
        $this->applyProjectTermFilter($query, $request, 'filter_project_term_id', 'project_term_id');
        $this->applyProjectTermFilter($query, $request, 'filter_course_id', 'course_id');
        $this->applyProjectTermFilter($query, $request, 'filter_operating_institution_id', 'operating_institution_id');
        $this->applyProjectTermFilter($query, $request, 'filter_project_period_id', 'project_period_id');
        $this->applyProjectTermFilter($query, $request, 'filter_country_id', 'country_id');

        // 키워드 검색
        if ($request->filled('keyword')) {
            $this->applyKeywordSearch($query, $request->keyword, $request->search_type);
        }
    }

    /**
     * 프로젝트 기수 관련 필터 적용
     */
    private function applyProjectTermFilter($query, Request $request, string $requestKey, string $columnKey): void
    {
        if (!$request->filled($requestKey)) {
            return;
        }
        
        $value = $request->get($requestKey);
        $query->where($columnKey, $value);
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
        } elseif ($searchType === 'author') {
            $query->whereHas('user', function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        } else {
            // 전체 검색
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function($userQuery) use ($keyword) {
                      $userQuery->where('name', 'like', "%{$keyword}%");
                  });
            });
        }
    }

    /**
     * 문의 목록 표시용 데이터 변환
     */
    private function transformInquiriesForList($inquiries): void
    {
        $inquiries->getCollection()->transform(function ($inquiry) {
            // 프로젝트 기수 표시 텍스트
            $inquiry->project_term_display_text = $this->getProjectTermDisplayText($inquiry);
            
            // 답변여부 한글 표시
            $inquiry->reply_status_text = $inquiry->reply_status === Inquiry::STATUS_COMPLETED ? '완료' : '미완료';
            
            return $inquiry;
        });
    }

    /**
     * 문의 상세 조회
     */
    public function getInquiry(int $id)
    {
        $inquiry = Inquiry::with([
            'user',
            'projectTerm',
            'course',
            'operatingInstitution',
            'projectPeriod',
            'country',
            'repliedByUser'
        ])->findOrFail($id);
        
        // 프로젝트 기수 표시 텍스트
        $inquiry->project_term_display_text = $this->getProjectTermDisplayText($inquiry);
        
        return $inquiry;
    }

    /**
     * 프로젝트 기수 표시 텍스트 생성
     */
    public function getProjectTermDisplayText($inquiry): string
    {
        $parts = [];

        // 기수
        if ($inquiry->projectTerm) {
            $parts[] = $inquiry->projectTerm->name;
        }

        // 과정
        if ($inquiry->course) {
            $courseName = $inquiry->course->name_ko;
            if ($inquiry->course->name_en) {
                $courseName .= ' / ' . $inquiry->course->name_en;
            }
            $parts[] = $courseName;
        }

        // 운영기관
        if ($inquiry->operatingInstitution) {
            $institutionName = $inquiry->operatingInstitution->name_ko;
            if ($inquiry->operatingInstitution->name_en) {
                $institutionName .= ' / ' . $inquiry->operatingInstitution->name_en;
            }
            $parts[] = $institutionName;
        }

        // 프로젝트기간
        if ($inquiry->projectPeriod) {
            $periodName = $inquiry->projectPeriod->name_ko;
            if ($inquiry->projectPeriod->name_en) {
                $periodName .= ' / ' . $inquiry->projectPeriod->name_en;
            }
            $parts[] = $periodName;
        }

        // 국가
        if ($inquiry->country) {
            $countryName = $inquiry->country->name_ko;
            if ($inquiry->country->name_en) {
                $countryName .= ' / ' . $inquiry->country->name_en;
            }
            $parts[] = $countryName;
        }

        return !empty($parts) ? implode(' / ', $parts) : '전체';
    }

    /**
     * 답변 저장/수정
     */
    public function replyInquiry(int $id, array $data): bool
    {
        $inquiry = Inquiry::findOrFail($id);
        
        // 답변 내용
        $inquiry->reply_content = $data['reply_content'] ?? null;
        
        // 답변 첨부파일
        if (isset($data['reply_attachments'])) {
            $inquiry->reply_attachments = $data['reply_attachments'];
        }
        
        // 답변여부
        $inquiry->reply_status = $data['reply_status'] ?? Inquiry::STATUS_PENDING;
        
        // 답변 완료 시 답변일 설정
        if ($inquiry->reply_status === Inquiry::STATUS_COMPLETED && !$inquiry->replied_at) {
            $inquiry->replied_at = now();
        }
        
        // 답변 작성자
        $inquiry->replied_by = Auth::id();
        
        return $inquiry->save();
    }

    /**
     * 첨부파일 처리
     */
    public function handleAttachments(Request $request, string $type = 'reply'): array
    {
        $attachments = [];
        $removeIndices = $request->input('remove_' . $type . '_attachments', []);
        
        // 기존 첨부파일 보존 (제거 요청이 없는 것만)
        if ($request->has('existing_' . $type . '_attachments')) {
            $existingAttachments = $request->input('existing_' . $type . '_attachments', []);
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
        $fileKey = $type . '_attachments';
        if ($request->hasFile($fileKey)) {
            foreach ($request->file($fileKey) as $file) {
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $file->store('uploads/inquiries/' . $type, 'public'),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        return $attachments;
    }

    /**
     * 문의 삭제
     */
    public function deleteInquiry(int $id): bool
    {
        $inquiry = Inquiry::findOrFail($id);
        
        // 첨부파일 삭제
        $this->deleteAttachments($inquiry->attachments);
        $this->deleteAttachments($inquiry->reply_attachments);
        
        return $inquiry->delete();
    }

    /**
     * 첨부파일 삭제
     */
    private function deleteAttachments(?array $attachments): void
    {
        if (!$attachments || !is_array($attachments)) {
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
}
