<?php

namespace App\Services\Backoffice;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryService
{
    /**
     * 특정 프로젝트기간의 모든 국가 조회
     */
    public function getCountriesByPeriod(int $periodId)
    {
        return Country::where('project_period_id', $periodId)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 국가 생성
     */
    public function createCountry(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = Country::where('project_period_id', $data['project_period_id'])
                    ->max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $country = Country::create($data);
            
            DB::commit();
            return $country;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 국가 수정
     */
    public function updateCountry(Country $country, array $data)
    {
        DB::beginTransaction();
        try {
            $country->update($data);
            
            DB::commit();
            return $country;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 국가 삭제
     */
    public function deleteCountry(Country $country)
    {
        DB::beginTransaction();
        try {
            // 하위 일정이 있는지 확인
            if ($country->schedules()->count() > 0) {
                throw new \Exception('하위 일정이 있어 삭제할 수 없습니다.');
            }

            $country->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 국가 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                Country::where('id', $order['id'])
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
