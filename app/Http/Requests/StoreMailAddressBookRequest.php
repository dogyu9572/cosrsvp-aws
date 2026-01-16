<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMailAddressBookRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts.*.email|string|max:100',
            'contacts.*.email' => 'required_with:contacts.*.name|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:50',
            'excel_file' => 'nullable|file|mimes:csv,xlsx,xls|max:5120',
        ];
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'name.required' => '주소록명을 입력해주세요.',
            'name.max' => '주소록명은 최대 255자까지 입력 가능합니다.',
            'contacts.*.name.required_with' => '이름을 입력해주세요.',
            'contacts.*.name.max' => '이름은 최대 100자까지 입력 가능합니다.',
            'contacts.*.email.required_with' => '이메일을 입력해주세요.',
            'contacts.*.email.email' => '올바른 이메일 형식이 아닙니다.',
            'contacts.*.email.max' => '이메일은 최대 255자까지 입력 가능합니다.',
            'contacts.*.phone.max' => '연락처는 최대 50자까지 입력 가능합니다.',
            'excel_file.file' => '파일을 선택해주세요.',
            'excel_file.mimes' => 'CSV, XLSX, XLS 파일만 업로드 가능합니다.',
            'excel_file.max' => '파일 크기는 최대 5MB까지 가능합니다.',
        ];
    }
}
