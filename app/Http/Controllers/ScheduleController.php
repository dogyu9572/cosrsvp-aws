<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * 스케줄 페이지 표시
     */
    public function index()
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "02";
        $gName = "Schedule";
        $sName = "";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];
        
        // 1. 로그인한 회원의 프로젝트 기수 데이터
        Log::info("로그인한 회원의 프로젝트 기수 데이터", [
            'member_id' => $member['id'] ?? null,
            'member_name' => $member['name'] ?? null,
            'project_term_id' => $memberProjectInfo['project_term_id'],
            'course_id' => $memberProjectInfo['course_id'],
            'operating_institution_id' => $memberProjectInfo['operating_institution_id'],
            'project_period_id' => $memberProjectInfo['project_period_id'],
            'country_id' => $memberProjectInfo['country_id'],
        ]);

        // 스케줄 데이터 조회
        $schedules = $this->getSchedulesByProjectTerm($memberProjectInfo);

        // 뷰 호환성을 위해 기존 변수명도 유지
        $memberProjectTermId = $memberProjectInfo['project_term_id'];

        return view('schedule.index', compact('gNum', 'gName', 'sName', 'schedules', 'memberProjectTermId'));
    }

    /**
     * 프로젝트 기수에 해당하는 스케줄 조회
     */
    private function getSchedulesByProjectTerm($memberProjectInfo)
    {
        try {
            $tableName = 'board_schedules';

            // 테이블 존재 여부 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                Log::warning("스케줄 테이블이 존재하지 않음: " . $tableName);
                return collect();
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'custom_fields', 'created_at')
                ->whereNull('deleted_at');

            // 2. 스케줄 전체 일정관리 정보
            $allSchedules = DB::table($tableName)
                ->select('id', 'title', 'custom_fields', 'created_at')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($schedule) {
                    $customFields = json_decode($schedule->custom_fields ?? '{}', true);
                    $projectInfo = [];
                    
                    if (isset($customFields['project_term'])) {
                        $projectTermData = $customFields['project_term'];
                        if (is_string($projectTermData)) {
                            $projectTermData = json_decode($projectTermData, true);
                        }
                        if (is_array($projectTermData)) {
                            $projectInfo = [
                                'project_term_id' => $projectTermData['project_term_id'] ?? null,
                                'course_id' => $projectTermData['course_id'] ?? null,
                                'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                                'project_period_id' => $projectTermData['project_period_id'] ?? null,
                                'country_id' => $projectTermData['country_id'] ?? null,
                            ];
                        }
                    }
                    
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'project_term_id' => $projectInfo['project_term_id'] ?? null,
                        'course_id' => $projectInfo['course_id'] ?? null,
                        'operating_institution_id' => $projectInfo['operating_institution_id'] ?? null,
                        'project_period_id' => $projectInfo['project_period_id'] ?? null,
                        'country_id' => $projectInfo['country_id'] ?? null,
                    ];
                })
                ->toArray();
            
            Log::info("스케줄 전체 일정관리 정보", [
                'total_count' => count($allSchedules),
                'schedules' => $allSchedules,
            ]);

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

            $schedules = $query->orderBy('created_at', 'desc')->get();
            
            // 필터링 후 추가 검증: PHP 레벨에서 모든 프로젝트 관련 필드가 일치하는 것만 필터링
            $filteredSchedules = $schedules->filter(function ($schedule) use ($memberProjectInfo) {
                $customFields = json_decode($schedule->custom_fields ?? '{}', true);
                
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
                $scheduleProjectInfo = [];
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $scheduleProjectInfo = [
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
                    
                    $scheduleValue = $scheduleProjectInfo[$key] ?? null;
                    if ($scheduleValue === null) {
                        $matches = false;
                        break;
                    }
                    
                    // 숫자/문자열 모두 비교
                    if (($scheduleValue != $memberValue) && ((string)$scheduleValue !== (string)$memberValue)) {
                        $matches = false;
                        break;
                    }
                }
                
                return $matches;
            });
            
            // 3. 필터링 후 일정관리 정보
            $filteredSchedulesInfo = $filteredSchedules->map(function($schedule) {
                $customFields = json_decode($schedule->custom_fields ?? '{}', true);
                $projectInfo = [];
                
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $projectInfo = [
                            'project_term_id' => $projectTermData['project_term_id'] ?? null,
                            'course_id' => $projectTermData['course_id'] ?? null,
                            'operating_institution_id' => $projectTermData['operating_institution_id'] ?? null,
                            'project_period_id' => $projectTermData['project_period_id'] ?? null,
                            'country_id' => $projectTermData['country_id'] ?? null,
                        ];
                    }
                }
                
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'project_term_id' => $projectInfo['project_term_id'] ?? null,
                    'course_id' => $projectInfo['course_id'] ?? null,
                    'operating_institution_id' => $projectInfo['operating_institution_id'] ?? null,
                    'project_period_id' => $projectInfo['project_period_id'] ?? null,
                    'country_id' => $projectInfo['country_id'] ?? null,
                ];
            })->toArray();
            
            Log::info("필터링 후 일정관리 정보", [
                'filter_project_info' => $memberProjectInfo,
                'count' => count($filteredSchedulesInfo),
                'schedules' => $filteredSchedulesInfo,
            ]);
            
            return $filteredSchedules->map(function ($schedule) {
                    $customFields = json_decode($schedule->custom_fields ?? '{}', true);
                    
                    if (!is_array($customFields)) {
                        $customFields = [];
                    }
                    
                    // project_term이 JSON 문자열로 저장된 경우 파싱
                    if (isset($customFields['project_term']) && is_string($customFields['project_term'])) {
                        $projectTermParsed = json_decode($customFields['project_term'], true);
                        if (is_array($projectTermParsed)) {
                            $customFields['project_term'] = $projectTermParsed;
                        }
                    }
                    
                    // 날짜 범위 추출 (display_date 또는 display_date_range 필드 확인)
                    $displayDateField = $customFields['display_date_range'] ?? $customFields['display_date'] ?? null;
                    $startDate = null;
                    $endDate = null;
                    
                    if ($displayDateField) {
                        // display_date가 JSON 문자열로 저장된 경우 파싱
                        if (is_string($displayDateField)) {
                            $decoded = json_decode($displayDateField, true);
                            if (is_array($decoded)) {
                                $displayDateField = $decoded;
                            }
                        }
                        
                        if (is_array($displayDateField)) {
                            // use_display_date가 true인 경우에만 날짜 사용 (schedules 게시판은 항상 사용)
                            $useDisplayDate = $displayDateField['use_display_date'] ?? true;
                            
                            if ($useDisplayDate) {
                                $startDate = $displayDateField['start_date'] ?? null;
                                $endDate = $displayDateField['end_date'] ?? null;
                            }
                        }
                    }

                    // 날짜 범위가 있으면 일수 계산
                    $daySpan = null;
                    if ($startDate && $endDate) {
                        try {
                            $start = Carbon::parse($startDate);
                            $end = Carbon::parse($endDate);
                            $daySpan = $start->diffInDays($end) + 1; // 시작일 포함
                        } catch (\Exception $e) {
                            Log::error("날짜 파싱 오류 (ID: {$schedule->id}): " . $e->getMessage());
                        }
                    }

                    // project_term 정보 추출
                    $projectTermInfo = null;
                    if (isset($customFields['project_term'])) {
                        $projectTermData = $customFields['project_term'];
                        if (is_string($projectTermData)) {
                            $projectTermData = json_decode($projectTermData, true);
                        }
                        if (is_array($projectTermData)) {
                            $projectTermInfo = $projectTermData['project_term_id'] ?? null;
                        }
                    }

                    return (object) [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'content' => $schedule->content ?? '',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'day_span' => $daySpan,
                        'created_at' => $schedule->created_at,
                        'project_term_id' => $projectTermInfo,
                    ];
                })
                ->filter(function ($schedule) {
                    // 날짜 범위가 있는 일정만 반환
                    return $schedule->start_date && $schedule->end_date;
                })
                ->values();

        } catch (\Exception $e) {
            Log::error("스케줄 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
    }
}
