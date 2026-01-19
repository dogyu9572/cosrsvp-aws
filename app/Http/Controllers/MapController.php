<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * 맵 페이지
     */
    public function index()
    {
        // 로그인 확인
        $memberId = session('member_id');
        if (!$memberId) {
            return redirect()->route('login');
        }

        $gNum = "03";
        $gName = "Accommodation and surrounding areas";

        return view('map.index', compact('gNum', 'gName'));
    }
}
