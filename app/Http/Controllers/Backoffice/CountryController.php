<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\Backoffice\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * 국가 등록 (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_period_id' => 'required|exists:project_periods,id',
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'reference_material_id' => 'nullable|integer',
            'document_name' => 'nullable|string|max:255',
            'submission_deadline' => 'nullable|date',
        ]);

        try {
            $country = $this->countryService->createCountry($validated);
            
            return response()->json([
                'success' => true,
                'message' => '국가가 등록되었습니다.',
                'country' => $country
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '국가 등록 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 국가 수정 (AJAX)
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'reference_material_id' => 'nullable|integer',
            'document_name' => 'nullable|string|max:255',
            'submission_deadline' => 'nullable|date',
        ]);

        try {
            $country = $this->countryService->updateCountry($country, $validated);
            
            return response()->json([
                'success' => true,
                'message' => '국가가 수정되었습니다.',
                'country' => $country
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '국가 수정 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 국가 삭제 (AJAX)
     */
    public function destroy(Country $country)
    {
        try {
            $this->countryService->deleteCountry($country);
            
            return response()->json([
                'success' => true,
                'message' => '국가가 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 국가 상세 조회 (AJAX)
     */
    public function show(Country $country)
    {
        try {
            return response()->json($country);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 특정 프로젝트기간의 국가 목록 조회 (AJAX)
     */
    public function getByPeriod($periodId)
    {
        try {
            $countries = $this->countryService->getCountriesByPeriod($periodId);
            return response()->json($countries);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 국가 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:countries,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->countryService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
