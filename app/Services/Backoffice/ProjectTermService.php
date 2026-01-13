<?php

namespace App\Services\Backoffice;

use App\Models\ProjectTerm;
use Illuminate\Support\Facades\DB;

class ProjectTermService
{
    /**
     * 모든 기수 조회
     */
    public function getAllTerms()
    {
        return ProjectTerm::orderBy('display_order')
            ->with('courses')
            ->get();
    }

    /**
     * 활성화된 기수만 조회
     */
    public function getActiveTerms()
    {
        return ProjectTerm::active()
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 기수 생성
     */
    public function createTerm(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = ProjectTerm::max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $term = ProjectTerm::create($data);
            
            DB::commit();
            return $term;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 기수 수정
     */
    public function updateTerm(ProjectTerm $term, array $data)
    {
        DB::beginTransaction();
        try {
            $term->update($data);
            
            DB::commit();
            return $term;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 기수 삭제
     */
    public function deleteTerm(ProjectTerm $term)
    {
        DB::beginTransaction();
        try {
            // 하위 과정이 있는지 확인
            if ($term->courses()->count() > 0) {
                throw new \Exception('하위 과정이 있어 삭제할 수 없습니다.');
            }

            $term->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 기수 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                ProjectTerm::where('id', $order['id'])
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
