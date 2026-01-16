<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'dispatch_subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:project_term,address_book,test',
            'dispatch_status' => 'required|in:saved,scheduled',
            'files.*' => 'nullable|file|max:10240', // 최대 10MB
        ];

        // 발송대상 타입에 따른 검증
        if ($this->input('recipient_type') === 'test') {
            $rules['test_email'] = 'required|email|max:255';
        } elseif ($this->input('recipient_type') === 'address_book') {
            $rules['address_book_ids'] = 'required|array|min:1';
            $rules['address_book_ids.*'] = 'exists:mail_address_books,id';
        } elseif ($this->input('recipient_type') === 'project_term') {
            $rules['recipient_filters'] = 'required|array|min:1';
            $rules['recipient_filters.*.project_term_id'] = 'nullable|exists:project_terms,id';
            $rules['recipient_filters.*.course_id'] = 'nullable|exists:courses,id';
            $rules['recipient_filters.*.operating_institution_id'] = 'nullable|exists:operating_institutions,id';
            $rules['recipient_filters.*.project_period_id'] = 'nullable|exists:project_periods,id';
            $rules['recipient_filters.*.country_id'] = 'nullable|exists:countries,id';
        }

        // 재발송 선택 시 예약일시 필수
        if ($this->input('dispatch_status') === 'scheduled') {
            $rules['scheduled_at'] = 'required|date|after:now';
        }

        return $rules;
    }

    /**
     * 유효성 검사 메시지를 정의합니다.
     */
    public function messages(): array
    {
        return [
            'title.required' => '제목을 입력해주세요.',
            'dispatch_subject.required' => '발송 제목을 입력해주세요.',
            'content.required' => '내용을 입력해주세요.',
            'recipient_type.required' => '발송대상을 선택해주세요.',
            'recipient_type.in' => '유효하지 않은 발송대상입니다.',
            'dispatch_status.required' => '발송여부를 선택해주세요.',
            'dispatch_status.in' => '유효하지 않은 발송여부입니다.',
            'test_email.required' => '테스트 이메일을 입력해주세요.',
            'test_email.email' => '올바른 이메일 형식이 아닙니다.',
            'address_book_ids.required' => '주소록을 선택해주세요.',
            'address_book_ids.min' => '최소 1개 이상의 주소록을 선택해주세요.',
            'address_book_ids.*.exists' => '유효하지 않은 주소록입니다.',
            'recipient_filters.required' => '기수별 발송 조건을 입력해주세요.',
            'recipient_filters.min' => '최소 1개 이상의 조건을 입력해주세요.',
            'scheduled_at.required' => '예약 발송일시를 입력해주세요.',
            'scheduled_at.date' => '올바른 날짜 형식이 아닙니다.',
            'scheduled_at.after' => '예약 발송일시는 현재 시간 이후여야 합니다.',
            'files.*.file' => '올바른 파일이 아닙니다.',
            'files.*.max' => '파일 크기는 최대 10MB까지 가능합니다.',
        ];
    }
}
