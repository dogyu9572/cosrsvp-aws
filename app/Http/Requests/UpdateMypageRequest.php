<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMypageRequest extends FormRequest
{
    /**
     * 요청에 대한 권한을 확인합니다.
     */
    public function authorize(): bool
    {
        return session()->has('member_id');
    }

    /**
     * 유효성 검사 규칙을 정의합니다.
     */
    public function rules(): array
    {
        return [
            'occupation' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'string', 'regex:/^\d{4}\.\d{2}\.\d{2}$/'],
            'major' => ['nullable', 'string', 'max:255'],
            'affiliation' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'passport_expiry' => ['nullable', 'string', 'regex:/^\d{4}\.\d{2}\.\d{2}$/'],
            'alien_registration_number' => ['nullable', 'string', 'max:255'],
            'alien_registration_expiry' => ['nullable', 'string', 'regex:/^\d{4}\.\d{2}\.\d{2}$/'],
            'clothing_size' => ['nullable', 'string', 'max:255'],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'occupation.max' => '직업은 255자를 초과할 수 없습니다.',
            'birth_date.regex' => '올바른 날짜 형식이 아닙니다. (YYYY.MM.DD 형식)',
            'major.max' => '전공은 255자를 초과할 수 없습니다.',
            'affiliation.max' => '소속은 255자를 초과할 수 없습니다.',
            'department.max' => '부서는 255자를 초과할 수 없습니다.',
            'position.max' => '직위는 255자를 초과할 수 없습니다.',
            'passport_number.max' => '여권번호는 255자를 초과할 수 없습니다.',
            'passport_expiry.regex' => '올바른 날짜 형식이 아닙니다. (YYYY.MM.DD 형식)',
            'alien_registration_number.max' => '외국인등록번호는 255자를 초과할 수 없습니다.',
            'alien_registration_expiry.regex' => '올바른 날짜 형식이 아닙니다. (YYYY.MM.DD 형식)',
            'clothing_size.max' => '옷 사이즈는 255자를 초과할 수 없습니다.',
            'dietary_restrictions.max' => '특이식성은 500자를 초과할 수 없습니다.',
            'special_requests.max' => '특이사항 및 요청사항은 1000자를 초과할 수 없습니다.',
        ];
    }
}
