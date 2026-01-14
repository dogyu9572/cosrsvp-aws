<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\ReplyInquiryRequest;
use App\Services\Backoffice\InquiryService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryService;

    public function __construct(InquiryService $inquiryService)
    {
        $this->inquiryService = $inquiryService;
    }

    /**
     * 문의 목록 페이지
     */
    public function index(Request $request)
    {
        $inquiries = $this->inquiryService->getInquiries($request);
        
        return view('backoffice.inquiries.index', compact('inquiries'));
    }

    /**
     * 문의 상세 페이지
     */
    public function show(int $id)
    {
        $inquiry = $this->inquiryService->getInquiry($id);
        
        return view('backoffice.inquiries.show', compact('inquiry'));
    }

    /**
     * 답변 저장/수정
     */
    public function reply(ReplyInquiryRequest $request, int $id)
    {
        $validated = $request->validated();
        
        // 첨부파일 처리
        $replyAttachments = $this->inquiryService->handleAttachments($request, 'reply');
        $validated['reply_attachments'] = $replyAttachments;
        
        $this->inquiryService->replyInquiry($id, $validated);
        
        return redirect()->route('backoffice.inquiries.index')
            ->with('success', '답변이 저장되었습니다.');
    }

    /**
     * 문의 삭제
     */
    public function destroy(int $id)
    {
        $this->inquiryService->deleteInquiry($id);
        
        return redirect()->route('backoffice.inquiries.index')
            ->with('success', '문의가 삭제되었습니다.');
    }
}
