@extends('backoffice.layouts.app')

@section('title', '문의 상세')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/summernote-custom.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/backoffice/inquiries.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container inquiries-show">
        <div class="board-header">
            <a href="{{ route('backoffice.inquiries.index') }}" class="btn btn-secondary btn-sm">
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

                <!-- 문의 회원 정보 -->
                <div class="board-form-group">
                    <label class="board-form-label">문의 회원 정보</label>
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label class="board-form-label">성명</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->name ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">성별</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->gender ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">생년월일</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->birth_date ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">현지 전화번호</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->local_phone ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">한국 전화번호</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->korean_phone ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">이메일</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->email ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">여권번호</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->passport_number ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">여권유효기간</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->passport_expiry ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label class="board-form-label">국가</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->country->name_ko ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">직업</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->occupation ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">전공</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->major ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">소속</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->affiliation ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">부서</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->department ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">직위</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->position ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">외국인등록번호</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->foreigner_registration_number ?? '' }}" readonly>
                            </div>
                            <div class="board-form-group">
                                <label class="board-form-label">외국인등록증 유효기간</label>
                                <input type="text" class="board-form-control" value="{{ $inquiry->user->foreigner_registration_expiry ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 문의 내용 -->
                <div class="board-form-group">
                    <label class="board-form-label">제목</label>
                    <input type="text" class="board-form-control" value="{{ $inquiry->title }}" readonly>
                </div>

                <div class="board-form-group">
                    <label class="board-form-label">문의내용</label>
                    <textarea class="board-form-control board-form-textarea" rows="10" readonly>{{ $inquiry->content }}</textarea>
                </div>

                @if($inquiry->attachments && count($inquiry->attachments) > 0)
                <div class="board-form-group">
                    <label class="board-form-label">첨부파일</label>
                    <div class="board-attachment-list">
                        @foreach($inquiry->attachments as $attachment)
                            <div class="board-attachment-item">
                                <i class="fas fa-file"></i>
                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" download class="board-attachment-name">
                                    {{ $attachment['name'] }}
                                </a>
                                @if(isset($attachment['size']))
                                    <span class="board-attachment-size">({{ number_format($attachment['size'] / 1024 / 1024, 2) }}MB)</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 답변 관리 -->
                    <form action="{{ route('backoffice.inquiries.reply', $inquiry->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="board-form-group">
                            <label for="reply_content" class="board-form-label">
                                답변내용
                                <span class="required">*</span>
                            </label>
                            <textarea class="board-form-control board-form-textarea summernote-editor" 
                                      id="reply_content" 
                                      name="reply_content" 
                                      rows="15" 
                                      required>{{ old('reply_content', $inquiry->reply_content) }}</textarea>
                        </div>

                        <div class="board-form-group">
                            <label class="board-form-label">
                                첨부파일
                            </label>
                            <div class="board-file-upload">
                                <div class="board-file-input-wrapper">
                                    <input type="file" class="board-file-input" id="reply_attachments" name="reply_attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                                    <div class="board-file-input-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                        <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                                    </div>
                                </div>
                                
                                @if($inquiry->reply_attachments && count($inquiry->reply_attachments) > 0)
                                    <div class="board-existing-files">
                                        <div class="board-attachment-list">
                                            @foreach($inquiry->reply_attachments as $index => $attachment)
                                                <div class="board-attachment-item existing-file" data-index="{{ $index }}">
                                                    <i class="fas fa-file"></i>
                                                    <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" download class="board-attachment-name">
                                                        {{ $attachment['name'] }}
                                                    </a>
                                                    @if(isset($attachment['size']))
                                                        <span class="board-attachment-size">({{ number_format($attachment['size'] / 1024 / 1024, 2) }}MB)</span>
                                                    @endif
                                                    <button type="button" class="board-attachment-remove" onclick="removeExistingReplyFile({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <input type="hidden" name="existing_reply_attachments[{{ $index }}]" value="{{ json_encode($attachment) }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="board-file-preview" id="replyFilePreview"></div>
                            </div>
                        </div>

                        <div class="board-form-group">
                            <label class="board-form-label">
                                답변여부
                                <span class="required">*</span>
                            </label>
                            <div class="board-options-list board-options-horizontal">
                                <div class="board-option-item">
                                    <input type="radio" 
                                           id="reply_status_pending" 
                                           name="reply_status" 
                                           value="pending" 
                                           @checked(old('reply_status', $inquiry->reply_status) === 'pending' || old('reply_status') === null && $inquiry->reply_status === null) 
                                           required>
                                    <label for="reply_status_pending">미완료</label>
                                </div>
                                <div class="board-option-item">
                                    <input type="radio" 
                                           id="reply_status_completed" 
                                           name="reply_status" 
                                           value="completed" 
                                           @checked(old('reply_status', $inquiry->reply_status) === 'completed') 
                                           required>
                                    <label for="reply_status_completed">완료</label>
                                </div>
                            </div>
                        </div>

                        <div class="board-form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 저장
                            </button>
                            <a href="{{ route('backoffice.inquiries.index') }}" class="btn btn-secondary">취소</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- jQuery, Bootstrap, Summernote JS (순서 중요!) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="{{ asset('js/backoffice/inquiry-form.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-post-form.js') }}"></script>
@endsection
