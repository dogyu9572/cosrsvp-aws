<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Services\Backoffice\MemberService;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * 회원 목록 표시
     */
    public function index(Request $request)
    {
        $members = $this->memberService->getMembersWithFilters($request);
        
        // 필터 옵션
        $projectTerms = ProjectTerm::active()->orderBy('display_order')->get();
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();

        return view('backoffice.members.index', compact(
            'members',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries'
        ));
    }

    /**
     * 회원 생성 폼 표시
     */
    public function create()
    {
        $projectTerms = ProjectTerm::active()->orderBy('display_order')->get();
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();

        return view('backoffice.members.create', compact(
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries'
        ));
    }

    /**
     * 회원 저장
     */
    public function store(StoreMemberRequest $request)
    {
        $data = $request->validated();
        
        // 파일 업로드 처리
        if ($request->hasFile('ticket_file')) {
            $file = $request->file('ticket_file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '.' . $extension;
            $data['ticket_file'] = $file->storeAs('members/tickets', $fileName, 'public');
        }
        
        $this->memberService->createMember($data);

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원이 추가되었습니다.');
    }

    /**
     * 회원 상세 정보 표시 (edit 페이지로 리다이렉트)
     */
    public function show($id)
    {
        return redirect()->route('backoffice.members.edit', $id);
    }

    /**
     * 회원 수정 폼 표시
     */
    public function edit($id)
    {
        $member = $this->memberService->getMemberDetail($id);
        $projectTerms = ProjectTerm::active()->orderBy('display_order')->get();
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();

        // 회원의 국가 정보에서 서류명, 제출마감일 조회
        $countryDocument = null;
        if ($member->country_id) {
            $country = Country::find($member->country_id);
            if ($country) {
                $countryDocument = (object) [
                    'document_name' => $country->document_name,
                    'submission_deadline' => $country->submission_deadline,
                ];
            }
        }

        return view('backoffice.members.edit', compact(
            'member',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries',
            'countryDocument'
        ));
    }

    /**
     * 회원 정보 업데이트
     */
    public function update(UpdateMemberRequest $request, $id)
    {
        $member = Member::findOrFail($id);
        $data = $request->validated();
        
        // 비밀번호가 비어있으면 제거
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        // 빈 문자열을 null로 변환 (nullable 필드들)
        $nullableFields = [
            'gender', 'email', 'phone_kr', 'phone_local', 'birth_date',
            'occupation', 'major', 'affiliation', 'department', 'position',
            'passport_number', 'passport_expiry', 'alien_registration_number', 'alien_registration_expiry',
            'project_term_id', 'course_id', 'operating_institution_id', 'project_period_id', 'country_id',
            'hotel_name', 'hotel_address', 'hotel_address_detail', 'training_period', 'visa_type',
            'cultural_experience', 'account_info', 'insurance_status', 'clothing_size',
            'dietary_restrictions', 'special_requests', 'departure_location', 'arrival_location',
            'entry_date', 'exit_date', 'entry_flight', 'exit_flight'
        ];
        
        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }
        
        // 파일 업로드 처리
        if ($request->hasFile('ticket_file')) {
            // 기존 파일 삭제
            if ($member->ticket_file) {
                Storage::disk('public')->delete($member->ticket_file);
            }
            $file = $request->file('ticket_file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '.' . $extension;
            $data['ticket_file'] = $file->storeAs('members/tickets', $fileName, 'public');
        }

        // 보완요청 처리
        if ($request->has('supplement_request') && is_array($request->supplement_request)) {
            foreach ($request->supplement_request as $documentId => $supplementContent) {
                $document = MemberDocument::find($documentId);
                if ($document && $document->member_id == $member->id) {
                    // 보완요청 내용이 있으면 저장하고 상태 변경
                    if (!empty(trim($supplementContent))) {
                        $document->supplement_request_content = trim($supplementContent);
                        $document->status = 'supplement_requested';
                        $document->save();
                    }
                }
            }
        }

        $this->memberService->updateMember($member, $data, Auth::id());

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원 정보가 수정되었습니다.');
    }

    /**
     * 회원 삭제
     */
    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $this->memberService->deleteMember($member);

        return redirect()->route('backoffice.members.index')
            ->with('success', '회원이 삭제되었습니다.');
    }

    /**
     * 비밀번호 초기화
     */
    public function resetPassword($id)
    {
        $member = Member::findOrFail($id);
        $this->memberService->resetPassword($member, Auth::id());

        return redirect()->back()
            ->with('success', '비밀번호가 초기화되었습니다. (기본 비밀번호: COS1234)');
    }

    /**
     * 선택 회원에게 메일 발송
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'integer|exists:members,id',
            'mail_list_id' => 'required|integer',
        ]);

        $memberIds = $request->input('member_ids');
        $mailListId = $request->input('mail_list_id');

        $this->memberService->sendEmailToMembers($memberIds, $mailListId);

        return redirect()->back()
            ->with('success', '메일이 발송되었습니다.');
    }

    /**
     * 프로젝트 기수별 회원 조회 (AJAX)
     */
    public function getMembersByProjectTerm(Request $request)
    {
        $filters = $request->only([
            'project_term_id',
            'course_id',
            'operating_institution_id',
            'project_period_id',
            'country_id'
        ]);

        $members = $this->memberService->getMembersByProjectTerm($filters);

        return response()->json($members);
    }

    /**
     * 항공권 파일 다운로드
     */
    public function downloadTicketFile($id)
    {
        $member = Member::findOrFail($id);
        
        if (!$member->ticket_file) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        $filePath = storage_path('app/public/' . $member->ticket_file);
        
        if (!file_exists($filePath)) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        // 저장된 파일명에서 원본 파일명 추출 (타임스탬프 제거)
        $storedFileName = basename($member->ticket_file);
        $originalFileName = preg_replace('/_\d+\./', '.', $storedFileName);
        
        return response()->download($filePath, $originalFileName);
    }

    /**
     * 항공권 파일 삭제
     */
    public function deleteTicketFile($id)
    {
        $member = Member::findOrFail($id);
        
        if (!$member->ticket_file) {
            return response()->json([
                'success' => false,
                'message' => '삭제할 파일이 없습니다.'
            ], 404);
        }

        try {
            $oldFileName = basename($member->ticket_file);
            Storage::disk('public')->delete($member->ticket_file);
            
            // updateMember를 사용하여 로그 기록
            $this->memberService->updateMember($member, ['ticket_file' => null], Auth::id());

            return response()->json([
                'success' => true,
                'message' => '항공권 파일이 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '파일 삭제 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * 보완요청 처리 (AJAX)
     */
    public function supplementRequest(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        
        $request->validate([
            'document_id' => 'required|exists:member_documents,id',
            'supplement_content' => 'required|string',
        ]);

        try {
            $document = MemberDocument::findOrFail($request->document_id);
            
            // 회원의 문서인지 확인
            if ($document->member_id != $member->id) {
                return response()->json([
                    'success' => false,
                    'message' => '권한이 없습니다.'
                ], 403);
            }

            // 보완요청 내용 저장 및 상태 변경
            $document->supplement_request_content = trim($request->supplement_content);
            $document->status = 'supplement_requested';
            $document->save();

            return response()->json([
                'success' => true,
                'message' => '보완요청이 완료되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '보완요청 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * 완료 처리 (AJAX)
     */
    public function completeRequest(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        
        $request->validate([
            'document_id' => 'required|exists:member_documents,id',
        ]);

        try {
            $document = MemberDocument::findOrFail($request->document_id);
            
            // 회원의 문서인지 확인
            if ($document->member_id != $member->id) {
                return response()->json([
                    'success' => false,
                    'message' => '권한이 없습니다.'
                ], 403);
            }

            // 상태를 submitted로 변경
            $document->status = 'submitted';
            $document->save();

            return response()->json([
                'success' => true,
                'message' => '완료 처리되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '완료 처리 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
