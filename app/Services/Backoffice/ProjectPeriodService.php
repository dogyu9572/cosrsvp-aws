<?php

namespace App\Services\Backoffice;

use App\Models\ProjectPeriod;
use Illuminate\Support\Facades\DB;

class ProjectPeriodService
{
    /**
     * 특정 운영기관의 모든 프로젝트기간 조회
     */
    public function getPeriodsByInstitution(int $institutionId)
    {
        return ProjectPeriod::where('operating_institution_id', $institutionId)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 프로젝트기간 생성
     */
    public function createPeriod(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = ProjectPeriod::where('operating_institution_id', $data['operating_institution_id'])
                    ->max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $period = ProjectPeriod::create($data);
            
            DB::commit();
            return $period;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 프로젝트기간 수정
     */
    public function updatePeriod(ProjectPeriod $period, array $data)
    {
        DB::beginTransaction();
        try {
            $period->update($data);
            
            DB::commit();
            return $period;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 프로젝트기간 삭제
     */
    public function deletePeriod(ProjectPeriod $period)
    {
        DB::beginTransaction();
        try {
            // 하위 국가가 있는지 확인
            if ($period->countries()->count() > 0) {
                throw new \Exception('하위 국가가 있어 삭제할 수 없습니다.');
            }

            $period->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 프로젝트기간 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                ProjectPeriod::where('id', $order['id'])
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
