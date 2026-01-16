@extends('backoffice.layouts.app')

@section('title', '메일발송 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/mails.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/summernote-custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            @if ($errors->any())
                <div class="board-alert board-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('backoffice.mails.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="board-form-group">
                    <label for="title" class="board-form-label">
                        제목 <span class="required">*</span>
                    </label>
                    <input type="text" id="title" name="title" class="board-form-control" value="{{ old('title') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="dispatch_subject" class="board-form-label">
                        발송 제목 <span class="required">*</span>
                    </label>
                    <input type="text" id="dispatch_subject" name="dispatch_subject" class="board-form-control" value="{{ old('dispatch_subject') }}" required>
                </div>

                <div class="board-form-group">
                    <label for="content" class="board-form-label">
                        내용 <span class="required">*</span>
                    </label>
                    <textarea id="content" name="content" class="board-form-control board-form-textarea summernote-editor" required>{{ old('content') }}</textarea>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">
                        발송대상 <span class="required">*</span>
                    </label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="recipient_type_project_term" name="recipient_type" value="project_term" @checked(old('recipient_type', 'project_term') == 'project_term') required>
                            <label for="recipient_type_project_term">기수별 발송</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="recipient_type_address_book" name="recipient_type" value="address_book" @checked(old('recipient_type') == 'address_book') required>
                            <label for="recipient_type_address_book">주소록</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="recipient_type_test" name="recipient_type" value="test" @checked(old('recipient_type') == 'test') required>
                            <label for="recipient_type_test">테스트</label>
                        </div>
                    </div>
                </div>

                <!-- 기수별 발송 섹션 -->
                <div id="project_term_section" class="recipient-section" style="display: {{ old('recipient_type', 'project_term') == 'project_term' ? 'block' : 'none' }};">
                    <div class="board-form-group">
                        <label class="board-form-label">기수별 발송 조건</label>
                        <div id="recipient_filters_container">
                            <div class="recipient-filter-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <select name="recipient_filters[0][project_term_id]" class="board-form-control">
                                    <option value="">전체</option>
                                    @foreach($projectTerms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                                <select name="recipient_filters[0][course_id]" class="board-form-control">
                                    <option value="">전체</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name_ko }}@if($course->name_en) / {{ $course->name_en }}@endif</option>
                                    @endforeach
                                </select>
                                <select name="recipient_filters[0][operating_institution_id]" class="board-form-control">
                                    <option value="">전체</option>
                                    @foreach($operatingInstitutions as $institution)
                                        <option value="{{ $institution->id }}">{{ $institution->name_ko }}@if($institution->name_en) / {{ $institution->name_en }}@endif</option>
                                    @endforeach
                                </select>
                                <select name="recipient_filters[0][project_period_id]" class="board-form-control">
                                    <option value="">전체</option>
                                    @foreach($projectPeriods as $period)
                                        <option value="{{ $period->id }}">{{ $period->name_ko }}@if($period->name_en) / {{ $period->name_en }}@endif</option>
                                    @endforeach
                                </select>
                                <select name="recipient_filters[0][country_id]" class="board-form-control">
                                    <option value="">전체</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name_ko }}@if($country->name_en) / {{ $country->name_en }}@endif</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger btn-sm remove-filter-btn">삭제</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="add_filter_btn">추가</button>
                    </div>
                </div>

                <!-- 주소록 발송 섹션 -->
                <div id="address_book_section" class="recipient-section" style="display: {{ old('recipient_type') == 'address_book' ? 'block' : 'none' }};">
                    <div class="board-form-group">
                        <label class="board-form-label">주소록 선택</label>
                        @foreach($addressBooks as $addressBook)
                            <div class="board-checkbox-item">
                                <input type="checkbox" id="address_book_{{ $addressBook->id }}" name="address_book_ids[]" value="{{ $addressBook->id }}" @checked(in_array($addressBook->id, old('address_book_ids', [])))>
                                <label for="address_book_{{ $addressBook->id }}">{{ $addressBook->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 테스트 발송 섹션 -->
                <div id="test_section" class="recipient-section" style="display: {{ old('recipient_type') == 'test' ? 'block' : 'none' }};">
                    <div class="board-form-group">
                        <label for="test_email" class="board-form-label">테스트 이메일</label>
                        <input type="email" id="test_email" name="test_email" class="board-form-control" value="{{ old('test_email') }}">
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">
                        발송여부 <span class="required">*</span>
                    </label>
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="radio" id="dispatch_status_saved" name="dispatch_status" value="saved" @checked(old('dispatch_status', 'saved') == 'saved') required>
                            <label for="dispatch_status_saved">일반저장</label>
                        </div>
                        <div class="board-option-item">
                            <input type="radio" id="dispatch_status_scheduled" name="dispatch_status" value="scheduled" @checked(old('dispatch_status') == 'scheduled') required>
                            <label for="dispatch_status_scheduled">재발송</label>
                        </div>
                    </div>
                </div>

                <div id="scheduled_at_section" class="board-form-group" style="display: {{ old('dispatch_status') == 'scheduled' ? 'block' : 'none' }};">
                    <label for="scheduled_at" class="board-form-label">발송일</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" class="board-form-control" value="{{ old('scheduled_at') }}">
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">첨부파일</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">저장</button>
                    <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary">목록</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="{{ asset('js/backoffice/mail-form.js') }}"></script>
@endsection
