<?php

namespace App\Services\Backoffice;

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    /**
     * 특정 국가의 모든 일정 조회
     */
    public function getSchedulesByCountry(int $countryId)
    {
        return Schedule::where('country_id', $countryId)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 일정 생성
     */
    public function createSchedule(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = Schedule::where('country_id', $data['country_id'])
                    ->max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $schedule = Schedule::create($data);
            
            DB::commit();
            return $schedule;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 일정 수정
     */
    public function updateSchedule(Schedule $schedule, array $data)
    {
        DB::beginTransaction();
        try {
            $schedule->update($data);
            
            DB::commit();
            return $schedule;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 일정 삭제
     */
    public function deleteSchedule(Schedule $schedule)
    {
        DB::beginTransaction();
        try {
            $schedule->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 일정 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                Schedule::where('id', $order['id'])
                    ->update(['display_order' => $order['order']]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
