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

        // 공지사항 데이터 조회
        $notices = $this->getNoticesByProjectTerm($memberProjectInfo, $request);

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

        // 공지사항 상세 조회
        $notice = $this->getNoticeById($id, $memberProjectInfo);

        if (!$notice) {
            abort(404, '공지사항을 찾을 수 없습니다.');
        }

        // 조회수 증가
        DB::table('board_notices')
            ->where('id', $id)
            ->increment('view_count');

        // 이전/다음 게시글 조회
        $prevNext = $this->getPrevNextNotices($id, $memberProjectInfo);

        return view('notice.show', compact('gNum', 'gName', 'sName', 'notice', 'memberProjectInfo', 'prevNext'));
    }

    /**
     * 프로젝트 기수에 해당하는 공지사항 조회
     */
    private function getNoticesByProjectTerm($memberProjectInfo, Request $request)
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

            // 정렬: 공지글 우선, 최신순
            $notices = $query->orderBy('is_notice', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            // 필터링 후 추가 검증: PHP 레벨에서 모든 프로젝트 관련 필드가 일치하는 것만 필터링
            $filteredNotices = $notices->filter(function ($notice) use ($memberProjectInfo) {
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                
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
                
                return $matches;
            });

            // 데이터 변환 (attachments 파싱)
            $transformedNotices = $filteredNotices->map(function ($notice) {
                $attachments = [];
                if ($notice->attachments) {
                    $attachments = json_decode($notice->attachments, true);
                    if (!is_array($attachments)) {
                        $attachments = [];
                    }
                }

                return (object) [
                    'id' => $notice->id,
                    'title' => $notice->title,
                    'content' => $notice->content,
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
            return collect();
        }
    }

    /**
     * 공지사항 상세 조회
     */
    private function getNoticeById($id, $memberProjectInfo)
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

            // 첨부파일 파싱
            $attachments = [];
            if ($notice->attachments) {
                $attachments = json_decode($notice->attachments, true);
                if (!is_array($attachments)) {
                    $attachments = [];
                }
            }

            return (object) [
                'id' => $notice->id,
                'title' => $notice->title,
                'content' => $notice->content,
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
    private function getPrevNextNotices($currentId, $memberProjectInfo)
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

            // 필터링된 공지사항 목록 조회 (프로젝트 기수 필터링 적용)
            $query = DB::table($tableName)
                ->select('id', 'title', 'custom_fields', 'created_at')
                ->whereNull('deleted_at');

            // 프로젝트 관련 정보 필터링
            if ($memberProjectInfo['project_term_id']) {
                $query->where(function($q) use ($memberProjectInfo) {
                    $projectTermId = $memberProjectInfo['project_term_id'];
                    $projectTermIdStr = (string)$projectTermId;
                    
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

            $notices = $query->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // PHP 레벨 필터링
            $filteredNotices = $notices->filter(function ($notice) use ($memberProjectInfo) {
                $customFields = json_decode($notice->custom_fields ?? '{}', true);
                
                if (!is_array($customFields)) {
                    return false;
                }
                
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
                    $prev = (object) [
                        'id' => $prevNotice->id,
                        'title' => $prevNotice->title,
                    ];
                }

                // 다음 게시글
                if ($currentIndex < $filteredNotices->count() - 1) {
                    $nextNotice = $filteredNotices[$currentIndex + 1];
                    $next = (object) [
                        'id' => $nextNotice->id,
                        'title' => $nextNotice->title,
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
