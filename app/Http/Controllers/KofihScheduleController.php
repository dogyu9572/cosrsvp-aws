<?php

namespace App\Http\Controllers;

use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KofihScheduleController extends Controller
{
    /**
     * 일정 페이지 표시
     */
    public function index(Request $request)
    {
        // 현재 월 (기본값: 현재 월)
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // 필터 파라미터
        $projectTermId = $request->get('project_term_id');
        $courseId = $request->get('course_id');
        $operatingInstitutionId = $request->get('operating_institution_id');
        $projectPeriodId = $request->get('project_period_id');
        $countryId = $request->get('country_id');
        
        // board_schedules 테이블에서 일정 조회 (일반 사용자용과 동일)
        $schedules = $this->getSchedulesFromBoard($request);
        
        // 필터 옵션
        // 초기 상태: 기수만 전체 목록 표시, 나머지는 빈 상태로 시작
        $projectTerms = ProjectTerm::orderBy('name')->get();
        
        // 선택된 필터 값이 있을 때만 해당 항목들 로드
        $courses = collect();
        $operatingInstitutions = collect();
        $projectPeriods = collect();
        $countries = collect();
        
        if ($projectTermId) {
            $courses = Course::where('project_term_id', $projectTermId)->active()->orderBy('name_ko')->get();
        }
        if ($courseId) {
            $operatingInstitutions = OperatingInstitution::where('course_id', $courseId)->active()->orderBy('name_ko')->get();
        }
        if ($operatingInstitutionId) {
            $projectPeriods = ProjectPeriod::where('operating_institution_id', $operatingInstitutionId)->active()->orderBy('name_ko')->get();
        }
        if ($projectPeriodId) {
            $countries = Country::where('project_period_id', $projectPeriodId)->active()->orderBy('name_ko')->get();
        }
        
        return view('kofih.schedule.index', compact(
            'schedules',
            'year',
            'month',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries',
            'projectTermId',
            'courseId',
            'operatingInstitutionId',
            'projectPeriodId',
            'countryId'
        ));
    }
    
    /**
     * board_schedules 테이블에서 일정 조회 (일반 사용자용과 동일한 로직)
     */
    private function getSchedulesFromBoard(Request $request)
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

            // 필터 적용
            $projectTermId = $request->get('project_term_id');
            $courseId = $request->get('course_id');
            $operatingInstitutionId = $request->get('operating_institution_id');
            $projectPeriodId = $request->get('project_period_id');
            $countryId = $request->get('country_id');
            
            // 필터 조건 구성
            $filterConditions = [
                'project_term_id' => $projectTermId,
                'course_id' => $courseId,
                'operating_institution_id' => $operatingInstitutionId,
                'project_period_id' => $projectPeriodId,
                'country_id' => $countryId,
            ];
            
            // 프로젝트 관련 필터 적용 (SQL 레벨에서 기본 필터링)
            if ($projectTermId || $courseId || $operatingInstitutionId || $projectPeriodId || $countryId) {
                if ($projectTermId) {
                    $projectTermIdStr = (string)$projectTermId;
                    $query->where(function($q) use ($projectTermId, $projectTermIdStr) {
                        $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_term_id":' . $projectTermId . '%'])
                          ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_term_id":"' . $projectTermIdStr . '"%'])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_term_id') = ?", [$projectTermId])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_term_id') = ?", [$projectTermIdStr])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"project_term_id":' . $projectTermId . '%'])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"project_term_id":"' . $projectTermIdStr . '"%']);
                    });
                }
                if ($courseId) {
                    $courseIdStr = (string)$courseId;
                    $query->where(function($q) use ($courseId, $courseIdStr) {
                        $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"course_id":' . $courseId . '%'])
                          ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"course_id":"' . $courseIdStr . '"%'])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.course_id') = ?", [$courseId])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.course_id') = ?", [$courseIdStr])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"course_id":' . $courseId . '%'])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"course_id":"' . $courseIdStr . '"%']);
                    });
                }
                if ($operatingInstitutionId) {
                    $operatingInstitutionIdStr = (string)$operatingInstitutionId;
                    $query->where(function($q) use ($operatingInstitutionId, $operatingInstitutionIdStr) {
                        $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"operating_institution_id":' . $operatingInstitutionId . '%'])
                          ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"operating_institution_id":"' . $operatingInstitutionIdStr . '"%'])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.operating_institution_id') = ?", [$operatingInstitutionId])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.operating_institution_id') = ?", [$operatingInstitutionIdStr])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"operating_institution_id":' . $operatingInstitutionId . '%'])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"operating_institution_id":"' . $operatingInstitutionIdStr . '"%']);
                    });
                }
                if ($projectPeriodId) {
                    $projectPeriodIdStr = (string)$projectPeriodId;
                    $query->where(function($q) use ($projectPeriodId, $projectPeriodIdStr) {
                        $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_period_id":' . $projectPeriodId . '%'])
                          ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"project_period_id":"' . $projectPeriodIdStr . '"%'])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_period_id') = ?", [$projectPeriodId])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.project_period_id') = ?", [$projectPeriodIdStr])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"project_period_id":' . $projectPeriodId . '%'])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"project_period_id":"' . $projectPeriodIdStr . '"%']);
                    });
                }
                if ($countryId) {
                    $countryIdStr = (string)$countryId;
                    $query->where(function($q) use ($countryId, $countryIdStr) {
                        $q->whereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"country_id":' . $countryId . '%'])
                          ->orWhereRaw("JSON_EXTRACT(custom_fields, '$.project_term') LIKE ?", ['%"country_id":"' . $countryIdStr . '"%'])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.country_id') = ?", [$countryId])
                          ->orWhereRaw("JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.project_term')), '$.country_id') = ?", [$countryIdStr])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"country_id":' . $countryId . '%'])
                          ->orWhereRaw("custom_fields LIKE ?", ['%"country_id":"' . $countryIdStr . '"%']);
                    });
                }
            }

            $schedules = $query->orderBy('created_at', 'desc')->get();
            
            // PHP 레벨에서 추가 검증: 모든 필터 조건이 일치하는 것만 필터링
            if ($projectTermId || $courseId || $operatingInstitutionId || $projectPeriodId || $countryId) {
                $schedules = $schedules->filter(function ($schedule) use ($filterConditions) {
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
                    
                    // 모든 필터 조건이 일치하는지 확인
                    foreach ($filterConditions as $key => $filterValue) {
                        if (empty($filterValue)) {
                            continue; // 필터 값이 없으면 비교하지 않음
                        }
                        
                        $scheduleValue = $scheduleProjectInfo[$key] ?? null;
                        if ($scheduleValue === null) {
                            return false; // 일정에 해당 필드가 없으면 제외
                        }
                        
                        // 숫자/문자열 모두 비교
                        if (($scheduleValue != $filterValue) && ((string)$scheduleValue !== (string)$filterValue)) {
                            return false; // 값이 일치하지 않으면 제외
                        }
                    }
                    
                    return true; // 모든 필터 조건이 일치함
                })->values();
            }
            
            // 날짜 정보 추출 및 변환
            return $schedules->map(function ($schedule) {
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

                // project_term 정보 추출 (프로젝트 정보 문자열 생성용)
                $projectInfo = '';
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $parts = [];
                        if (isset($projectTermData['project_term_id'])) {
                            $term = \App\Models\ProjectTerm::find($projectTermData['project_term_id']);
                            if ($term) $parts[] = $term->name;
                        }
                        if (isset($projectTermData['course_id'])) {
                            $course = \App\Models\Course::find($projectTermData['course_id']);
                            if ($course) $parts[] = $course->name_ko ?? $course->name_en;
                        }
                        if (isset($projectTermData['operating_institution_id'])) {
                            $institution = \App\Models\OperatingInstitution::find($projectTermData['operating_institution_id']);
                            if ($institution) $parts[] = $institution->name_ko ?? $institution->name_en;
                        }
                        if (isset($projectTermData['project_period_id'])) {
                            $period = \App\Models\ProjectPeriod::find($projectTermData['project_period_id']);
                            if ($period) $parts[] = $period->name_ko ?? $period->name_en;
                        }
                        if (isset($projectTermData['country_id'])) {
                            $country = \App\Models\Country::find($projectTermData['country_id']);
                            if ($country) $parts[] = $country->name_ko ?? $country->name;
                        }
                        $projectInfo = implode(' / ', $parts);
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
                    'project_info' => $projectInfo,
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
    
    /**
     * 기수에 따른 과정 목록 조회 (AJAX)
     */
    public function getCoursesByProjectTerm(Request $request)
    {
        $projectTermId = $request->get('project_term_id');
        
        if (!$projectTermId) {
            return response()->json([]);
        }
        
        $courses = Course::where('project_term_id', $projectTermId)
            ->active()
            ->orderBy('display_order')
            ->orderBy('name_ko')
            ->get(['id', 'name_ko', 'name_en']);
        
        return response()->json($courses);
    }
    
    /**
     * 과정에 따른 운영기관 목록 조회 (AJAX)
     */
    public function getInstitutionsByCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json([]);
        }
        
        $institutions = OperatingInstitution::where('course_id', $courseId)
            ->active()
            ->orderBy('display_order')
            ->orderBy('name_ko')
            ->get(['id', 'name_ko', 'name_en']);
        
        return response()->json($institutions);
    }
    
    /**
     * 운영기관에 따른 프로젝트기간 목록 조회 (AJAX)
     */
    public function getProjectPeriodsByInstitution(Request $request)
    {
        $operatingInstitutionId = $request->get('operating_institution_id');
        
        if (!$operatingInstitutionId) {
            return response()->json([]);
        }
        
        $periods = ProjectPeriod::where('operating_institution_id', $operatingInstitutionId)
            ->active()
            ->orderBy('display_order')
            ->orderBy('name_ko')
            ->get(['id', 'name_ko', 'name_en']);
        
        return response()->json($periods);
    }
    
    /**
     * 프로젝트기간에 따른 국가 목록 조회 (AJAX)
     */
    public function getCountriesByProjectPeriod(Request $request)
    {
        $projectPeriodId = $request->get('project_period_id');
        
        if (!$projectPeriodId) {
            return response()->json([]);
        }
        
        try {
            $countries = Country::where('project_period_id', $projectPeriodId)
                ->active()
                ->orderBy('display_order')
                ->orderBy('name_ko')
                ->get(['id', 'name_ko', 'name']);
            
            return response()->json($countries);
        } catch (\Exception $e) {
            \Log::error('국가 조회 오류: ' . $e->getMessage());
            return response()->json(['error' => '국가 조회 중 오류가 발생했습니다.'], 500);
        }
    }
}