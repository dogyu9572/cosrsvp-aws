<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
            'login_id' => 'required|string|max:100|unique:members,login_id',
            'password' => 'required|string|min:8',
            'name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female',
            'email' => 'nullable|email|max:255',
            'phone_kr' => 'nullable|string|max:50',
            'phone_local' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'occupation' => 'nullable|string|max:100',
            'major' => 'nullable|string|max:100',
            'affiliation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'alien_registration_number' => 'nullable|string|max:50',
            'alien_registration_expiry' => 'nullable|date',
            'project_term_id' => 'nullable|exists:project_terms,id',
            'course_id' => 'nullable|exists:courses,id',
            'operating_institution_id' => 'nullable|exists:operating_institutions,id',
            'project_period_id' => 'nullable|exists:project_periods,id',
            'country_id' => 'nullable|exists:countries,id',
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:500',
            'hotel_address_detail' => 'nullable|string|max:255',
            'training_period' => 'nullable|string|max:100',
            'visa_type' => 'nullable|string|max:50',
            'cultural_experience' => 'nullable|string',
            'account_info' => 'nullable|string|max:255',
            'insurance_status' => 'nullable|string|max:50',
            'clothing_size' => 'nullable|string|max:10',
            'dietary_restrictions' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'departure_location' => 'nullable|string|max:100',
            'arrival_location' => 'nullable|string|max:100',
            'entry_date' => 'nullable|date',
            'exit_date' => 'nullable|date',
            'entry_flight' => 'nullable|string|max:255',
            'exit_flight' => 'nullable|string|max:255',
            'ticket_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'is_active' => 'boolean',
        ];
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'login_id.required' => '아이디를 입력해주세요.',
            'login_id.unique' => '이미 사용 중인 아이디입니다.',
            'password.required' => '비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'name.required' => '성명을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
            'project_term_id.exists' => '선택한 프로젝트 기수가 존재하지 않습니다.',
            'course_id.exists' => '선택한 과정이 존재하지 않습니다.',
            'operating_institution_id.exists' => '선택한 운영기관이 존재하지 않습니다.',
            'project_period_id.exists' => '선택한 프로젝트기간이 존재하지 않습니다.',
            'country_id.exists' => '선택한 국가가 존재하지 않습니다.',
        ];
    }
}
