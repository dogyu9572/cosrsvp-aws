<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ProjectTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * 비밀번호 재설정 이메일 요청 폼 표시
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        // 프로젝트 기수 목록 조회
        $projectTerms = ProjectTerm::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('auth.passwords.email', compact('projectTerms'));
    }

    /**
     * 비밀번호 재설정 이메일 발송
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 유효성 검사
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'project_term_id' => 'required|integer|exists:project_terms,id',
            'email' => 'required|email|max:255',
        ]);

        try {
            // 회원 조회
            $member = Member::where('name', $validated['name'])
                ->where('project_term_id', $validated['project_term_id'])
                ->where('email', $validated['email'])
                ->whereNull('deleted_at')
                ->where('is_active', true)
                ->first();

            if (!$member) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => '입력하신 정보와 일치하는 회원을 찾을 수 없습니다.']);
            }

            // 이메일로 비밀번호 재설정 링크 발송
            // Member는 User 모델이 아니므로, 직접 처리하거나
            // Password::broker('members')를 사용해야 할 수 있습니다.
            // 일단 기본 Password 리셋을 사용하되, 이메일 주소로만 전송
            $status = Password::sendResetLink(
                ['email' => $validated['email']]
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', '비밀번호 재설정 링크가 이메일로 발송되었습니다.');
            }

            return back()
                ->withInput()
                ->withErrors(['email' => '비밀번호 재설정 링크 발송에 실패했습니다.']);

        } catch (\Exception $e) {
            Log::error("비밀번호 재설정 요청 오류: " . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => '비밀번호 재설정 중 오류가 발생했습니다.']);
        }
    }
}
