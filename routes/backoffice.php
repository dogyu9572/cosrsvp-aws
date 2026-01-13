<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\AdminMenuController;
use App\Http\Controllers\Backoffice\CategoryController;
use App\Http\Controllers\Backoffice\SettingController;
use App\Http\Controllers\Backoffice\BoardController;
use App\Http\Controllers\Backoffice\BoardTemplateController;
use App\Http\Controllers\Backoffice\BoardSkinController;
use App\Http\Controllers\Backoffice\BoardPostController;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\LogController;
use App\Http\Controllers\Backoffice\AdminController;
use App\Http\Controllers\Backoffice\AdminGroupController;
use App\Http\Controllers\Backoffice\BannerController;
use App\Http\Controllers\Backoffice\PopupController;
use App\Http\Controllers\Backoffice\AccessStatisticsController;
use App\Http\Controllers\Backoffice\ProjectTermController;
use App\Http\Controllers\Backoffice\CourseController;
use App\Http\Controllers\Backoffice\OperatingInstitutionController;
use App\Http\Controllers\Backoffice\ProjectPeriodController;
use App\Http\Controllers\Backoffice\CountryController;
use App\Http\Controllers\Backoffice\ScheduleController;

// =============================================================================
// 백오피스 인증 라우트
// =============================================================================
Route::prefix('backoffice')->name('backoffice.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// =============================================================================
// 백오피스 라우트 (관리자 전용)
// =============================================================================

Route::prefix('backoffice')->middleware(['backoffice'])->group(function () {
    
    // 대시보드
    Route::get('/', [App\Http\Controllers\Backoffice\DashboardController::class, 'index'])
        ->name('backoffice.dashboard');
    
    // 대시보드 API
    Route::get('/api/statistics', [App\Http\Controllers\Backoffice\DashboardController::class, 'statistics'])
        ->name('backoffice.api.statistics');

    // -------------------------------------------------------------------------
    // 시스템 관리
    // -------------------------------------------------------------------------

    // 관리자 메뉴 관리
    Route::resource('admin-menus', AdminMenuController::class, [
        'names' => 'backoffice.admin-menus'
    ])->except(['show']);

    // 메뉴 순서 업데이트
    Route::post('admin-menus/update-order', [AdminMenuController::class, 'updateOrder'])
        ->name('backoffice.admin-menus.update-order');
    
    // 메뉴 부모 업데이트 (드래그로 메뉴 이동)
    Route::post('admin-menus/update-parent', [AdminMenuController::class, 'updateParent'])
        ->name('backoffice.admin-menus.update-parent');

    // 카테고리 관리
    // 카테고리 순서 업데이트 (resource 라우트보다 앞에 위치)
    Route::post('categories/update-order', [CategoryController::class, 'updateOrder'])
        ->name('backoffice.categories.update-order');

    // 활성 카테고리 조회 (AJAX - resource 라우트보다 앞에 위치)
    Route::get('categories/active/{group}', [CategoryController::class, 'getActiveCategories'])
        ->name('backoffice.categories.active');

    // 특정 그룹의 1차 카테고리 조회 (AJAX)
    Route::get('categories/get-by-group/{groupId}', [CategoryController::class, 'getByGroup'])
        ->name('backoffice.categories.get-by-group');

    // 카테고리 수정용 데이터 조회 (AJAX)
    Route::get('categories/{category}/edit-data', [CategoryController::class, 'getEditData'])
        ->name('backoffice.categories.edit-data');

    // 인라인 수정 (AJAX)
    Route::post('categories/{category}/update-inline', [CategoryController::class, 'updateInline'])
        ->name('backoffice.categories.update-inline');

    // 모달 등록 (AJAX)
    Route::post('categories/store-modal', [CategoryController::class, 'storeModal'])
        ->name('backoffice.categories.store-modal');

    // 모달 수정 (AJAX)
    Route::put('categories/update-modal', [CategoryController::class, 'updateModal'])
        ->name('backoffice.categories.update-modal');

    // 미리 생성될 코드 조회 (AJAX)
    Route::post('categories/generate-preview-code', [CategoryController::class, 'generatePreviewCode'])
        ->name('backoffice.categories.generate-preview-code');

    Route::resource('categories', CategoryController::class, [
        'names' => 'backoffice.categories'
    ])->except(['show']);

    // 기본설정 관리
    Route::get('setting', [SettingController::class, 'index'])
        ->name('backoffice.setting.index');
    Route::post('setting', [SettingController::class, 'update'])
        ->name('backoffice.setting.update');

    // 접속 로그 관리
    Route::get('logs/access', [LogController::class, 'access'])
        ->name('backoffice.logs.access');
    Route::get('user-access-logs', [LogController::class, 'userAccessLogs'])
        ->name('backoffice.user-access-logs');
    Route::get('admin-access-logs', [LogController::class, 'adminAccessLogs'])
        ->name('backoffice.admin-access-logs');
    
    // 통계 관리
    Route::get('access-statistics', [AccessStatisticsController::class, 'index'])
        ->name('backoffice.access-statistics');
    Route::get('access-statistics/get-statistics', [AccessStatisticsController::class, 'getStatistics'])
        ->name('backoffice.access-statistics.get-statistics');

    // 관리자 계정 관리
    Route::post('admins/bulk-destroy', [AdminController::class, 'bulkDestroy'])
        ->name('backoffice.admins.bulk-destroy');
    Route::post('admins/check-login-id', [AdminController::class, 'checkLoginId'])
        ->name('backoffice.admins.check-login-id');
    Route::resource('admins', AdminController::class, [
        'names' => 'backoffice.admins'
    ]);

    // 관리자 권한 그룹 관리
    Route::resource('admin-groups', AdminGroupController::class, [
        'names' => 'backoffice.admin-groups'
    ])->except(['show']);

    // 권한 그룹 권한 설정
    Route::get('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'editPermissions'])
        ->name('backoffice.admin-groups.permissions.edit');
    Route::post('admin-groups/{admin_group}/permissions', [AdminGroupController::class, 'updatePermissions'])
        ->name('backoffice.admin-groups.permissions.update');

    // -------------------------------------------------------------------------
    // 콘텐츠 관리
    // -------------------------------------------------------------------------

    // 이미지 업로드
    Route::post('upload-image', function (Request $request) {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/editor', 'public');

            return response()->json([
                'uploaded' => true,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'uploaded' => false,
            'error' => ['message' => '이미지 업로드에 실패했습니다.']
        ]);
    });

    // 정렬 순서 업데이트
    Route::post('board-posts/update-sort-order', [BoardPostController::class, 'updateSortOrder'])->name('backoffice.board-posts.update-sort-order');

    // 게시글 관리 (특정 게시판)
    Route::prefix('board-posts/{slug}')->name('backoffice.board-posts.')->group(function () {
        Route::get('/', [BoardPostController::class, 'index'])->name('index');
        Route::get('/create', [BoardPostController::class, 'create'])->name('create');
        Route::post('/', [BoardPostController::class, 'store'])->name('store');
        Route::get('/{post}', [BoardPostController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [BoardPostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [BoardPostController::class, 'update'])->name('update');
        Route::delete('/{post}', [BoardPostController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-destroy', [BoardPostController::class, 'bulkDestroy'])->name('bulk_destroy');
    });

    // 게시판 관리
    Route::resource('boards', BoardController::class, [
        'names' => 'backoffice.boards'
    ])->except(['show']); // show는 제외 (게시글 목록과 충돌)

    // 게시판 템플릿 관리
    Route::resource('board-templates', BoardTemplateController::class, [
        'names' => 'backoffice.board-templates',
        'parameters' => ['board-templates' => 'boardTemplate']
    ]);

    // 게시판 템플릿 추가 기능
    Route::post('board-templates/{boardTemplate}/duplicate', [BoardTemplateController::class, 'duplicate'])
        ->name('backoffice.board-templates.duplicate');
    Route::get('board-templates/{boardTemplate}/data', [BoardTemplateController::class, 'getTemplateData'])
        ->name('backoffice.board-templates.data');

    // 게시판 스킨 관리
    Route::resource('board-skins', BoardSkinController::class, [
        'names' => 'backoffice.board-skins',
        'parameters' => ['board-skins' => 'boardSkin']
    ]);

    // 게시판 스킨 템플릿 편집
    Route::prefix('board-skins/{boardSkin}')->name('backoffice.board-skins.')->group(function () {
        Route::get('template', [BoardSkinController::class, 'editTemplate'])
            ->name('edit_template');
        Route::post('template', [BoardSkinController::class, 'updateTemplate'])
            ->name('update_template');
    });

    // 게시글 관리
    Route::resource('posts', BoardPostController::class, [
        'names' => 'backoffice.posts'
    ]);

    // 회원 관리
    Route::resource('users', UserController::class, [
        'names' => 'backoffice.users'
    ]);

    // 배너 관리
    Route::resource('banners', BannerController::class, [
        'names' => 'backoffice.banners'
    ]);
    Route::post('banners/update-order', [BannerController::class, 'updateOrder'])->name('backoffice.banners.update-order');

    // 팝업 관리
    Route::resource('popups', PopupController::class, [
        'names' => 'backoffice.popups'
    ]);
    Route::post('popups/update-order', [PopupController::class, 'updateOrder'])->name('backoffice.popups.update-order');

    // 세션 연장
    Route::post('session/extend', [App\Http\Controllers\Backoffice\SessionController::class, 'extend'])
        ->name('backoffice.session.extend');

    // -------------------------------------------------------------------------
    // 프로젝트 관리
    // -------------------------------------------------------------------------

    // 프로젝트 기수 관리
    Route::post('project-terms/update-order', [ProjectTermController::class, 'updateOrder'])
        ->name('backoffice.project-terms.update-order');
    Route::resource('project-terms', ProjectTermController::class, [
        'names' => 'backoffice.project-terms'
    ])->except(['create']);

    // 과정 관리 (AJAX)
    Route::post('courses/update-order', [CourseController::class, 'updateOrder'])
        ->name('backoffice.courses.update-order');
    Route::get('courses/get-by-term/{termId}', [CourseController::class, 'getByTerm'])
        ->name('backoffice.courses.get-by-term');
    Route::get('courses/{course}', [CourseController::class, 'show'])
        ->name('backoffice.courses.show');
    Route::resource('courses', CourseController::class, [
        'names' => 'backoffice.courses'
    ])->except(['index', 'show', 'create', 'edit']);

    // 운영기관 관리 (AJAX)
    Route::post('operating-institutions/update-order', [OperatingInstitutionController::class, 'updateOrder'])
        ->name('backoffice.operating-institutions.update-order');
    Route::get('operating-institutions/get-by-course/{courseId}', [OperatingInstitutionController::class, 'getByCourse'])
        ->name('backoffice.operating-institutions.get-by-course');
    Route::get('operating-institutions/{operatingInstitution}', [OperatingInstitutionController::class, 'show'])
        ->name('backoffice.operating-institutions.show');
    Route::resource('operating-institutions', OperatingInstitutionController::class, [
        'names' => 'backoffice.operating-institutions'
    ])->except(['index', 'show', 'create', 'edit']);

    // 프로젝트기간 관리 (AJAX)
    Route::post('project-periods/update-order', [ProjectPeriodController::class, 'updateOrder'])
        ->name('backoffice.project-periods.update-order');
    Route::get('project-periods/get-by-institution/{institutionId}', [ProjectPeriodController::class, 'getByInstitution'])
        ->name('backoffice.project-periods.get-by-institution');
    Route::get('project-periods/{projectPeriod}', [ProjectPeriodController::class, 'show'])
        ->name('backoffice.project-periods.show');
    Route::resource('project-periods', ProjectPeriodController::class, [
        'names' => 'backoffice.project-periods'
    ])->except(['index', 'show', 'create', 'edit']);

    // 국가 관리 (AJAX)
    Route::post('countries/update-order', [CountryController::class, 'updateOrder'])
        ->name('backoffice.countries.update-order');
    Route::get('countries/get-by-period/{periodId}', [CountryController::class, 'getByPeriod'])
        ->name('backoffice.countries.get-by-period');
    Route::get('countries/{country}', [CountryController::class, 'show'])
        ->name('backoffice.countries.show');
    Route::resource('countries', CountryController::class, [
        'names' => 'backoffice.countries'
    ])->except(['index', 'show', 'create', 'edit']);

    // 일정 관리 (AJAX)
    Route::post('schedules/update-order', [ScheduleController::class, 'updateOrder'])
        ->name('backoffice.schedules.update-order');
    Route::get('schedules/get-by-country/{countryId}', [ScheduleController::class, 'getByCountry'])
        ->name('backoffice.schedules.get-by-country');
    Route::get('schedules/{schedule}', [ScheduleController::class, 'show'])
        ->name('backoffice.schedules.show');
    Route::resource('schedules', ScheduleController::class, [
        'names' => 'backoffice.schedules'
    ])->except(['index', 'show', 'create', 'edit']);
});
