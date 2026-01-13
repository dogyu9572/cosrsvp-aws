<?php

namespace App\Services\Backoffice;

use App\Models\OperatingInstitution;
use Illuminate\Support\Facades\DB;

class OperatingInstitutionService
{
    /**
     * 특정 과정의 모든 운영기관 조회
     */
    public function getInstitutionsByCourse(int $courseId)
    {
        return OperatingInstitution::where('course_id', $courseId)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 운영기관 생성
     */
    public function createInstitution(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = OperatingInstitution::where('course_id', $data['course_id'])
                    ->max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $institution = OperatingInstitution::create($data);
            
            DB::commit();
            return $institution;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 운영기관 수정
     */
    public function updateInstitution(OperatingInstitution $institution, array $data)
    {
        DB::beginTransaction();
        try {
            $institution->update($data);
            
            DB::commit();
            return $institution;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 운영기관 삭제
     */
    public function deleteInstitution(OperatingInstitution $institution)
    {
        DB::beginTransaction();
        try {
            // 하위 프로젝트기간이 있는지 확인
            if ($institution->projectPeriods()->count() > 0) {
                throw new \Exception('하위 프로젝트기간이 있어 삭제할 수 없습니다.');
            }

            $institution->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 운영기관 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                OperatingInstitution::where('id', $order['id'])
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
