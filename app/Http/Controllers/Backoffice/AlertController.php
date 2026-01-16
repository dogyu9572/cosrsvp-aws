<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlertRequest;
use App\Http\Requests\UpdateAlertRequest;
use App\Services\Backoffice\AlertService;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * 알림 목록 표시
     */
    public function index(Request $request)
    {
        $alerts = $this->alertService->getAlertsWithFilters($request);
        return view('backoffice.alerts.index', compact('alerts'));
    }

    /**
     * 알림 등록 폼 표시
     */
    public function create(Request $request)
    {
        $memberId = $request->get('member_id');
        $members = \App\Models\Member::active()->orderBy('name')->get(['id', 'name']);
        
        return view('backoffice.alerts.create', compact('members', 'memberId'));
    }

    /**
     * 알림 저장
     */
    public function store(StoreAlertRequest $request)
    {
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        
        $this->alertService->createAlert($data, $files);

        $memberId = $request->input('member_id');
        return redirect()->route('backoffice.alerts.index', $memberId ? ['member_id' => $memberId] : [])
            ->with('success', '알림이 등록되었습니다.');
    }

    /**
     * 알림 수정 폼 표시
     */
    public function edit($id)
    {
        $alert = Alert::with('files')->findOrFail($id);
        return view('backoffice.alerts.edit', compact('alert'));
    }

    /**
     * 알림 업데이트
     */
    public function update(UpdateAlertRequest $request, $id)
    {
        $alert = Alert::findOrFail($id);
        $data = $request->validated();
        $files = $request->hasFile('files') ? $request->file('files') : [];
        $deletedFileIds = $request->input('deleted_file_ids', []);

        $this->alertService->updateAlert($alert, $data, $files, $deletedFileIds);

        $memberId = $request->input('member_id');
        return redirect()->route('backoffice.alerts.index', $memberId ? ['member_id' => $memberId] : [])
            ->with('success', '알림이 수정되었습니다.');
    }

    /**
     * 알림 삭제
     */
    public function destroy($id)
    {
        $alert = Alert::findOrFail($id);
        $this->alertService->deleteAlert($alert);

        return redirect()->route('backoffice.alerts.index')
            ->with('success', '알림이 삭제되었습니다.');
    }
}
