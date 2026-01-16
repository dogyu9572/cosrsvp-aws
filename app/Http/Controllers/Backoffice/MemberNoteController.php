<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberNoteRequest;
use App\Http\Requests\UpdateMemberNoteRequest;
use App\Services\Backoffice\MemberNoteService;
use App\Models\MemberNote;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberNoteController extends Controller
{
    protected $memberNoteService;

    public function __construct(MemberNoteService $memberNoteService)
    {
        $this->memberNoteService = $memberNoteService;
    }

    /**
     * 회원비고 목록 표시
     */
    public function index(Request $request)
    {
        $memberNotes = $this->memberNoteService->getMemberNotesWithFilters($request);
        $members = Member::active()->orderBy('name')->get(['id', 'name']);
        
        return view('backoffice.member-notes.index', compact('memberNotes', 'members'));
    }

    /**
     * 회원비고 등록 폼 표시
     */
    public function create(Request $request)
    {
        $memberId = $request->get('member_id');
        $members = Member::active()->orderBy('name')->get(['id', 'name']);
        
        return view('backoffice.member-notes.create', compact('members', 'memberId'));
    }

    /**
     * 회원비고 저장
     */
    public function store(StoreMemberNoteRequest $request)
    {
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        
        $this->memberNoteService->createMemberNote($data, $files);

        $memberId = $request->input('member_id');
        return redirect()->route('backoffice.member-notes.index', $memberId ? ['member_id' => $memberId] : [])
            ->with('success', '회원비고가 등록되었습니다.');
    }

    /**
     * 회원비고 수정 폼 표시
     */
    public function edit($id)
    {
        $memberNote = MemberNote::with(['member', 'files'])->findOrFail($id);
        $members = Member::active()->orderBy('name')->get(['id', 'name']);
        
        return view('backoffice.member-notes.edit', compact('memberNote', 'members'));
    }

    /**
     * 회원비고 업데이트
     */
    public function update(UpdateMemberNoteRequest $request, $id)
    {
        $memberNote = MemberNote::findOrFail($id);
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        $deletedFileIds = $request->input('deleted_file_ids', []);

        $this->memberNoteService->updateMemberNote($memberNote, $data, $files, $deletedFileIds);

        return redirect()->route('backoffice.member-notes.index')
            ->with('success', '회원비고가 수정되었습니다.');
    }

    /**
     * 회원비고 삭제
     */
    public function destroy($id)
    {
        $memberNote = MemberNote::findOrFail($id);
        $this->memberNoteService->deleteMemberNote($memberNote);

        return redirect()->route('backoffice.member-notes.index')
            ->with('success', '회원비고가 삭제되었습니다.');
    }
}
