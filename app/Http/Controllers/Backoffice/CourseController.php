<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Backoffice\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * 과정 등록 (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_term_id' => 'required|exists:project_terms,id',
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
        ]);

        try {
            $course = $this->courseService->createCourse($validated);
            
            return response()->json([
                'success' => true,
                'message' => '과정이 등록되었습니다.',
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '과정 등록 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 과정 수정 (AJAX)
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name_ko' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
        ]);

        try {
            $course = $this->courseService->updateCourse($course, $validated);
            
            return response()->json([
                'success' => true,
                'message' => '과정이 수정되었습니다.',
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '과정 수정 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * 과정 삭제 (AJAX)
     */
    public function destroy(Course $course)
    {
        try {
            $this->courseService->deleteCourse($course);
            
            return response()->json([
                'success' => true,
                'message' => '과정이 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * 과정 상세 조회 (AJAX)
     */
    public function show(Course $course)
    {
        try {
            return response()->json($course);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 특정 기수의 과정 목록 조회 (AJAX)
     */
    public function getByTerm($termId)
    {
        try {
            $courses = $this->courseService->getCoursesByTerm($termId);
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * 과정 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:courses,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->courseService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
