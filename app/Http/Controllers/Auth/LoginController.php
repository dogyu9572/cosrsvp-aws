<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * 로그인 폼 표시
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * 로그인 처리
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        // login_id로 회원 찾기
        $member = Member::where('login_id', $request->login_id)
            ->where('is_active', true)
            ->first();

        if (!$member) {
            throw ValidationException::withMessages([
                'login_id' => [trans('auth.failed')],
            ]);
        }

        // 비밀번호 확인
        if (!Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'login_id' => [trans('auth.failed')],
            ]);
        }

        // 세션에 회원 정보 저장
        $request->session()->put('member_id', $member->id);
        $request->session()->put('member', $member->toArray());
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * 로그아웃 처리
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->forget('member_id');
        $request->session()->forget('member');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
