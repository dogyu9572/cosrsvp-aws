<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ProjectTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FindIdController extends Controller
{
    /**
     * 아이디 찾기 폼 표시
     */
    public function showForm()
    {
        // 프로젝트 기수 목록 조회
        $projectTerms = ProjectTerm::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('auth.find-id', compact('projectTerms'));
    }

    /**
     * 아이디 찾기 처리
     */
    public function findId(Request $request)
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

            // 아이디 찾기 결과 페이지로 리다이렉트
            return redirect()->route('find-id.result')
                ->with('login_id', $member->login_id);

        } catch (\Exception $e) {
            Log::error("아이디 찾기 오류: " . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => '아이디 찾기 중 오류가 발생했습니다.']);
        }
    }

    /**
     * 아이디 찾기 결과 표시
     */
    public function showResult()
    {
        $loginId = session('login_id');

        if (!$loginId) {
            return redirect()->route('find-id')
                ->with('error', '아이디 찾기를 먼저 진행해주세요.');
        }

        return view('auth.find-id-result', compact('loginId'));
    }
}
