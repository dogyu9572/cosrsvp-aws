<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        // 회원의 국가 정보를 통해 참고자료 조회
        $referenceMaterial = null;
        $memberModel = Member::find($member['id']);
        if ($memberModel && $memberModel->country_id) {
            $country = Country::find($memberModel->country_id);
            if ($country && $country->reference_material_id) {
                // board_references 테이블에서 참고자료 조회
                $referenceMaterial = DB::table('board_references')
                    ->where('id', $country->reference_material_id)
                    ->whereNull('deleted_at')
                    ->first();
                
                // custom_fields에서 영문 제목/내용 추출
                if ($referenceMaterial) {
                    $customFields = json_decode($referenceMaterial->custom_fields ?? '{}', true);
                    if (!is_array($customFields)) {
                        $customFields = [];
                    }
                    $referenceMaterial->title = $customFields['title_en'] ?? $referenceMaterial->title;
                    $referenceMaterial->content = $customFields['content_en'] ?? $referenceMaterial->content;
                }
            }
        }

        return view('note.show', compact('gNum', 'gName', 'sName', 'referenceMaterial'));
    }
}
