@extends('backoffice.layouts.app')

@section('title', '주소록 수정')

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

            <form action="{{ route('backoffice.mail-address-books.update', $addressBook) }}" method="POST" id="address_book_form" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="board-form-group">
                    <label for="name" class="board-form-label">
                        주소록 명 <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="board-form-control" value="{{ old('name', $addressBook->name) }}" required>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">엑셀 등록</label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="excel_file" name="excel_file" accept=".csv,.xlsx,.xls">
                            <div class="board-file-input-content">
                                <span class="board-file-input-text">엑셀 파일을 선택하거나 여기로 드래그하세요 (CSV, XLSX, XLS 파일만 가능, 최대 5MB)</span>
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
                                @foreach($addressBook->contacts as $contact)
                                    <tr data-contact-id="{{ $contact->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><input type="text" class="form-control contact-name" value="{{ $contact->name }}"></td>
                                        <td><input type="email" class="form-control contact-email" value="{{ $contact->email }}"></td>
                                        <td><input type="text" class="form-control contact-phone" value="{{ $contact->phone }}"></td>
                                        <td>{{ $contact->created_at->format('Y.m.d') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm update-contact-btn" data-contact-id="{{ $contact->id }}">수정</button>
                                            <button type="button" class="btn btn-danger btn-sm delete-contact-btn" data-contact-id="{{ $contact->id }}">삭제</button>
                                        </td>
                                    </tr>
                                @endforeach
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
<script>
    window.addressBookId = {{ $addressBook->id }};
</script>
<script src="{{ asset('js/backoffice/mail-address-book-form.js') }}"></script>
@endsection
