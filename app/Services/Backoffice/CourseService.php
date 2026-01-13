<?php

namespace App\Services\Backoffice;

use App\Models\Course;
use App\Models\ProjectTerm;
use Illuminate\Support\Facades\DB;

class CourseService
{
    /**
     * 특정 기수의 모든 과정 조회
     */
    public function getCoursesByTerm(int $termId)
    {
        return Course::where('project_term_id', $termId)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * 과정 생성
     */
    public function createCourse(array $data)
    {
        DB::beginTransaction();
        try {
            // display_order 자동 설정
            if (!isset($data['display_order'])) {
                $maxOrder = Course::where('project_term_id', $data['project_term_id'])
                    ->max('display_order');
                $data['display_order'] = ($maxOrder ?? 0) + 1;
            }

            $course = Course::create($data);
            
            DB::commit();
            return $course;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 과정 수정
     */
    public function updateCourse(Course $course, array $data)
    {
        DB::beginTransaction();
        try {
            $course->update($data);
            
            DB::commit();
            return $course;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 과정 삭제
     */
    public function deleteCourse(Course $course)
    {
        DB::beginTransaction();
        try {
            // 하위 운영기관이 있는지 확인
            if ($course->operatingInstitutions()->count() > 0) {
                throw new \Exception('하위 운영기관이 있어 삭제할 수 없습니다.');
            }

            $course->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 과정 순서 변경
     */
    public function updateOrder(array $orders)
    {
        DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                Course::where('id', $order['id'])
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
