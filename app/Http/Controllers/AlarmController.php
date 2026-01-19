<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    /**
     * 알람 목록 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $memberId = session('member_id');
        if (!$memberId) {
            return redirect()->route('login');
        }

        $gNum = "08";
        $gName = "Alarm";
        $sName = "02";

        // 로그인한 회원의 알람 조회
        $query = Alert::where('member_id', $memberId)
            ->orderBy('created_at', 'desc');

        // 검색 필터 적용
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('korean_title', 'like', '%' . $keyword . '%')
                  ->orWhere('english_title', 'like', '%' . $keyword . '%')
                  ->orWhere('korean_content', 'like', '%' . $keyword . '%')
                  ->orWhere('english_content', 'like', '%' . $keyword . '%');
            });
        }

        // 페이지네이션 처리
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;

        $alarms = $query->with('files')->paginate($perPage)->withQueryString();

        return view('alarm.index', compact('gNum', 'gName', 'sName', 'alarms'));
    }

    /**
     * 알람 상세 페이지
     */
    public function show($id)
    {
        // 로그인 확인
        $memberId = session('member_id');
        if (!$memberId) {
            return redirect()->route('login');
        }

        $gNum = "08";
        $gName = "Alarm";
        $sName = "02";

        // 로그인한 회원의 알람만 조회
        $alarm = Alert::with('files')
            ->where('member_id', $memberId)
            ->findOrFail($id);

        return view('alarm.show', compact('gNum', 'gName', 'sName', 'alarm'));
    }
}
