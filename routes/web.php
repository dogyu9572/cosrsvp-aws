<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\FindIdController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\Backoffice\PopupController;

// =============================================================================
// 기본 라우트 파일
// =============================================================================

// 메인 페이지
Route::get('/', [HomeController::class, 'index'])->name('home');

// 스케줄 페이지
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');

// 공지사항 페이지
Route::get('/notices', [NoticeController::class, 'index'])->name('notices');
Route::get('/notices/{id}', [NoticeController::class, 'show'])->name('notices.show');

// 갤러리 페이지
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/gallery/{id}', [GalleryController::class, 'show'])->name('gallery.show');

// FAQ 페이지
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// 문의사항 페이지
Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries');
Route::get('/inquiries/create', [InquiryController::class, 'create'])->name('inquiries.create');
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
Route::get('/inquiries/{id}', [InquiryController::class, 'show'])->name('inquiries.show');

// 팝업 표시 (일반 팝업용)
Route::get('/popup/{popup}', [PopupController::class, 'showPopup'])->name('popup.show');

// 로그인 (사용자 페이지)
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

// 아이디 찾기
Route::get('/find-id', [FindIdController::class, 'showForm'])->name('find-id');
Route::post('/find-id', [FindIdController::class, 'findId']);
Route::get('/find-id/result', [FindIdController::class, 'showResult'])->name('find-id.result');

// 비밀번호 찾기
Route::get('/find-pw', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('find-pw');
Route::post('/find-pw', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// 마이페이지 (로그인 필수)
Route::get('/mypage', [UserController::class, 'mypage'])->name('mypage');
Route::post('/mypage', [UserController::class, 'update'])->name('mypage.update');

// 알람 페이지 (로그인 필수)
Route::get('/alarms', [AlarmController::class, 'index'])->name('alarms');
Route::get('/alarms/{id}', [AlarmController::class, 'show'])->name('alarms.show');

// 인증 관련 라우트
Route::prefix('auth')->name('auth.')->group(function () {

    // 회원가입
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // 비밀번호 재설정
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
            ->name('request');
        Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('email');
        Route::get('/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
            ->name('reset');
        Route::post('/reset', [ResetPasswordController::class, 'reset'])
            ->name('update');
    });
});

// =============================================================================
// 분리된 라우트 파일들 포함
// =============================================================================

// 백오피스 라우트 (관리자 전용)
require __DIR__.'/backoffice.php';