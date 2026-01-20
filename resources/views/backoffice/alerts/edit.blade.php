@extends('backoffice.layouts.app')

@section('title', '알림 수정')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/summernote-custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="board-container">
    @php
        // 디버깅용: memberId 확인
        // dd(['memberId' => $memberId, 'query' => request()->query('member_id')]);
    @endphp
    <div class="board-header">
        <a href="{{ route('backoffice.alerts.index', !empty($memberId) ? ['member_id' => $memberId] : []) }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('backoffice.alerts.update', $alert) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if(isset($memberId) && $memberId)
                    <input type="hidden" name="member_id" value="{{ $memberId }}">
                @endif

                <div class="board-form-group">
                    <div class="board-options-list board-options-horizontal">
                        <div class="board-option-item">
                            <input type="checkbox" id="is_notice" name="is_notice" value="1" @checked(old('is_notice', $alert->is_notice))>
                            <label for="is_notice">공지사항</label>
                        </div>
                    </div>
                    <small class="board-form-text">*체크하면 목록 최상단에 표시됩니다.</small>
                </div>

                <div class="board-form-group">
                    <label for="korean_title" class="board-form-label">
                        국문 제목 <span class="required">*</span>
                    </label>
                    <input type="text" id="korean_title" name="korean_title" class="board-form-control" value="{{ old('korean_title', $alert->korean_title) }}" required>
                </div>

                <div class="board-form-group">
                    <label for="english_title" class="board-form-label">
                        영문 제목 <span class="required">*</span>
                    </label>
                    <input type="text" id="english_title" name="english_title" class="board-form-control" value="{{ old('english_title', $alert->english_title) }}" required>
                </div>

                <div class="board-form-group">
                    <label for="korean_content" class="board-form-label">
                        국문 내용 <span class="required">*</span>
                    </label>
                    <textarea id="korean_content" name="korean_content" class="board-form-control board-form-textarea summernote-editor" required>{{ old('korean_content', $alert->korean_content) }}</textarea>
                </div>

                <div class="board-form-group">
                    <label for="english_content" class="board-form-label">
                        영문 내용 <span class="required">*</span>
                    </label>
                    <textarea id="english_content" name="english_content" class="board-form-control board-form-textarea summernote-editor" required>{{ old('english_content', $alert->english_content) }}</textarea>
                </div>

                @if($alert->files->count() > 0)
                <div class="board-form-group">
                    <label class="board-form-label">기존 첨부파일</label>
                    <div id="existing-files">
                        @foreach($alert->files as $file)
                            <div class="existing-file-item" style="display: flex; align-items: center; gap: 10px; padding: 10px; background-color: #f5f5f5; border-radius: 4px; margin-bottom: 10px;">
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" style="flex: 1; color: #1976d2; text-decoration: none;">{{ $file->file_name }}</a>
                                <button type="button" class="btn btn-sm btn-danger remove-existing-file" data-file-id="{{ $file->id }}">삭제</button>
                                <input type="hidden" name="deleted_file_ids[]" value="" class="deleted-file-id">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="board-form-group">
                    <label class="board-form-label">새 첨부파일</label>
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
                    <a href="{{ route('backoffice.alerts.index', isset($memberId) && $memberId ? ['member_id' => $memberId] : []) }}" class="btn btn-secondary">목록</a>
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
<script src="{{ asset('js/backoffice/alert-form.js') }}"></script>
@endsection
