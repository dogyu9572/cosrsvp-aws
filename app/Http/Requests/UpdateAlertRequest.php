<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlertRequest extends FormRequest
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
            'member_id' => 'nullable|exists:members,id',
            'is_notice' => 'boolean',
            'korean_title' => 'required|string|max:255',
            'english_title' => 'required|string|max:255',
            'korean_content' => 'required|string',
            'english_content' => 'required|string',
            'files.*' => 'nullable|file|max:10240', // 최대 10MB
        ];
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'korean_title.required' => '국문 제목을 입력해주세요.',
            'english_title.required' => '영문 제목을 입력해주세요.',
            'korean_content.required' => '국문 내용을 입력해주세요.',
            'english_content.required' => '영문 내용을 입력해주세요.',
            'files.*.file' => '올바른 파일이 아닙니다.',
            'files.*.max' => '파일 크기는 최대 10MB까지 가능합니다.',
        ];
    }
}
