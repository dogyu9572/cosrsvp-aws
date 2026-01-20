<?php

namespace App\Http\Controllers;

use App\Services\Backoffice\MemberService;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;

class KofihMemberController extends Controller
{
    public function __construct(
        private MemberService $memberService
    ) {}

    /**
     * 회원 목록 표시
     */
    public function index(Request $request)
    {
        // 회원 목록 조회 (백오피스 서비스 재사용)
        $members = $this->memberService->getMembersWithFilters($request);
        
        // 필터 옵션 데이터
        $projectTerms = ProjectTerm::active()->orderBy('display_order')->get();
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();

        return view('kofih.member', compact(
            'members',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries'
        ));
    }
}