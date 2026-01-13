<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\Backoffice\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * 일정 등록 (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $schedule = $this->scheduleService->createSchedule($validated);
            
            return response()->json([
                'success' => true,
                'message' => '일정이 등록되었습니다.',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '일정 등록 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 일정 수정 (AJAX)
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $schedule = $this->scheduleService->updateSchedule($schedule, $validated);
            
            return response()->json([
                'success' => true,
                'message' => '일정이 수정되었습니다.',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '일정 수정 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 일정 삭제 (AJAX)
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $this->scheduleService->deleteSchedule($schedule);
            
            return response()->json([
                'success' => true,
                'message' => '일정이 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 일정 상세 조회 (AJAX)
     */
    public function show(Schedule $schedule)
    {
        try {
            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 특정 국가의 일정 목록 조회 (AJAX)
     */
    public function getByCountry($countryId)
    {
        try {
            $schedules = $this->scheduleService->getSchedulesByCountry($countryId);
            return response()->json($schedules);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 일정 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:schedules,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->scheduleService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
