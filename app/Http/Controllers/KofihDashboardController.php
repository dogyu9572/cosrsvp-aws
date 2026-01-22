<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberNote;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KofihDashboardController extends Controller
{
    /**
     * 대시보드 메인 페이지
     */
    public function index(Request $request)
    {
        // 필터 파라미터
        $filters = [
            'project_term_id' => $request->get('project_term_id'),
            'course_id' => $request->get('course_id'),
            'operating_institution_id' => $request->get('operating_institution_id'),
            'project_period_id' => $request->get('project_period_id'),
            'country_id' => $request->get('country_id'),
        ];
        
        // 필터 옵션 로드
        $projectTerms = ProjectTerm::orderBy('name')->get();
        
        // 선택된 필터 값이 있을 때만 해당 항목들 로드
        $courses = collect();
        $operatingInstitutions = collect();
        $projectPeriods = collect();
        $countries = collect();
        
        if ($filters['project_term_id']) {
            $courses = Course::where('project_term_id', $filters['project_term_id'])->active()->orderBy('name_ko')->get();
        }
        if ($filters['course_id']) {
            $operatingInstitutions = OperatingInstitution::where('course_id', $filters['course_id'])->active()->orderBy('name_ko')->get();
        }
        if ($filters['operating_institution_id']) {
            $projectPeriods = ProjectPeriod::where('operating_institution_id', $filters['operating_institution_id'])->active()->orderBy('name_ko')->get();
        }
        if ($filters['project_period_id']) {
            $countries = Country::where('project_period_id', $filters['project_period_id'])->active()->orderBy('name_ko')->get();
        }
        
        // 통계 데이터 조회
        $statistics = $this->getStatistics($filters);
        
        // 일정 그룹 리스트 조회
        $scheduleGroups = $this->getScheduleGroups($filters);
        
        $gNum = 'main';
        $gName = '회원';
        
        return view('kofih.dashboard.index', compact(
            'filters',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries',
            'statistics',
            'scheduleGroups',
            'gNum',
            'gName'
        ));
    }
    
    /**
     * 통계 데이터 조회
     */
    private function getStatistics(array $filters)
    {
        try {
            // 필터 조건에 맞는 회원 쿼리 생성
            $memberQuery = Member::query();
            
            if (!empty($filters['project_term_id'])) {
                $memberQuery->where('project_term_id', $filters['project_term_id']);
            }
            if (!empty($filters['course_id'])) {
                $memberQuery->where('course_id', $filters['course_id']);
            }
            if (!empty($filters['operating_institution_id'])) {
                $memberQuery->where('operating_institution_id', $filters['operating_institution_id']);
            }
            if (!empty($filters['project_period_id'])) {
                $memberQuery->where('project_period_id', $filters['project_period_id']);
            }
            if (!empty($filters['country_id'])) {
                $memberQuery->where('country_id', $filters['country_id']);
            }
            
            // 전체 학생 수
            $totalStudents = $memberQuery->count();
            
            // 전체 그룹 수 (프로젝트 조합별 중복 제거)
            $totalGroups = Member::select('project_term_id', 'course_id', 'operating_institution_id', 'project_period_id', 'country_id')
                ->where(function($q) use ($filters) {
                    if (!empty($filters['project_term_id'])) {
                        $q->where('project_term_id', $filters['project_term_id']);
                    }
                    if (!empty($filters['course_id'])) {
                        $q->where('course_id', $filters['course_id']);
                    }
                    if (!empty($filters['operating_institution_id'])) {
                        $q->where('operating_institution_id', $filters['operating_institution_id']);
                    }
                    if (!empty($filters['project_period_id'])) {
                        $q->where('project_period_id', $filters['project_period_id']);
                    }
                    if (!empty($filters['country_id'])) {
                        $q->where('country_id', $filters['country_id']);
                    }
                })
                ->distinct()
                ->count();
            
            // 긴급 특이사항 수
            $urgentNotes = MemberNote::where('status', 'urgent')
                ->whereHas('member', function($q) use ($filters) {
                    if (!empty($filters['project_term_id'])) {
                        $q->where('project_term_id', $filters['project_term_id']);
                    }
                    if (!empty($filters['course_id'])) {
                        $q->where('course_id', $filters['course_id']);
                    }
                    if (!empty($filters['operating_institution_id'])) {
                        $q->where('operating_institution_id', $filters['operating_institution_id']);
                    }
                    if (!empty($filters['project_period_id'])) {
                        $q->where('project_period_id', $filters['project_period_id']);
                    }
                    if (!empty($filters['country_id'])) {
                        $q->where('country_id', $filters['country_id']);
                    }
                })
                ->count();
            
            // 주의 특이사항 수
            $cautionNotes = MemberNote::where('status', 'caution')
                ->whereHas('member', function($q) use ($filters) {
                    if (!empty($filters['project_term_id'])) {
                        $q->where('project_term_id', $filters['project_term_id']);
                    }
                    if (!empty($filters['course_id'])) {
                        $q->where('course_id', $filters['course_id']);
                    }
                    if (!empty($filters['operating_institution_id'])) {
                        $q->where('operating_institution_id', $filters['operating_institution_id']);
                    }
                    if (!empty($filters['project_period_id'])) {
                        $q->where('project_period_id', $filters['project_period_id']);
                    }
                    if (!empty($filters['country_id'])) {
                        $q->where('country_id', $filters['country_id']);
                    }
                })
                ->count();
            
            return [
                'total_students' => $totalStudents,
                'total_groups' => $totalGroups,
                'urgent_notes' => $urgentNotes,
                'caution_notes' => $cautionNotes,
            ];
            
        } catch (\Exception $e) {
            Log::error("대시보드 통계 데이터 조회 오류: " . $e->getMessage());
            return [
                'total_students' => 0,
                'total_groups' => 0,
                'urgent_notes' => 0,
                'caution_notes' => 0,
            ];
        }
    }
    
    /**
     * 프로젝트 그룹별 일정 리스트 조회
     * 회원 참여 여부와 관계없이 모든 가능한 프로젝트 조합을 생성
     */
    private function getScheduleGroups(array $filters)
    {
        try {
            // 필터 조건에 맞는 모든 프로젝트 조합 생성
            // 계층 구조: ProjectTerm → Course → OperatingInstitution → ProjectPeriod → Country
            
            // 1. 기수 조회 (필터 적용)
            $projectTermsQuery = ProjectTerm::query();
            if (!empty($filters['project_term_id'])) {
                $projectTermsQuery->where('id', $filters['project_term_id']);
            }
            $projectTerms = $projectTermsQuery->orderBy('name')->get();
            
            $groups = collect();
            
            // 2. 각 기수별로 모든 조합 생성
            foreach ($projectTerms as $projectTerm) {
                // 과정 조회 (필터 적용)
                $coursesQuery = Course::where('project_term_id', $projectTerm->id)->active();
                if (!empty($filters['course_id'])) {
                    $coursesQuery->where('id', $filters['course_id']);
                }
                $courses = $coursesQuery->orderBy('name_ko')->get();
                
                foreach ($courses as $course) {
                    // 운영기관 조회 (필터 적용)
                    $institutionsQuery = OperatingInstitution::where('course_id', $course->id)->active();
                    if (!empty($filters['operating_institution_id'])) {
                        $institutionsQuery->where('id', $filters['operating_institution_id']);
                    }
                    $institutions = $institutionsQuery->orderBy('name_ko')->get();
                    
                    foreach ($institutions as $institution) {
                        // 프로젝트 기간 조회 (필터 적용)
                        $periodsQuery = ProjectPeriod::where('operating_institution_id', $institution->id)->active();
                        if (!empty($filters['project_period_id'])) {
                            $periodsQuery->where('id', $filters['project_period_id']);
                        }
                        $periods = $periodsQuery->orderBy('name_ko')->get();
                        
                        foreach ($periods as $period) {
                            // 국가 조회 (필터 적용)
                            $countriesQuery = Country::where('project_period_id', $period->id)->active();
                            if (!empty($filters['country_id'])) {
                                $countriesQuery->where('id', $filters['country_id']);
                            }
                            $countries = $countriesQuery->orderBy('name_ko')->get();
                            
                            foreach ($countries as $country) {
                                // 프로젝트 조합 정보
                                $projectInfo = [
                                    'project_term_id' => $projectTerm->id,
                                    'course_id' => $course->id,
                                    'operating_institution_id' => $institution->id,
                                    'project_period_id' => $period->id,
                                    'country_id' => $country->id,
                                ];
                                
                                // 해당 프로젝트 조합의 회원 수 (0명일 수도 있음)
                                $memberCount = Member::where('project_term_id', $projectInfo['project_term_id'])
                                    ->where('course_id', $projectInfo['course_id'])
                                    ->where('operating_institution_id', $projectInfo['operating_institution_id'])
                                    ->where('project_period_id', $projectInfo['project_period_id'])
                                    ->where('country_id', $projectInfo['country_id'])
                                    ->count();
                                
                                // 해당 프로젝트 조합의 회원 ID 목록
                                $memberIds = Member::where('project_term_id', $projectInfo['project_term_id'])
                                    ->where('course_id', $projectInfo['course_id'])
                                    ->where('operating_institution_id', $projectInfo['operating_institution_id'])
                                    ->where('project_period_id', $projectInfo['project_period_id'])
                                    ->where('country_id', $projectInfo['country_id'])
                                    ->pluck('id');
                                
                                // 긴급 특이사항 수
                                $urgentCount = 0;
                                if ($memberIds->isNotEmpty()) {
                                    $urgentCount = MemberNote::where('status', 'urgent')
                                        ->whereIn('member_id', $memberIds)
                                        ->count();
                                }
                                
                                // 주의 특이사항 수
                                $cautionCount = 0;
                                if ($memberIds->isNotEmpty()) {
                                    $cautionCount = MemberNote::where('status', 'caution')
                                        ->whereIn('member_id', $memberIds)
                                        ->count();
                                }
                                
                                // 진행률 계산
                                $progressRate = $this->getProgressRate($projectInfo);
                                
                                // 단계별 일정 조회
                                $stepSchedules = $this->getStepSchedules($projectInfo);
                                
                                // 프로젝트 정보 문자열 생성
                                $titleParts = [];
                                $titleParts[] = $projectTerm->name ;
                                $titleParts[] = $course->name_ko ?? $course->name_en;
                                $titleParts[] = $institution->name_ko ?? $institution->name_en;
                                $titleParts[] = $period->name_ko ?? $period->name_en;
                                $titleParts[] = $country->name_ko ?? $country->name_en;
                                
                                $groups->push((object) [
                                    'project_info' => $projectInfo,
                                    'title' => implode(' / ', $titleParts),
                                    'member_count' => $memberCount,
                                    'urgent_count' => $urgentCount,
                                    'caution_count' => $cautionCount,
                                    'progress_rate' => $progressRate,
                                    'step_schedules' => $stepSchedules,
                                ]);
                            }
                        }
                    }
                }
            }
            
            return $groups;
            
        } catch (\Exception $e) {
            Log::error("일정 그룹 리스트 조회 오류: " . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * 진행률 계산
     * schedules 테이블의 start_date, end_date를 기준으로 완료된 일정 수를 계산
     */
    private function getProgressRate(array $projectInfo)
    {
        try {
            $schedules = $this->getStepSchedules($projectInfo);
            
            if ($schedules->isEmpty()) {
                return 0;
            }
            
            $today = Carbon::today();
            $completedCount = 0;
            $totalCount = $schedules->count();
            
            foreach ($schedules as $schedule) {
                // end_date가 있고, 오늘 날짜보다 과거인 경우 완료로 간주
                if (!empty($schedule->end_date)) {
                    try {
                        $endDate = Carbon::parse($schedule->end_date);
                        if ($endDate->isPast() || $endDate->isToday()) {
                            $completedCount++;
                        }
                    } catch (\Exception $e) {
                        // 날짜 파싱 오류 시 해당 일정은 제외
                        Log::warning("일정 날짜 파싱 오류 (ID: {$schedule->id}): " . $e->getMessage());
                    }
                }
            }
            
            return $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
            
        } catch (\Exception $e) {
            Log::error("진행률 계산 오류: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 단계별 일정 조회
     */
    private function getStepSchedules(array $projectInfo)
    {
        try {
            // Country 모델을 통해 프로젝트 정보 검증 및 schedules 조회
            $country = Country::where('id', $projectInfo['country_id'])
                ->whereHas('projectPeriod', function($q) use ($projectInfo) {
                    $q->where('id', $projectInfo['project_period_id'])
                      ->whereHas('operatingInstitution', function($q) use ($projectInfo) {
                          $q->where('id', $projectInfo['operating_institution_id'])
                            ->whereHas('course', function($q) use ($projectInfo) {
                                $q->where('id', $projectInfo['course_id'])
                                  ->where('project_term_id', $projectInfo['project_term_id']);
                            });
                      });
                })
                ->first();
            
            if (!$country) {
                return collect();
            }
            
            // 해당 Country의 활성화된 일정 조회
            $schedules = Schedule::where('country_id', $country->id)
                ->active()
                ->orderBy('display_order')
                ->get();
            
            return $schedules->map(function ($schedule) {
                return (object) [
                    'id' => $schedule->id,
                    'title' => $schedule->name_ko ?? $schedule->name_en,
                    'start_date' => $schedule->start_date?->format('Y-m-d'),
                    'end_date' => $schedule->end_date?->format('Y-m-d'),
                    'display_order' => $schedule->display_order ?? 999,
                    'created_at' => $schedule->created_at,
                ];
            });
            
        } catch (\Exception $e) {
            Log::error("단계별 일정 조회 오류: " . $e->getMessage());
            return collect();
        }
    }
}
