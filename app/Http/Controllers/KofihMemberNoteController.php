<?php

namespace App\Http\Controllers;

use App\Services\Backoffice\MemberNoteService;
use App\Models\Member;
use App\Models\MemberNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KofihMemberNoteController extends Controller
{
    public function __construct(
        private MemberNoteService $memberNoteService
    ) {}

    /**
     * 회원비고 목록 표시
     */
    public function index(Request $request)
    {
        // 회원비고 목록 조회 (백오피스 서비스 재사용)
        $memberNotes = $this->memberNoteService->getMemberNotesWithFilters($request);
        
        // 회원 목록 (필터용)
        $members = Member::active()->orderBy('name')->get(['id', 'name']);

        return view('kofih.member-notes.index', compact('memberNotes', 'members'));
    }

    /**
     * 회원비고 상세 표시
     */
    public function show($id)
    {
        $note = MemberNote::with(['member', 'files'])->findOrFail($id);
        
        // 이전/다음 글 조회
        $prevNote = MemberNote::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNote = MemberNote::where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        return view('kofih.member-notes.show', compact('note', 'prevNote', 'nextNote'));
    }
}