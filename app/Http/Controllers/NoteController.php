<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberNote;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Note 상세 페이지 표시
     */
    public function show()
    {
        $member = session('member');
        if (!$member || !isset($member['id'])) {
            return redirect()->route('login');
        }

        $gNum = "main";
        $gName = "Note";
        $sName = "";

        // 로그인한 회원의 Note 조회 (회원 공유가 활성화된 것만)
        $memberNote = MemberNote::where('member_id', $member['id'])
            ->where('share_with_member', true)
            ->with(['files'])
            ->orderBy('created_at', 'desc')
            ->first();

        return view('note.show', compact('gNum', 'gName', 'sName', 'memberNote'));
    }
}
