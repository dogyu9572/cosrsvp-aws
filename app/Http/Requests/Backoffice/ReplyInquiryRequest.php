<?php

namespace App\Http\Requests\Backoffice;

use Illuminate\Foundation\Http\FormRequest;

class ReplyInquiryRequest extends FormRequest
{
    /**
     * 사용자가 이 요청을 할 권한이 있는지 확인
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 유효성 검사 규칙
     */
    public function rules(): array
    {
        return [
            'reply_content' => 'required|string',
            'reply_attachments' => 'nullable|array',
            'reply_attachments.*' => 'file|max:10240', // 최대 10MB
            'reply_status' => 'required|in:pending,completed',
            'existing_reply_attachments' => 'nullable|array',
            'remove_reply_attachments' => 'nullable|array',
        ];
    }

    /**
     * 유효성 검사 오류 메시지
     */
    public function messages(): array
    {
        return [
            'reply_content.required' => '답변 내용을 입력해주세요.',
            'reply_status.required' => '답변여부를 선택해주세요.',
            'reply_status.in' => '유효하지 않은 답변여부입니다.',
            'reply_attachments.*.file' => '첨부파일이 올바르지 않습니다.',
            'reply_attachments.*.max' => '첨부파일은 최대 10MB까지 업로드 가능합니다.',
        ];
    }
}
