<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class KofihMypageController extends Controller
{
    /**
     * 마이페이지 표시
     */
    public function index(Request $request)
    {
        $memberId = $request->get('member_id');
        
        if (!$memberId) {
            return redirect()->route('kofih.member.index')
                ->with('error', '회원을 선택해주세요.');
        }
        
        $member = Member::with([
            'projectTerm',
            'course',
            'operatingInstitution',
            'projectPeriod',
            'country'
        ])->findOrFail($memberId);

        return view('kofih.mypage.index', compact('member'));
    }
}