@extends('backoffice.layouts.app')

@section('title', '주소록 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/mail-address-books.css') }}">
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.mail-address-books.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card mail-address-books">
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

            <form action="{{ route('backoffice.mail-address-books.store') }}" method="POST" id="address_book_form" enctype="multipart/form-data">
                @csrf

                <div class="board-form-group">
                    <label for="name" class="board-form-label">
                        주소록 정보 <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="board-form-control" value="{{ old('name') }}" required>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">엑셀 등록</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="excel_file" name="excel_file" accept=".csv,.xlsx,.xls">
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">엑셀 파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">CSV, XLSX, XLS 파일만 가능 (최대 5MB)</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="excel_file_preview"></div>
                    </div>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">엑셀 샘플</label>
                    <a href="{{ route('backoffice.mail-address-books.excel-sample') }}" class="btn btn-secondary btn-sm">샘플 다운</a>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">주소록 리스트</label>
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>이름</th>
                                    <th>이메일</th>
                                    <th>연락처</th>
                                    <th>등록일</th>
                                    <th>관리</th>
                                </tr>
                            </thead>
                            <tbody id="contacts_table_body">
                                <!-- 신규 연락처 행은 JavaScript로 동적 추가 -->
                            </tbody>
                        </table>
                        <div style="margin-top: 10px;">
                            <button type="button" class="btn btn-secondary btn-sm" id="add_contact_row_btn">추가</button>
                        </div>
                    </div>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 저장
                    </button>
                    <a href="{{ route('backoffice.mail-address-books.index') }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/mail-address-book-form.js') }}"></script>
@endsection
