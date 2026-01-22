<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoticeController extends Controller
{
    /**
     * 공지사항 목록 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "04";
        $gName = "Notice";
        $sName = "";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 로그인한 회원 ID
        $memberId = $member['id'] ?? null;

        // 공지사항 데이터 조회
        $notices = $this->getNoticesByProjectTerm($memberProjectInfo, $request, $memberId);

        return view('notice.index', compact('gNum', 'gName', 'sName', 'notices', 'memberProjectInfo'));
    }

    /**
     * 공지사항 상세 페이지
     */
    public function show($id)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "04";
        $gName = "Notice";
        $sName = "";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 로그인한 회원 ID
        $memberId = $member['id'] ?? null;

        // 공지사항 상세 조회
        $notice = $this->getNoticeById($id, $memberProjectInfo, $memberId);

        if (!$notice) {
            abort(404, '공지사항을 찾을 수 없습니다.');
        }

        // 조회수 증가
        DB::table('board_notices')
            ->where('id', $id)
            ->increment('view_count');

        // 이전/다음 게시글 조회
        $prevNext = $this->getPrevNextNotices($id, $memberProjectInfo, $memberId);

        return view('notice.show', compact('gNum', 'gName', 'sName', 'notice', 'memberProjectInfo', 'prevNext'));
    }

    /**
     * 프로젝트 기수에 해당하는 공지사항 조회
     */
    private function getNoticesByProjectTerm($memberProjectInfo, Request $request, $memberId = null)
    {
        try {
            $tableName = 'board_notices';

            // 테이블 존재 여부 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("공지사항 테이블이 존재하지 않음: " . $tableName);
                return collect();
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'author_name', 'attachments', 'view_count', 'is_notice', 'created_at', 'custom_fields')
                ->whereNull('deleted_at');

            // 프로젝트 기수 필터링은 PHP 레벨에서 처리 (학생 체크가 있으면 프로젝트 기수 조건 무시)

            // 검색 필터 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }

            // 정렬: 공지글 우선, 최신순
            $notices = $query->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // 필터링 후 추가 검증: students 필드의 student_ids에 체크된 학생만 표시
            $filteredNotices = $notices->filter(function ($notice) use ($memberProjectInfo, $memberId) {
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                
                Log::info("공지사항 필터링 시작", [
                    'notice_id' => $notice->id,
                    'member_id' => $memberId,
                    'custom_fields_raw' => $notice->custom_fields,
                    'custom_fields_parsed' => $customFields
                ]);
                
                if (!is_array($customFields)) {
                    Log::warning("custom_fields 파싱 실패", ['notice_id' => $notice->id]);
                    return false;
                }
                
                // students 필드 확인
                if (isset($customFields['students'])) {
                    $studentsData = is_string($customFields['students']) 
                        ? json_decode($customFields['students'], true) 
                        : $customFields['students'];
                    
                    if (is_array($studentsData) && isset($studentsData['student_ids'])) {
                        $studentIds = is_array($studentsData['student_ids']) ? $studentsData['student_ids'] : [];
                        
                        // student_ids가 비어있으면 표시하지 않음
                        if (empty($studentIds)) {
                            return false;
                        }
                        
                        // memberId가 student_ids에 포함되면 표시
                        if ($memberId && in_array((int)$memberId, array_map('intval', $studentIds))) {
                            return true;
                        }
                        
                        // 포함되지 않으면 표시하지 않음
                        return false;
                    }
                }
                
                // students 필드가 없으면 프로젝트 기수 조건 확인
                
                // project_term이 JSON 문자열로 저장된 경우 파싱
                if (isset($customFields['project_term']) && is_string($customFields['project_term'])) {
                    $projectTermParsed = json_decode($customFields['project_term'], true);
                    if (is_array($projectTermParsed)) {
                        $customFields['project_term'] = $projectTermParsed;
                    }
                }
                
                // project_term 데이터 추출
                $noticeProjectInfo = [];
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $noticeProjectInfo = [
                            'project_term_id' => $projectTermData['project_term_id'] ?? null,
                            'course_id' => $projectTermData['course_id'] ?? null,
                            'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                            'project_period_id' => $projectTermData['project_period_id'] ?? null,
                            'country_id' => $projectTermData['country_id'] ?? null,
                        ];
                    }
                }
                
                Log::info("프로젝트 기수 비교", [
                    'notice_id' => $notice->id,
                    'notice_project_info' => $noticeProjectInfo,
                    'member_project_info' => $memberProjectInfo
                ]);
                
                // 모든 필드가 일치하는지 확인
                $matches = true;
                foreach ($memberProjectInfo as $key => $memberValue) {
                    if ($memberValue === null) {
                        continue; // 회원 정보가 없으면 비교하지 않음
                    }
                    
                    $noticeValue = $noticeProjectInfo[$key] ?? null;
                    if ($noticeValue === null) {
                        $matches = false;
                        break;
                    }
                    
                    // 숫자/문자열 모두 비교
                    if (($noticeValue != $memberValue) && ((string)$noticeValue !== (string)$memberValue)) {
                        $matches = false;
                        break;
                    }
                }
                
                Log::info("프로젝트 기수 비교 결과", [
                    'notice_id' => $notice->id,
                    'matches' => $matches
                ]);
                
                return $matches;
            });

            // 데이터 변환 (attachments 파싱, 영문 필드 추출)
            $transformedNotices = $filteredNotices->map(function ($notice) {
                $attachments = [];
                if ($notice->attachments) {
                    $attachments = json_decode($notice->attachments, true);
                    if (!is_array($attachments)) {
                        $attachments = [];
                    }
                }

                // custom_fields에서 영문 제목/내용 추출
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                if (!is_array($customFields)) {
                    $customFields = [];
                }
                $englishTitle = $customFields['title_en'] ?? $notice->title;
                $englishContent = $customFields['content_en'] ?? $notice->content;

                return (object) [
                    'id' => $notice->id,
                    'title' => $englishTitle,
                    'content' => $englishContent,
                    'author_name' => $notice->author_name,
                    'attachments' => $attachments,
                    'view_count' => $notice->view_count,
                    'is_notice' => $notice->is_notice,
                    'created_at' => $notice->created_at,
                ];
            });

            // 페이지네이션 처리
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;
            
            $currentPage = $request->get('page', 1);
            $total = $transformedNotices->count();
            $offset = ($currentPage - 1) * $perPage;
            $items = $transformedNotices->slice($offset, $perPage)->values();

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
            Log::error("공지사항 데이터 조회 오류: " . $e->getMessage());
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

    /**
     * 공지사항 상세 조회
     */
    private function getNoticeById($id, $memberProjectInfo, $memberId = null)
    {
        try {
            $tableName = 'board_notices';

            // 테이블 존재 여부 확인
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

            // 프로젝트 기수 필터링 확인
            $customFields = json_decode($notice->custom_fields ?? '{}', true);
            
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
            $noticeProjectInfo = [];
            if (isset($customFields['project_term'])) {
                $projectTermData = $customFields['project_term'];
                if (is_string($projectTermData)) {
                    $projectTermData = json_decode($projectTermData, true);
                }
                if (is_array($projectTermData)) {
                    $noticeProjectInfo = [
                        'project_term_id' => $projectTermData['project_term_id'] ?? null,
                        'course_id' => $projectTermData['course_id'] ?? null,
                        'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                        'project_period_id' => $projectTermData['project_period_id'] ?? null,
                        'country_id' => $projectTermData['country_id'] ?? null,
                    ];
                }
            }
            
            // students 필드 확인
            if (isset($customFields['students'])) {
                $studentsData = is_string($customFields['students']) 
                    ? json_decode($customFields['students'], true) 
                    : $customFields['students'];
                
                if (is_array($studentsData) && isset($studentsData['student_ids'])) {
                    $studentIds = is_array($studentsData['student_ids']) ? $studentsData['student_ids'] : [];
                    
                    // student_ids가 비어있으면 null 반환
                    if (empty($studentIds)) {
                        return null;
                    }
                    
                    // memberId가 student_ids에 포함되면 통과
                    if ($memberId && in_array((int)$memberId, array_map('intval', $studentIds))) {
                        return (object) [
                            'id' => $notice->id,
                            'title' => $customFields['title_en'] ?? $notice->title,
                            'content' => $customFields['content_en'] ?? $notice->content,
                            'author_name' => $notice->author_name,
                            'attachments' => json_decode($notice->attachments ?? '[]', true) ?: [],
                            'view_count' => $notice->view_count,
                            'is_notice' => $notice->is_notice,
                            'created_at' => $notice->created_at,
                        ];
                    }
                    
                    // 포함되지 않으면 null 반환
                    return null;
                }
            }
            
            // students 필드가 없으면 프로젝트 기수 조건 확인
            {
                // 학생 체크가 없으면 프로젝트 기수 조건 확인
                // 모든 필드가 일치하는지 확인
                foreach ($memberProjectInfo as $key => $memberValue) {
                    if ($memberValue === null) {
                        continue; // 회원 정보가 없으면 비교하지 않음
                    }
                    
                    $noticeValue = $noticeProjectInfo[$key] ?? null;
                    if ($noticeValue === null) {
                        return null; // 일치하지 않으면 null 반환
                    }
                    
                    // 숫자/문자열 모두 비교
                    if (($noticeValue != $memberValue) && ((string)$noticeValue !== (string)$memberValue)) {
                        return null; // 일치하지 않으면 null 반환
                    }
                }
            }

            // 첨부파일 파싱
            $attachments = [];
            if ($notice->attachments) {
                $attachments = json_decode($notice->attachments, true);
                if (!is_array($attachments)) {
                    $attachments = [];
                }
            }

            // custom_fields에서 영문 제목/내용 추출
            $customFields = json_decode($notice->custom_fields ?? '{}', true);
            if (!is_array($customFields)) {
                $customFields = [];
            }
            $englishTitle = $customFields['title_en'] ?? $notice->title;
            $englishContent = $customFields['content_en'] ?? $notice->content;

            return (object) [
                'id' => $notice->id,
                'title' => $englishTitle,
                'content' => $englishContent,
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

    /**
     * 이전/다음 공지사항 조회
     */
    private function getPrevNextNotices($currentId, $memberProjectInfo, $memberId = null)
    {
        try {
            $tableName = 'board_notices';

            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                return ['prev' => null, 'next' => null];
            }

            // 현재 공지사항 조회
            $currentNotice = DB::table($tableName)
                ->where('id', $currentId)
                ->whereNull('deleted_at')
                ->first();

            if (!$currentNotice) {
                return ['prev' => null, 'next' => null];
            }

            // 필터링된 공지사항 목록 조회 (프로젝트 기수 필터링은 PHP 레벨에서 처리)
            $query = DB::table($tableName)
                ->select('id', 'title', 'custom_fields', 'created_at')
                ->whereNull('deleted_at');

            $notices = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // PHP 레벨 필터링: students 필드의 student_ids에 체크된 학생만 표시
            $filteredNotices = $notices->filter(function ($notice) use ($memberProjectInfo, $memberId) {
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                
                if (!is_array($customFields)) {
                    return false;
                }
                
                // students 필드 확인
                if (isset($customFields['students'])) {
                    $studentsData = is_string($customFields['students']) 
                        ? json_decode($customFields['students'], true) 
                        : $customFields['students'];
                    
                    if (is_array($studentsData) && isset($studentsData['student_ids'])) {
                        $studentIds = is_array($studentsData['student_ids']) ? $studentsData['student_ids'] : [];
                        
                        // student_ids가 비어있으면 제외
                        if (empty($studentIds)) {
                            return false;
                        }
                        
                        // memberId가 student_ids에 포함되면 통과
                        if ($memberId && in_array((int)$memberId, array_map('intval', $studentIds))) {
                            return true;
                        }
                        
                        // 포함되지 않으면 제외
                        return false;
                    }
                }
                
                // students 필드가 없으면 프로젝트 기수 조건 확인
                if (isset($customFields['project_term']) && is_string($customFields['project_term'])) {
                    $projectTermParsed = json_decode($customFields['project_term'], true);
                    if (is_array($projectTermParsed)) {
                        $customFields['project_term'] = $projectTermParsed;
                    }
                }
                
                $noticeProjectInfo = [];
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $noticeProjectInfo = [
                            'project_term_id' => $projectTermData['project_term_id'] ?? null,
                            'course_id' => $projectTermData['course_id'] ?? null,
                            'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                            'project_period_id' => $projectTermData['project_period_id'] ?? null,
                            'country_id' => $projectTermData['country_id'] ?? null,
                        ];
                    }
                }
                
                $matches = true;
                foreach ($memberProjectInfo as $key => $memberValue) {
                    if ($memberValue === null) {
                        continue;
                    }
                    
                    $noticeValue = $noticeProjectInfo[$key] ?? null;
                    if ($noticeValue === null) {
                        $matches = false;
                        break;
                    }
                    
                    if (($noticeValue != $memberValue) && ((string)$noticeValue !== (string)$memberValue)) {
                        $matches = false;
                        break;
                    }
                }
                
                return $matches;
            })->values();

            // 현재 공지사항의 인덱스 찾기
            $currentIndex = $filteredNotices->search(function ($notice) use ($currentId) {
                return $notice->id == $currentId;
            });

            $prev = null;
            $next = null;

            if ($currentIndex !== false) {
                // 이전 게시글
                if ($currentIndex > 0) {
                    $prevNotice = $filteredNotices[$currentIndex - 1];
                    $prevCustomFields = json_decode($prevNotice->custom_fields ?? '{}', true);
                    if (!is_array($prevCustomFields)) {
                        $prevCustomFields = [];
                    }
                    $prevEnglishTitle = $prevCustomFields['title_en'] ?? $prevNotice->title;
                    $prev = (object) [
                        'id' => $prevNotice->id,
                        'title' => $prevEnglishTitle,
                    ];
                }

                // 다음 게시글
                if ($currentIndex < $filteredNotices->count() - 1) {
                    $nextNotice = $filteredNotices[$currentIndex + 1];
                    $nextCustomFields = json_decode($nextNotice->custom_fields ?? '{}', true);
                    if (!is_array($nextCustomFields)) {
                        $nextCustomFields = [];
                    }
                    $nextEnglishTitle = $nextCustomFields['title_en'] ?? $nextNotice->title;
                    $next = (object) [
                        'id' => $nextNotice->id,
                        'title' => $nextEnglishTitle,
                    ];
                }
            }

            return ['prev' => $prev, 'next' => $next];

        } catch (\Exception $e) {
            Log::error("이전/다음 공지사항 조회 오류: " . $e->getMessage());
            return ['prev' => null, 'next' => null];
        }
    }
}
