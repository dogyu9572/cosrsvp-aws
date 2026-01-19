<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\Popup;
use App\Models\Banner;
use App\Services\ExchangeRateService;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $exchangeRateService;
    protected $weatherService;

    public function __construct(ExchangeRateService $exchangeRateService, WeatherService $weatherService)
    {
        $this->exchangeRateService = $exchangeRateService;
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        $gNum = "main";
        $gName = "";
        $sName = "";
        
        // 로그인한 회원의 프로젝트 관련 정보
        $member = session('member');
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
        
        // notices 게시판 최신글 3개 - 프로젝트 필터링 적용
        $noticePosts = $this->getLatestPostsWithFilter('notices', 3, $memberProjectInfo);
        
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
        
        return view('home.index', compact('gNum', 'gName', 'sName', 'galleryPosts', 'topNotice', 'noticePosts', 'popups', 'banners'));
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
     * 기수, 과정, 운영기관, 프로젝트기간, 국가 전부 일치하는 값만 가져옵니다.
     */
    private function getLatestPostsWithFilter($boardSlug, $limit = 4, $memberProjectInfo = [])
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

            // 필터링 후 추가 검증: PHP 레벨에서 모든 프로젝트 관련 필드가 일치하는 것만 필터링
            $filteredPosts = $posts->filter(function ($post) use ($memberProjectInfo) {
                $customFields = json_decode($post->custom_fields ?? '{}', true);
                
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
}