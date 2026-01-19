<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateMypageRequest;
use App\Models\Member;
use App\Services\Backoffice\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    /**
     * 사용자 서비스 주입
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 사용자 프로필 화면 표시
     */
    public function profile()
    {
        return view('users.profile', [
            'user' => $this->userService->getCurrentUser()
        ]);
    }

    /**
     * 사용자 프로필 업데이트
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userService->getCurrentUser();
        
        $this->userService->updateProfile($user, $request->validated());

        return redirect()->route('profile')->with('status', '프로필이 성공적으로 업데이트되었습니다.');
    }

    /**
     * 마이페이지 화면 표시
     */
    public function mypage()
    {
        $memberId = session('member_id');
        
        if (!$memberId) {
            return redirect()->route('login')->with('error', '로그인이 필요합니다.');
        }

        $member = Member::with(['projectTerm', 'course', 'operatingInstitution', 'projectPeriod', 'country'])
            ->findOrFail($memberId);

        return view('mypage.index', compact('member'));
    }

    /**
     * 마이페이지 정보 업데이트
     */
    public function update(UpdateMypageRequest $request)
    {
        $memberId = session('member_id');
        
        if (!$memberId) {
            return redirect()->route('login')->with('error', '로그인이 필요합니다.');
        }

        $member = Member::findOrFail($memberId);
        
        $data = $request->validated();
        
        // 날짜 필드 변환 (jQuery UI datepicker는 yy.mm.dd 형식으로 전송됨)
        if (isset($data['birth_date']) && $data['birth_date']) {
            try {
                $data['birth_date'] = \Carbon\Carbon::createFromFormat('Y.m.d', $data['birth_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // 변환 실패 시 원본 유지
            }
        }
        
        if (isset($data['passport_expiry']) && $data['passport_expiry']) {
            try {
                $data['passport_expiry'] = \Carbon\Carbon::createFromFormat('Y.m.d', $data['passport_expiry'])->format('Y-m-d');
            } catch (\Exception $e) {
                // 변환 실패 시 원본 유지
            }
        }
        
        if (isset($data['alien_registration_expiry']) && $data['alien_registration_expiry']) {
            try {
                $data['alien_registration_expiry'] = \Carbon\Carbon::createFromFormat('Y.m.d', $data['alien_registration_expiry'])->format('Y-m-d');
            } catch (\Exception $e) {
                // 변환 실패 시 원본 유지
            }
        }

        $member->update($data);
        
        // 세션의 member 정보도 업데이트
        session()->put('member', $member->fresh()->toArray());

        return redirect()->route('mypage')->with('success', '정보가 성공적으로 업데이트되었습니다.');
    }
}
