<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMailRequest;
use App\Http\Requests\UpdateMailRequest;
use App\Services\Backoffice\MailService;
use App\Models\Mail;
use App\Models\MailAddressBook;
use App\Models\ProjectTerm;
use App\Models\Course;
use App\Models\OperatingInstitution;
use App\Models\ProjectPeriod;
use App\Models\Country;
use Illuminate\Http\Request;

class MailController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * 메일발송 목록 표시
     */
    public function index(Request $request)
    {
        $mails = $this->mailService->getMailsWithFilters($request);
        return view('backoffice.mails.index', compact('mails'));
    }

    /**
     * 메일발송 등록 폼 표시
     */
    public function create()
    {
        $addressBooks = MailAddressBook::orderBy('name')->get(['id', 'name']);
        $projectTerms = ProjectTerm::active()->orderBy('created_at', 'desc')->get(['id', 'name']);
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();
        
        return view('backoffice.mails.create', compact(
            'addressBooks',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries'
        ));
    }

    /**
     * 메일발송 저장
     */
    public function store(StoreMailRequest $request)
    {
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        
        $this->mailService->createMail($data, $files);

        return redirect()->route('backoffice.mails.index')
            ->with('success', '메일이 등록되었습니다.');
    }

    /**
     * 메일발송 수정 폼 표시
     */
    public function edit($id)
    {
        $mail = Mail::with(['files', 'recipientFilters', 'addressBookSelections'])->findOrFail($id);
        $addressBooks = MailAddressBook::orderBy('name')->get(['id', 'name']);
        $projectTerms = ProjectTerm::active()->orderBy('created_at', 'desc')->get(['id', 'name']);
        $courses = Course::active()->orderBy('display_order')->get();
        $operatingInstitutions = OperatingInstitution::active()->orderBy('display_order')->get();
        $projectPeriods = ProjectPeriod::active()->orderBy('display_order')->get();
        $countries = Country::active()->orderBy('display_order')->get();
        
        return view('backoffice.mails.edit', compact(
            'mail',
            'addressBooks',
            'projectTerms',
            'courses',
            'operatingInstitutions',
            'projectPeriods',
            'countries'
        ));
    }

    /**
     * 메일발송 업데이트
     */
    public function update(UpdateMailRequest $request, $id)
    {
        $mail = Mail::findOrFail($id);
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        $deletedFileIds = $request->input('deleted_file_ids', []);

        $this->mailService->updateMail($mail, $data, $files, $deletedFileIds);

        return redirect()->route('backoffice.mails.index')
            ->with('success', '메일이 수정되었습니다.');
    }

    /**
     * 메일발송 삭제
     */
    public function destroy($id)
    {
        $mail = Mail::findOrFail($id);
        $this->mailService->deleteMail($mail);

        return redirect()->route('backoffice.mails.index')
            ->with('success', '메일이 삭제되었습니다.');
    }

    /**
     * 기수별 필터로 회원 조회 (AJAX, 미리보기용)
     */
    public function getMembersByFilters(Request $request)
    {
        $request->validate([
            'filters' => 'required|array|min:1',
            'filters.*.project_term_id' => 'nullable|exists:project_terms,id',
            'filters.*.course_id' => 'nullable|exists:courses,id',
            'filters.*.operating_institution_id' => 'nullable|exists:operating_institutions,id',
            'filters.*.project_period_id' => 'nullable|exists:project_periods,id',
            'filters.*.country_id' => 'nullable|exists:countries,id',
        ]);

        $recipients = $this->mailService->getRecipientsByProjectTerm($request->filters);

        return response()->json([
            'success' => true,
            'count' => $recipients->count(),
            'recipients' => $recipients,
        ]);
    }
}
