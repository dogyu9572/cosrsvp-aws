<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\ProjectPeriod;
use App\Services\Backoffice\ProjectPeriodService;
use Illuminate\Http\Request;

class ProjectPeriodController extends Controller
{
    protected $projectPeriodService;

    public function __construct(ProjectPeriodService $projectPeriodService)
    {
        $this->projectPeriodService = $projectPeriodService;
    }

    /**
     * 프로젝트기간 등록 (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'operating_institution_id' => 'required|exists:operating_institutions,id',
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
        ]);

        try {
            $period = $this->projectPeriodService->createPeriod($validated);
            
            return response()->json([
                'success' => true,
                'message' => '프로젝트기간이 등록되었습니다.',
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트기간 등록 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 프로젝트기간 수정 (AJAX)
     */
    public function update(Request $request, ProjectPeriod $projectPeriod)
    {
        $validated = $request->validate([
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
        ]);

        try {
            $period = $this->projectPeriodService->updatePeriod($projectPeriod, $validated);
            
            return response()->json([
                'success' => true,
                'message' => '프로젝트기간이 수정되었습니다.',
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트기간 수정 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 프로젝트기간 삭제 (AJAX)
     */
    public function destroy(ProjectPeriod $projectPeriod)
    {
        try {
            $this->projectPeriodService->deletePeriod($projectPeriod);
            
            return response()->json([
                'success' => true,
                'message' => '프로젝트기간이 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 프로젝트기간 상세 조회 (AJAX)
     */
    public function show(ProjectPeriod $projectPeriod)
    {
        try {
            return response()->json($projectPeriod);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 특정 운영기관의 프로젝트기간 목록 조회 (AJAX)
     */
    public function getByInstitution($institutionId)
    {
        try {
            $periods = $this->projectPeriodService->getPeriodsByInstitution($institutionId);
            return response()->json($periods);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 프로젝트기간 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:project_periods,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->projectPeriodService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
