<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InquiryController extends Controller
{
    /**
     * 문의 목록 페이지
     */
    public function index(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "07";
        $gName = "Contact Us";
        $sName = "Contact Us";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 문의 데이터 조회 (프로젝트 기수 필터링 적용)
        $inquiries = $this->getInquiriesByProjectTerm($request, $memberProjectInfo);

        return view('inquiry.index', compact('gNum', 'gName', 'sName', 'inquiries'));
    }

    /**
     * 문의 상세 페이지
     */
    public function show($id)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "07";
        $gName = "Contact Us";
        $sName = "Contact Us";

        // 로그인한 회원의 프로젝트 관련 정보
        $memberProjectInfo = [
            'project_term_id' => $member['project_term_id'] ?? null,
            'course_id' => $member['course_id'] ?? null,
            'operating_institution_id' => $member['operating_institution_id'] ?? null,
            'project_period_id' => $member['project_period_id'] ?? null,
            'country_id' => $member['country_id'] ?? null,
        ];

        // 문의 상세 조회 (프로젝트 기수 필터링 적용)
        $inquiry = $this->getInquiryById($id, $memberProjectInfo);

        if (!$inquiry) {
            abort(404, '문의사항을 찾을 수 없습니다.');
        }

        return view('inquiry.show', compact('gNum', 'gName', 'sName', 'inquiry'));
    }

    /**
     * 문의 작성 페이지
     */
    public function create()
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        $gNum = "07";
        $gName = "Contact Us";
        $sName = "Contact Us";

        return view('inquiry.create', compact('gNum', 'gName', 'sName'));
    }

    /**
     * 문의 저장 처리
     */
    public function store(Request $request)
    {
        // 로그인 확인
        $member = session('member');
        if (!$member) {
            return redirect()->route('login');
        }

        // 유효성 검사
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB
        ]);

        try {
            // 첨부파일 처리
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('inquiries/attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                    ];
                }
            }

            // 문의 저장
            $inquiry = Inquiry::create([
                'user_id' => $member['id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'attachments' => $attachments,
                'project_term_id' => $member['project_term_id'] ?? null,
                'course_id' => $member['course_id'] ?? null,
                'operating_institution_id' => $member['operating_institution_id'] ?? null,
                'project_period_id' => $member['project_period_id'] ?? null,
                'country_id' => $member['country_id'] ?? null,
                'reply_status' => 'pending',
            ]);

            return redirect()->route('inquiries.show', $inquiry->id)
                ->with('success', '문의가 등록되었습니다.');

        } catch (\Exception $e) {
            Log::error("문의 저장 오류: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => '문의 등록 중 오류가 발생했습니다.']);
        }
    }

    /**
     * 프로젝트 기수에 해당하는 문의 목록 조회
     */
    private function getInquiriesByProjectTerm(Request $request, $memberProjectInfo)
    {
        try {
            $query = Inquiry::whereNull('deleted_at');

            // 프로젝트 관련 정보 필터링 (모든 필드 일치해야 함)
            if ($memberProjectInfo['project_term_id']) {
                $query->where('project_term_id', $memberProjectInfo['project_term_id']);
            }
            if ($memberProjectInfo['course_id']) {
                $query->where('course_id', $memberProjectInfo['course_id']);
            }
            if ($memberProjectInfo['operating_institution_id']) {
                $query->where('operating_institution_id', $memberProjectInfo['operating_institution_id']);
            }
            if ($memberProjectInfo['project_period_id']) {
                $query->where('project_period_id', $memberProjectInfo['project_period_id']);
            }
            if ($memberProjectInfo['country_id']) {
                $query->where('country_id', $memberProjectInfo['country_id']);
            }

            // 검색 필터 적용
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                      ->orWhere('content', 'like', '%' . $keyword . '%');
                });
            }

            // 정렬: 최신순
            $perPage = $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 20, 30, 50]) ? $perPage : 10;

            $inquiries = $query->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            return $inquiries;

        } catch (\Exception $e) {
            Log::error("문의 목록 조회 오류: " . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                10,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }
    }

    /**
     * 프로젝트 기수에 해당하는 문의 상세 조회
     */
    private function getInquiryById($id, $memberProjectInfo)
    {
        try {
            $query = Inquiry::whereNull('deleted_at')->where('id', $id);

            // 프로젝트 관련 정보 필터링 (모든 필드 일치해야 함)
            if ($memberProjectInfo['project_term_id']) {
                $query->where('project_term_id', $memberProjectInfo['project_term_id']);
            }
            if ($memberProjectInfo['course_id']) {
                $query->where('course_id', $memberProjectInfo['course_id']);
            }
            if ($memberProjectInfo['operating_institution_id']) {
                $query->where('operating_institution_id', $memberProjectInfo['operating_institution_id']);
            }
            if ($memberProjectInfo['project_period_id']) {
                $query->where('project_period_id', $memberProjectInfo['project_period_id']);
            }
            if ($memberProjectInfo['country_id']) {
                $query->where('country_id', $memberProjectInfo['country_id']);
            }

            return $query->first();

        } catch (\Exception $e) {
            Log::error("문의 상세 조회 오류: " . $e->getMessage());
            return null;
        }
    }
}
