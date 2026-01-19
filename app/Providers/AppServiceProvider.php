<?php

namespace App\Providers;

use App\Models\AdminMenu;
use App\Models\Setting;
use App\Models\Course;
use App\Models\OperatingInstitution;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HTTPS 강제 (.env의 APP_URL이 https://로 시작하는 경우)
        $applicationUrl = config('app.url');
        if (is_string($applicationUrl) && str_starts_with($applicationUrl, 'https://')) {
            URL::forceScheme('https');
        }

        // 백오피스 경로에서 현재 메뉴 정보를 뷰에 공유
        if (Request::is('backoffice*')) {
            View::composer('*', function ($view) {
                $currentPath = Request::path();
                $currentMenu = AdminMenu::getCurrentMenu($currentPath);

                // 현재 메뉴가 있으면 타이틀 생성, 없으면 기본 타이틀 사용
                $menuTitle = $currentMenu ? $currentMenu->name : '백오피스';
                $title = "백오피스 - {$menuTitle}";

                $view->with('menuTitle', $menuTitle);
                $view->with('title', $title);
                
                // 사이드바 데이터 추가 (모든 페이지에서 공통 사용)
                $view->with('siteTitle', Setting::getValue('site_title', '관리자'));
                
                // 사용자 권한에 따른 메뉴 필터링
                $user = Auth::user();
                if ($user && $user->isSuperAdmin()) {
                    // 슈퍼 관리자는 모든 메뉴 표시
                    $mainMenus = AdminMenu::getMainMenus();
                } elseif ($user) {
                    // 일반 관리자는 권한 있는 메뉴만 표시
                    $accessibleMenuIds = $user->accessibleMenus()->pluck('admin_menus.id')->toArray();
                    
                    // 부모 메뉴 가져오기 (자식 메뉴는 eager loading하지 않음)
                    $mainMenus = AdminMenu::whereNull('parent_id')
                        ->where('is_active', true)
                        ->orderBy('order')
                        ->get()
                        ->filter(function ($menu) use ($accessibleMenuIds) {
                            // 부모 메뉴 자체에 권한이 있는지 확인
                            $hasParentPermission = in_array($menu->id, $accessibleMenuIds);
                            
                            // 권한이 있는 자식 메뉴만 필터링하여 로드
                            $filteredChildren = AdminMenu::where('parent_id', $menu->id)
                                ->where('is_active', true)
                                ->orderBy('order')
                                ->get()
                                ->filter(function ($child) use ($accessibleMenuIds) {
                                    return in_array($child->id, $accessibleMenuIds);
                                });
                            
                            // 자식 메뉴를 필터링된 것으로 교체
                            $menu->setRelation('children', $filteredChildren);
                            
                            // 부모 메뉴 권한이 있거나, 권한 있는 자식 메뉴가 하나라도 있으면 표시
                            return $hasParentPermission || $filteredChildren->count() > 0;
                        });
                } else {
                    $mainMenus = collect();
                }
                
                $view->with('mainMenus', $mainMenus);
            });
        }

        // 사용자 헤더/푸터 공통 데이터 바인딩
        View::composer(['components.user-header', 'components.user-footer'], function ($view) {
            $member = session('member', null);
            
            // 회원 기본 정보
            $memberName = $member['name'] ?? 'Hong Gil-dong';
            $memberAffiliation = 'Basic Medicine_Korea University'; // 기본값
            
            // 담당자 정보 기본값
            $kofhiManagerName = 'Noh Yeon';
            $kofhiManagerPhone = '010-4660-9460';
            $kofhiManagerEmail = 'rohyoun@inje.ac.kr';
            $cosmojinManagerName = 'Kim Young-hee';
            $cosmojinManagerPhone = '010-1111-2222';
            $cosmojinManagerEmail = 'staff2@email.com';
            
            // 과정_운영기관 정보 조회
            $course = null;
            $operatingInstitution = null;
            
            if ($member && isset($member['course_id'])) {
                $course = Course::find($member['course_id']);
            }
            
            if ($member && isset($member['operating_institution_id'])) {
                $operatingInstitution = OperatingInstitution::find($member['operating_institution_id']);
            }
            
            // 과정_운영기관 정보로 소속 설정
            if ($course && $operatingInstitution) {
                $courseName = $course->name_en ?: $course->name_ko;
                $institutionName = $operatingInstitution->name_en ?: $operatingInstitution->name_ko;
                $memberAffiliation = $courseName . '_' . $institutionName;
            } elseif ($course) {
                $memberAffiliation = $course->name_en ?: $course->name_ko;
            } elseif ($operatingInstitution) {
                $memberAffiliation = $operatingInstitution->name_en ?: $operatingInstitution->name_ko;
            } elseif ($member && isset($member['affiliation'])) {
                $memberAffiliation = $member['affiliation'];
            }
            
            // 운영기관 담당자 정보 설정
            if ($operatingInstitution) {
                if ($operatingInstitution->kofhi_manager_name) {
                    $kofhiManagerName = $operatingInstitution->kofhi_manager_name;
                }
                if ($operatingInstitution->kofhi_manager_phone) {
                    $kofhiManagerPhone = $operatingInstitution->kofhi_manager_phone;
                }
                if ($operatingInstitution->kofhi_manager_email) {
                    $kofhiManagerEmail = $operatingInstitution->kofhi_manager_email;
                }
                if ($operatingInstitution->cosmojin_manager_name) {
                    $cosmojinManagerName = $operatingInstitution->cosmojin_manager_name;
                }
                if ($operatingInstitution->cosmojin_manager_phone) {
                    $cosmojinManagerPhone = $operatingInstitution->cosmojin_manager_phone;
                }
                if ($operatingInstitution->cosmojin_manager_email) {
                    $cosmojinManagerEmail = $operatingInstitution->cosmojin_manager_email;
                }
            }
            
            $view->with([
                'memberName' => $memberName,
                'memberAffiliation' => $memberAffiliation,
                'kofhiManagerName' => $kofhiManagerName,
                'kofhiManagerPhone' => $kofhiManagerPhone,
                'kofhiManagerEmail' => $kofhiManagerEmail,
                'cosmojinManagerName' => $cosmojinManagerName,
                'cosmojinManagerPhone' => $cosmojinManagerPhone,
                'cosmojinManagerEmail' => $cosmojinManagerEmail,
            ]);
        });

        // 쿼리 로깅 활성화 (디버깅용)
        if (config('app.debug')) {
            DB::listen(function ($query) {
                // 로깅 제외할 테이블 목록
                $excludedTables = [
                    'daily_visitor_stats',
                    'visitor_logs',
                    'sessions',
                ];
                
                // 제외할 테이블이 포함된 쿼리는 로깅하지 않음
                $shouldLog = true;
                foreach ($excludedTables as $table) {
                    if (stripos($query->sql, $table) !== false) {
                        $shouldLog = false;
                        break;
                    }
                }
                
                if ($shouldLog) {
                    Log::info(
                        'SQL 쿼리 실행',
                        [
                            'sql' => $query->sql,
                            'bindings' => $query->bindings,
                            'time' => $query->time
                        ]
                    );
                }
            });
        }
    }
}
