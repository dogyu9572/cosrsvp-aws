<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\OperatingInstitution;
use App\Services\Backoffice\OperatingInstitutionService;
use Illuminate\Http\Request;

class OperatingInstitutionController extends Controller
{
    protected $operatingInstitutionService;

    public function __construct(OperatingInstitutionService $operatingInstitutionService)
    {
        $this->operatingInstitutionService = $operatingInstitutionService;
    }

    /**
     * 운영기관 등록 (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'cosmojin_manager_name' => 'nullable|string|max:100',
            'cosmojin_manager_phone' => 'nullable|string|max:50',
            'cosmojin_manager_email' => 'nullable|email|max:100',
            'kofhi_manager_name' => 'nullable|string|max:100',
            'kofhi_manager_phone' => 'nullable|string|max:50',
            'kofhi_manager_email' => 'nullable|email|max:100',
        ]);

        try {
            $institution = $this->operatingInstitutionService->createInstitution($validated);
            
            return response()->json([
                'success' => true,
                'message' => '운영기관이 등록되었습니다.',
                'institution' => $institution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '운영기관 등록 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 운영기관 수정 (AJAX)
     */
    public function update(Request $request, OperatingInstitution $operatingInstitution)
    {
        $validated = $request->validate([
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'cosmojin_manager_name' => 'nullable|string|max:100',
            'cosmojin_manager_phone' => 'nullable|string|max:50',
            'cosmojin_manager_email' => 'nullable|email|max:100',
            'kofhi_manager_name' => 'nullable|string|max:100',
            'kofhi_manager_phone' => 'nullable|string|max:50',
            'kofhi_manager_email' => 'nullable|email|max:100',
        ]);

        try {
            $institution = $this->operatingInstitutionService->updateInstitution($operatingInstitution, $validated);
            
            return response()->json([
                'success' => true,
                'message' => '운영기관이 수정되었습니다.',
                'institution' => $institution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '운영기관 수정 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 운영기관 삭제 (AJAX)
     */
    public function destroy(OperatingInstitution $operatingInstitution)
    {
        try {
            $this->operatingInstitutionService->deleteInstitution($operatingInstitution);
            
            return response()->json([
                'success' => true,
                'message' => '운영기관이 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 운영기관 상세 조회 (AJAX)
     */
    public function show(OperatingInstitution $operatingInstitution)
    {
        try {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json($operatingInstitution);
            }
            return response()->json($operatingInstitution);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 특정 과정의 운영기관 목록 조회 (AJAX)
     */
    public function getByCourse($courseId)
    {
        try {
            $institutions = $this->operatingInstitutionService->getInstitutionsByCourse($courseId);
            return response()->json($institutions);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 운영기관 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:operating_institutions,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->operatingInstitutionService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
