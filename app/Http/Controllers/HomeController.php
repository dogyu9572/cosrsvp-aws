<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\Popup;
use App\Models\Banner;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\Country;
use App\Models\Schedule;
use App\Models\Alert;
use App\Services\ExchangeRateService;
use App\Services\WeatherService;
use App\Services\NaverNewsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected $exchangeRateService;
    protected $weatherService;
    protected $newsService;

    public function __construct(ExchangeRateService $exchangeRateService, WeatherService $weatherService, NaverNewsService $newsService)
    {
        $this->exchangeRateService = $exchangeRateService;
        $this->weatherService = $weatherService;
        $this->newsService = $newsService;
    }

    public function index()
    {
        // 로그인 체크
        $member = session('member');
        if (!$member || !isset($member['id'])) {
            return redirect()->route('login')->with('error', '로그인이 필요합니다.');
        }
        
        $gNum = "main";
        $gName = "";
        $sName = "";
        
        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];
        
        // gallerys 게시판 최신글 4개
        $galleryPosts = $this->getLatestPosts('gallerys', 4);
        
        // top-notices 게시판 최신글 1개 (띠공지) - 프로젝트 필터링 적용
        $topNotice = $this->getLatestPostsWithFilter('top-notices', 1, $memberProjectInfo)->first();
        
        // notices 게시판 최신글 3개 - 프로젝트 필터링 적용 (학생 체크 포함)
        $memberId = $member['id'] ?? null;
        $noticePosts = $this->getLatestPostsWithFilter('notices', 3, $memberProjectInfo, $memberId);
        
        // 스케줄 데이터 조회
        $schedules = $this->getSchedulesByProjectTerm($memberProjectInfo);
        
        // 로그인한 회원의 전체 정보 조회 (입출국 정보 포함)
        $memberModel = null;
        $memberDocuments = collect();
        $countryDocument = null;
        $memberDocument = null;
        $documentName = null;
        $submissionDeadline = null;
        
        if ($memberId) {
            $memberModel = Member::find($memberId);
            $memberDocuments = MemberDocument::where('member_id', $memberId)
                ->orderBy('submission_deadline', 'asc')
                ->get();
            
            // 회원의 국가 정보에서 서류명, 제출마감일 조회
            if ($memberModel && $memberModel->country_id) {
                $country = Country::find($memberModel->country_id);
                if ($country) {
                    $countryDocument = (object) [
                        'document_name' => $country->document_name,
                        'submission_deadline' => $country->submission_deadline,
                    ];
                }
            }
            
            // MemberDocument가 있으면 MemberDocument 사용, 없으면 Country 정보 사용
            $memberDocument = $memberDocuments && $memberDocuments->count() > 0 ? $memberDocuments->first() : null;
            $documentName = $memberDocument ? $memberDocument->document_name : ($countryDocument ? $countryDocument->document_name : null);
            $submissionDeadline = $memberDocument ? $memberDocument->submission_deadline : ($countryDocument ? $countryDocument->submission_deadline : null);
        }
        
        // 회원의 국가에 해당하는 일정 조회 (최대 5개)
        $stepSchedules = collect();
        if ($memberModel && $memberModel->country_id) {
            $stepSchedules = Schedule::where('country_id', $memberModel->country_id)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->take(5)
                ->get()
                ->map(function($schedule) {
                    $today = Carbon::today(); // 날짜만 비교 (시간 제외)
                    $startDate = $schedule->start_date ? Carbon::parse($schedule->start_date)->startOfDay() : null;
                    $endDate = $schedule->end_date ? Carbon::parse($schedule->end_date)->endOfDay() : null;
                    
                    $isCurrent = $startDate && $endDate 
                        && $today >= $startDate 
                        && $today <= $endDate;
                    
                    return (object) [
                        'id' => $schedule->id,
                        'step_number' => $schedule->display_order,
                        'name_en' => $schedule->name_en,
                        'start_date' => $schedule->start_date,
                        'end_date' => $schedule->end_date,
                        'is_current' => $isCurrent,
                        'is_completed' => $endDate && $today > $endDate,
                    ];
                });
        }
        
        // 활성화된 팝업 조회 (쿠키 확인하여 숨겨진 팝업 제외)
        $popups = Popup::select('id', 'title', 'popup_type', 'popup_display_type', 'popup_image', 'popup_content', 'url', 'url_target', 'width', 'height', 'position_top', 'position_left')
            ->active()
            ->inPeriod()
            ->ordered()
            ->get()
            ->filter(function($popup) {
                // 서버사이드에서 쿠키 확인하여 숨겨진 팝업 제외
                $cookieName = 'popup_hide_' . $popup->id;
                return !isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] !== '1';
            });

        // 활성화된 배너 조회
        $banners = Banner::active()
            ->inPeriod()
            ->ordered()
            ->get();
        
        // 뉴스 데이터 조회 (카테고리별)
        $newsByCategory = $this->getNewsByCategory();
        
        return view('home.index', compact('gNum', 'gName', 'sName', 'galleryPosts', 'topNotice', 'noticePosts', 'popups', 'banners', 'schedules', 'memberDocuments', 'memberId', 'memberModel', 'countryDocument', 'memberDocument', 'documentName', 'submissionDeadline', 'stepSchedules', 'newsByCategory'));
    }
    
    /**
     * 특정 게시판의 최신글을 가져옵니다.
     */
    private function getLatestPosts($boardSlug, $limit = 4)
    {
        try {
            $board = Board::where('slug', $boardSlug)->first();
            if (!$board) {
                return collect();
            }
            
            $tableName = "board_{$boardSlug}";
            
            // 테이블 존재 여부 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                return collect();
            }
            
            return DB::table($tableName)
                ->select('id', 'title', 'content', 'created_at', 'thumbnail', 'category')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($post) use ($boardSlug) {
                    return (object) [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content ?? '',
                        'created_at' => $post->created_at,
                        'thumbnail' => $post->thumbnail,
                        'url' => route('backoffice.board-posts.show', [$boardSlug, $post->id])
                    ];
                });
                
        } catch (\Exception $e) {
            Log::error("게시판 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * 프로젝트 필터링을 적용하여 특정 게시판의 최신글을 가져옵니다.
     * students 필드가 있으면 학생 체크만 확인, 없으면 프로젝트 기수 조건 확인.
     */
    private function getLatestPostsWithFilter($boardSlug, $limit = 4, $memberProjectInfo = [], $memberId = null)
    {
        try {
            $board = Board::where('slug', $boardSlug)->first();
            if (!$board) {
                return collect();
            }
            
            $tableName = "board_{$boardSlug}";
            
            // 테이블 존재 여부 확인
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                return collect();
            }
            
            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'created_at', 'thumbnail', 'category', 'custom_fields')
                ->whereNull('deleted_at');

            // 프로젝트 관련 정보 필터링 (project_term_id가 있는 경우)
            if (!empty($memberProjectInfo['project_term_id'])) {
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

            $posts = $query->orderBy('created_at', 'desc')
                ->get();

            // 필터링 후 추가 검증: students 필드 확인 후 프로젝트 기수 조건 확인
            $filteredPosts = $posts->filter(function ($post) use ($memberProjectInfo, $memberId) {
                $customFields = json_decode($post->custom_fields ?? '{}', true);
                
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
                        
                        // student_ids가 비어있으면 표시하지 않음
                        if (empty($studentIds)) {
                            return false;
                        }
                        
                        // memberId가 student_ids에 포함되면 표시 (프로젝트 기수 조건 무시)
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
                
                // 표출일정 체크
                $displayDateField = $customFields['display_date_range'] ?? $customFields['display_date'] ?? null;
                if ($displayDateField) {
                    // display_date가 JSON 문자열로 저장된 경우 파싱
                    if (is_string($displayDateField)) {
                        $decoded = json_decode($displayDateField, true);
                        if (is_array($decoded)) {
                            $displayDateField = $decoded;
                        }
                    }
                    
                    if (is_array($displayDateField)) {
                        $useDisplayDate = $displayDateField['use_display_date'] ?? false;
                        
                        // use_display_date가 true인 경우에만 표출일정 체크
                        if ($useDisplayDate) {
                            $startDate = $displayDateField['start_date'] ?? null;
                            $endDate = $displayDateField['end_date'] ?? null;
                            
                            if ($startDate || $endDate) {
                                $today = Carbon::now()->startOfDay();
                                
                                // 시작일이 있고 오늘보다 미래면 제외
                                if ($startDate) {
                                    try {
                                        $start = Carbon::parse($startDate)->startOfDay();
                                        if ($today->lt($start)) {
                                            return false;
                                        }
                                    } catch (\Exception $e) {
                                        Log::error("표출일정 시작일 파싱 오류 (ID: {$post->id}): " . $e->getMessage());
                                    }
                                }
                                
                                // 종료일이 있고 오늘보다 과거면 제외
                                if ($endDate) {
                                    try {
                                        $end = Carbon::parse($endDate)->endOfDay();
                                        if ($today->gt($end)) {
                                            return false;
                                        }
                                    } catch (\Exception $e) {
                                        Log::error("표출일정 종료일 파싱 오류 (ID: {$post->id}): " . $e->getMessage());
                                    }
                                }
                            }
                        }
                    }
                }
                
                // project_term 데이터 추출
                $postProjectInfo = [];
                if (isset($customFields['project_term'])) {
                    $projectTermData = $customFields['project_term'];
                    if (is_string($projectTermData)) {
                        $projectTermData = json_decode($projectTermData, true);
                    }
                    if (is_array($projectTermData)) {
                        $postProjectInfo = [
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
                    
                    $postValue = $postProjectInfo[$key] ?? null;
                    if ($postValue === null) {
                        $matches = false;
                        break;
                    }
                    
                    // 숫자/문자열 모두 비교
                    if (($postValue != $memberValue) && ((string)$postValue !== (string)$memberValue)) {
                        $matches = false;
                        break;
                    }
                }
                
                return $matches;
            })->take($limit);

            return $filteredPosts->map(function ($post) use ($boardSlug) {
                // custom_fields에서 영문 제목/내용 추출
                $customFields = json_decode($post->custom_fields ?? '{}', true);
                if (!is_array($customFields)) {
                    $customFields = [];
                }
                $englishTitle = $customFields['title_en'] ?? $post->title;
                $englishContent = $customFields['content_en'] ?? ($post->content ?? '');

                return (object) [
                    'id' => $post->id,
                    'title' => $englishTitle,
                    'content' => $englishContent,
                    'created_at' => $post->created_at,
                    'thumbnail' => $post->thumbnail,
                    'url' => route('backoffice.board-posts.show', [$boardSlug, $post->id])
                ];
            });
                
        } catch (\Exception $e) {
            Log::error("게시판 데이터 조회 오류: " . $e->getMessage());
            return collect();
        }
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
                return collect();
            }

            $query = DB::table($tableName)
                ->select('id', 'title', 'content', 'custom_fields', 'created_at')
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

    /**
     * 카테고리별 뉴스 데이터 조회
     */
    private function getNewsByCategory()
    {
        try {
            // 카테고리별로 뉴스 가져오기
            $newsByCategory = [
                'all' => [],
                'main_news' => [],
                'lifestyle' => [],
                'fashion' => [],
                'entertainment' => []
            ];
            
            // all 카테고리: 모든 뉴스 가져오기
            $allNews = $this->newsService->getTrendingNews(null, 10, 5);
            $newsByCategory['all'] = array_slice($allNews, 0, 3);
            
            // 각 카테고리별로 별도 호출
            $mainNews = $this->newsService->getTrendingNews('main_news', 10, 5);
            $newsByCategory['main_news'] = array_slice($mainNews, 0, 3);
            
            $lifestyleNews = $this->newsService->getTrendingNews('lifestyle', 10, 5);
            $newsByCategory['lifestyle'] = array_slice($lifestyleNews, 0, 3);
            
            $fashionNews = $this->newsService->getTrendingNews('fashion', 10, 5);
            $newsByCategory['fashion'] = array_slice($fashionNews, 0, 3);
            
            $entertainmentNews = $this->newsService->getTrendingNews('entertainment', 10, 5);
            $newsByCategory['entertainment'] = array_slice($entertainmentNews, 0, 3);
            
            return $newsByCategory;
        } catch (\Exception $e) {
            Log::error("뉴스 데이터 조회 오류: " . $e->getMessage());
            return [
                'all' => [],
                'main_news' => [],
                'lifestyle' => [],
                'fashion' => [],
                'entertainment' => []
            ];
        }
    }

    /**
     * 환율 정보를 JSON으로 반환합니다.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExchangeRates()
    {
        $rates = $this->exchangeRateService->getExchangeRates();
        
        // 모든 값이 null이면 실패 응답
        if ($rates['usd'] === null && $rates['eur'] === null && $rates['gbp'] === null) {
            return response()->json([
                'success' => false,
                'data' => null,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $rates,
        ]);
    }

    /**
     * 날씨 정보를 JSON으로 반환합니다.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeather(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        
        $weather = $this->weatherService->getWeatherInfo(
            $lat ? (float)$lat : null,
            $lng ? (float)$lng : null
        );
        
        // 데이터가 없으면 빈 값 반환
        if ($weather['temperature'] === null && $weather['sky'] === null) {
            return response()->json([
                'success' => false,
                'data' => null,
            ]);
        }
        
        // 아이콘 경로 추가 (값이 있을 때만)
        if ($weather['sky'] !== null && $weather['pty'] !== null) {
            $icon = $this->weatherService->getWeatherIcon(
                $weather['sky'],
                $weather['pty']
            );
            if ($icon) {
                $weather['icon'] = $icon;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $weather,
        ]);
    }

    /**
     * 항공권 파일 다운로드 (사용자용)
     */
    public function downloadTicketFile()
    {
        $member = session('member');
        if (!$member || !isset($member['id'])) {
            abort(401, '로그인이 필요합니다.');
        }

        $memberModel = Member::findOrFail($member['id']);
        
        if (!$memberModel->ticket_file) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        $filePath = storage_path('app/public/' . $memberModel->ticket_file);
        
        if (!file_exists($filePath)) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        // 저장된 파일명에서 원본 파일명 추출 (타임스탬프 제거)
        $storedFileName = basename($memberModel->ticket_file);
        $originalFileName = preg_replace('/_\d+\./', '.', $storedFileName);
        
        return response()->download($filePath, $originalFileName);
    }
}