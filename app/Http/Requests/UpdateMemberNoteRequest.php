<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberNoteRequest extends FormRequest
{
    /**
     * 요청에 대한 권한을 확인합니다.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 유효성 검사 규칙을 정의합니다.
     */
    public function rules(): array
    {
        return [
            'member_id' => 'required|exists:members,id',
            'status' => 'required|in:normal,urgent,caution',
            'korean_title' => 'required|string|max:255',
            'english_title' => 'required|string|max:255',
            'korean_content' => 'required|string',
            'english_content' => 'required|string',
            'share_with_member' => 'boolean',
            'share_with_kofhi' => 'boolean',
            'share_with_operator' => 'boolean',
            'files.*' => 'nullable|file|max:10240', // 최대 10MB
        ];
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'member_id.required' => '회원을 선택해주세요.',
            'member_id.exists' => '선택한 회원이 존재하지 않습니다.',
            'status.required' => '상태를 선택해주세요.',
            'status.in' => '올바른 상태를 선택해주세요.',
            'korean_title.required' => '국문 제목을 입력해주세요.',
            'english_title.required' => '영문 제목을 입력해주세요.',
            'korean_content.required' => '국문 내용을 입력해주세요.',
            'english_content.required' => '영문 내용을 입력해주세요.',
            'files.*.file' => '올바른 파일이 아닙니다.',
            'files.*.max' => '파일 크기는 최대 10MB까지 가능합니다.',
        ];
    }
}
