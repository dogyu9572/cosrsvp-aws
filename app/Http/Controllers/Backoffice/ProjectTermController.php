<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\ProjectTerm;
use App\Services\Backoffice\ProjectTermService;
use Illuminate\Http\Request;

class ProjectTermController extends Controller
{
    protected $projectTermService;

    public function __construct(ProjectTermService $projectTermService)
    {
        $this->projectTermService = $projectTermService;
    }

    /**
     * 프로젝트 기수 리스트
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        
        $query = ProjectTerm::query();
        
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        $terms = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('backoffice.project-terms.index', compact('terms', 'search'));
    }

    /**
     * 프로젝트 기수 등록 처리
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $this->projectTermService->createTerm($validated);
            
            // AJAX 요청인 경우 JSON 응답
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '프로젝트 기수가 등록되었습니다.'
                ]);
            }
            
            return redirect()
                ->route('backoffice.project-terms.index')
                ->with('success', '프로젝트 기수가 등록되었습니다.');
        } catch (\Exception $e) {
            // AJAX 요청인 경우 JSON 응답
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '프로젝트 기수 등록 중 오류가 발생했습니다: ' . $e->getMessage()
                ], 422);
            }
            
            return back()
                ->withInput()
                ->with('error', '프로젝트 기수 등록 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 프로젝트 기수 상세 (계층 구조 관리)
     */
    public function show(ProjectTerm $projectTerm)
    {
        $projectTerm->load('courses.operatingInstitutions.projectPeriods.countries.schedules');
        
        return view('backoffice.project-terms.show', compact('projectTerm'));
    }

    /**
     * 프로젝트 기수 수정 폼
     */
    public function edit(ProjectTerm $projectTerm)
    {
        return view('backoffice.project-terms.edit', compact('projectTerm'));
    }

    /**
     * 프로젝트 기수 수정 처리
     */
    public function update(Request $request, ProjectTerm $projectTerm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $this->projectTermService->updateTerm($projectTerm, $validated);
            
            return redirect()
                ->route('backoffice.project-terms.index')
                ->with('success', '프로젝트 기수가 수정되었습니다.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '프로젝트 기수 수정 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 프로젝트 기수 삭제
     */
    public function destroy(ProjectTerm $projectTerm)
    {
        try {
            $this->projectTermService->deleteTerm($projectTerm);
            
            return redirect()
                ->route('backoffice.project-terms.index')
                ->with('success', '프로젝트 기수가 삭제되었습니다.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * 프로젝트 기수 순서 변경 (AJAX)
     */
    public function updateOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:project_terms,id',
                'orders.*.order' => 'required|integer',
            ]);

            $this->projectTermService->updateOrder($validated['orders']);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '순서 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * 참고자료 목록 조회 (AJAX)
     */
    public function getReferenceMaterials(Request $request)
    {
        try {
            $references = \Illuminate\Support\Facades\DB::table('board_references')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => $references
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '참고자료 목록 조회 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
