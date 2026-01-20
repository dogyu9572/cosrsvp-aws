<?php

namespace App\Http\Controllers;

use App\Models\AccessCode;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KofihAuthController extends Controller
{
    /**
     * 코드 입력 페이지 표시
     */
    public function showLoginForm()
    {
        return view('kofih.login');
    }

    /**
     * 코드 인증 처리
     */
    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // 코드 검증 (대소문자 구분 없음)
        $accessCode = AccessCode::validateCode($request->code);

        if (!$accessCode) {
            throw ValidationException::withMessages([
                'code' => ['유효하지 않은 코드입니다.'],
            ]);
        }

        // 세션에 인증 정보 저장
        $request->session()->put('kofih_authenticated', true);
        $request->session()->put('kofih_access_code_id', $accessCode->id);
        $request->session()->regenerate();

        return redirect()->intended('/kofih/')->with('success', '코드 인증이 완료되었습니다.');
    }

    /**
     * 로그아웃 처리
     */
    public function logout(Request $request)
    {
        $request->session()->forget('kofih_authenticated');
        $request->session()->forget('kofih_access_code_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/kofih/login');
    }
}