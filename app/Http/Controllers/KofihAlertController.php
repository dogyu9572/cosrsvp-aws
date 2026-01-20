<?php

namespace App\Http\Controllers;

use App\Services\Backoffice\AlertService;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KofihAlertController extends Controller
{
    public function __construct(
        private AlertService $alertService
    ) {}

    /**
     * 알림 목록 표시
     */
    public function index(Request $request)
    {
        // 알림 목록 조회 (백오피스 서비스 재사용)
        $alerts = $this->alertService->getAlertsWithFilters($request);

        return view('kofih.alerts.index', compact('alerts'));
    }

    /**
     * 알림 상세 표시
     */
    public function show($id)
    {
        $alert = Alert::with(['member', 'files'])->findOrFail($id);
        
        // 이전/다음 글 조회
        $prevAlert = Alert::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextAlert = Alert::where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        return view('kofih.alerts.show', compact('alert', 'prevAlert', 'nextAlert'));
    }
}