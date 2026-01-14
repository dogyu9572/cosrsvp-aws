@extends('backoffice.layouts.app')

@section('title', ($board->name ?? '게시판'))

@section('styles')
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'top-notices') }}" class="btn btn-secondary btn-sm">
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

            <form action="{{ route('backoffice.board-posts.store', $board->slug ?? 'top-notices') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($board->isNoticeEnabled())
                <div class="board-form-group">
                    <div class="board-checkbox-item">
                        <input type="checkbox" 
                               class="board-checkbox-input" 
                               id="is_notice" 
                               name="is_notice" 
                               value="1" 
                               @checked(old('is_notice') == '1')>
                        <label for="is_notice" class="board-form-label">
                            <i class="fas fa-bullhorn"></i> 공지글
                        </label>
                    </div>
                    <small class="board-form-text">체크하면 공지글로 설정되어 최상단에 표시됩니다.</small>
                </div>
                @endif

                <!-- 프로젝트 기수 및 학생 선택 필드 (맨 위로 이동) -->
                @if($board->custom_fields_config && count($board->custom_fields_config) > 0)
                    @foreach($board->custom_fields_config as $fieldConfig)
                        @if(in_array($fieldConfig['type'], ['project_term', 'student_select']))
                            <div class="board-form-group">
                                <label for="custom_field_{{ $fieldConfig['name'] }}" class="board-form-label">
                                    {{ $fieldConfig['label'] }}
                                    @if($fieldConfig['required'])
                                        <span class="required">*</span>
                                    @endif
                                </label>
                                
                                @if($fieldConfig['type'] === 'project_term')
                                    <!-- 프로젝트 기수 선택 (5단계 계층 구조) -->
                                    <div class="project-term-selector" data-field-name="{{ $fieldConfig['name'] }}">
                                        <div class="board-form-row" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                            <div class="board-form-col" style="flex: 1; min-width: 150px;">
                                                <label class="board-form-label" style="font-size: 12px; margin-bottom: 5px;">기수</label>
                                                <select class="board-form-control project-term-select" 
                                                        id="custom_field_{{ $fieldConfig['name'] }}_term" 
                                                        name="custom_field_{{ $fieldConfig['name'] }}_term"
                                                        data-level="term"
                                                        @if($fieldConfig['required']) required @endif>
                                                    <option value="">전체</option>
                                                    @foreach(\App\Models\ProjectTerm::active()->orderBy('created_at', 'desc')->get() as $term)
                                                        <option value="{{ $term->id }}" @selected(old('custom_field_' . $fieldConfig['name'] . '_term') == $term->id)>
                                                            {{ $term->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="board-form-col" style="flex: 1; min-width: 150px;">
                                                <label class="board-form-label" style="font-size: 12px; margin-bottom: 5px;">과정</label>
                                                <select class="board-form-control project-term-select" 
                                                        id="custom_field_{{ $fieldConfig['name'] }}_course" 
                                                        name="custom_field_{{ $fieldConfig['name'] }}_course"
                                                        data-level="course"
                                                        disabled>
                                                    <option value="">전체</option>
                                                </select>
                                            </div>
                                            <div class="board-form-col" style="flex: 1; min-width: 150px;">
                                                <label class="board-form-label" style="font-size: 12px; margin-bottom: 5px;">운영기관</label>
                                                <select class="board-form-control project-term-select" 
                                                        id="custom_field_{{ $fieldConfig['name'] }}_institution" 
                                                        name="custom_field_{{ $fieldConfig['name'] }}_institution"
                                                        data-level="institution"
                                                        disabled>
                                                    <option value="">전체</option>
                                                </select>
                                            </div>
                                            <div class="board-form-col" style="flex: 1; min-width: 150px;">
                                                <label class="board-form-label" style="font-size: 12px; margin-bottom: 5px;">프로젝트기간</label>
                                                <select class="board-form-control project-term-select" 
                                                        id="custom_field_{{ $fieldConfig['name'] }}_period" 
                                                        name="custom_field_{{ $fieldConfig['name'] }}_period"
                                                        data-level="period"
                                                        disabled>
                                                    <option value="">전체</option>
                                                </select>
                                            </div>
                                            <div class="board-form-col" style="flex: 1; min-width: 150px;">
                                                <label class="board-form-label" style="font-size: 12px; margin-bottom: 5px;">국가</label>
                                                <select class="board-form-control project-term-select" 
                                                        id="custom_field_{{ $fieldConfig['name'] }}_country" 
                                                        name="custom_field_{{ $fieldConfig['name'] }}_country"
                                                        data-level="country"
                                                        disabled>
                                                    <option value="">전체</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- 숨겨진 필드: 선택된 값들을 JSON으로 저장 -->
                                        <input type="hidden" 
                                               id="custom_field_{{ $fieldConfig['name'] }}" 
                                               name="custom_field_{{ $fieldConfig['name'] }}" 
                                               value="{{ old('custom_field_' . $fieldConfig['name']) }}">
                                    </div>
                                @elseif($fieldConfig['type'] === 'student_select')
                                    <!-- 학생 선택 필드 (프로젝트 기수 선택에 따라 동적 로드) -->
                                    <div class="student-selector" data-field-name="{{ $fieldConfig['name'] }}">
                                        <div class="student-list-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                            <div class="student-list-empty" style="color: #6c757d; text-align: center; padding: 20px;">
                                                프로젝트 기수를 선택하면 학생 목록이 표시됩니다.
                                            </div>
                                            <div class="student-list" style="display: none;">
                                                <!-- 학생 체크박스가 여기에 동적으로 추가됨 -->
                                            </div>
                                        </div>
                                        <!-- 숨겨진 필드: 선택된 학생 ID 배열을 JSON으로 저장 -->
                                        <input type="hidden" 
                                               id="custom_field_{{ $fieldConfig['name'] }}" 
                                               name="custom_field_{{ $fieldConfig['name'] }}" 
                                               value="{{ old('custom_field_' . $fieldConfig['name']) }}">
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif

                @if($board->isFieldEnabled('category') && $categoryOptions && $categoryOptions->count() > 0)
                <div class="board-form-group">
                    <label for="category" class="board-form-label">
                        카테고리 분류
                        @if($board->isFieldRequired('category'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <select class="board-form-control" id="category" name="category" @if($board->isFieldRequired('category')) required @endif>
                        <option value="">카테고리를 선택하세요</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category->name }}" @selected(old('category') == $category->name)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($board->isFieldEnabled('title'))
                <input type="hidden" id="title" name="title" value="{{ old('title', '띠공지') }}" @if($board->isFieldRequired('title')) required @endif>
                @endif

                @if($board->isFieldEnabled('content'))
                <div class="board-form-group">
                    <label for="content" class="board-form-label">
                        내용
                        @if($board->isFieldRequired('content'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <input type="text" class="board-form-control" id="content" name="content" value="{{ old('content') }}" style="height: auto; min-height: 38px;" @if($board->isFieldRequired('content')) required @endif>
                </div>
                @endif

                <!-- 표출일자 필드 (별도 처리) -->
                @if($board->custom_fields_config && count($board->custom_fields_config) > 0)
                    @foreach($board->custom_fields_config as $fieldConfig)
                        @if($fieldConfig['type'] === 'display_date_range')
                            @php
                                $displayDateData = old('custom_field_' . $fieldConfig['name']) ? json_decode(old('custom_field_' . $fieldConfig['name']), true) : ['use_display_date' => false, 'start_date' => '', 'end_date' => ''];
                                $useDisplayDate = $displayDateData['use_display_date'] ?? false;
                                $startDate = $displayDateData['start_date'] ?? '';
                                $endDate = $displayDateData['end_date'] ?? '';
                            @endphp
                            <div class="board-form-group display-date-range-selector" data-field-name="{{ $fieldConfig['name'] }}">
                                <label class="board-form-label">
                                    {{ $fieldConfig['label'] }}
                                    @if($fieldConfig['required'])
                                        <span class="required">*</span>
                                    @endif
                                </label>
                                <div class="board-checkbox-item" style="margin-bottom: 10px; margin-left: 0; padding-left: 0;">
                                    <input type="checkbox" 
                                           class="board-checkbox-input" 
                                           id="custom_field_{{ $fieldConfig['name'] }}_use" 
                                           name="custom_field_{{ $fieldConfig['name'] }}_use" 
                                           value="1"
                                           @checked($useDisplayDate)>
                                    <label for="custom_field_{{ $fieldConfig['name'] }}_use" class="board-form-label" style="margin-left: 0;">
                                        표출일자 사용
                                    </label>
                                </div>
                                <div class="date-range-inputs" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px; margin-left: 0; padding-left: 0; max-width: 600px;">
                                    <input type="date" 
                                           class="board-form-control display-date-input" 
                                           id="custom_field_{{ $fieldConfig['name'] }}_start" 
                                           style="flex: 1; max-width: 250px;"
                                           value="{{ $startDate }}"
                                           @if(!$useDisplayDate) disabled @endif>
                                    <span style="margin: 0 5px;">~</span>
                                    <input type="date" 
                                           class="board-form-control display-date-input" 
                                           id="custom_field_{{ $fieldConfig['name'] }}_end" 
                                           style="flex: 1; max-width: 250px;"
                                           value="{{ $endDate }}"
                                           @if(!$useDisplayDate) disabled @endif>
                                </div>
                                <small class="board-form-text" style="color: #6c757d;">
                                    *표출일자를 사용하지 않을시, 상시 표출되는 팝업이 생성됩니다.
                                </small>
                                <input type="hidden" 
                                       id="custom_field_{{ $fieldConfig['name'] }}" 
                                       name="custom_field_{{ $fieldConfig['name'] }}" 
                                       value="{{ old('custom_field_' . $fieldConfig['name'], json_encode(['use_display_date' => $useDisplayDate, 'start_date' => $startDate, 'end_date' => $endDate])) }}">
                            </div>
                        @endif
                    @endforeach
                @endif

                @if($board->enable_sorting)
                <div class="board-form-group">
                    <label for="sort_order" class="board-form-label">정렬 순서</label>
                    <input type="number" class="board-form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $nextSortOrder ?? 0) }}" min="0">
                    <small class="board-form-text">숫자가 클수록 위에 표시됩니다.</small>
                </div>
                @endif

                <!-- 커스텀 필드 입력 폼 (프로젝트 기수, 학생 선택, 표출일자 제외) -->
                @if($board->custom_fields_config && count($board->custom_fields_config) > 0)
                    @foreach($board->custom_fields_config as $fieldConfig)
                        @if(!in_array($fieldConfig['type'], ['project_term', 'student_select', 'display_date_range']))
                            <div class="board-form-group">
                                <label for="custom_field_{{ $fieldConfig['name'] }}" class="board-form-label">
                                    {{ $fieldConfig['label'] }}
                                    @if($fieldConfig['required'])
                                        <span class="required">*</span>
                                    @endif
                                </label>
                                
                                @if($fieldConfig['type'] === 'text')
                                <input type="text" 
                                       class="board-form-control" 
                                       id="custom_field_{{ $fieldConfig['name'] }}" 
                                       name="custom_field_{{ $fieldConfig['name'] }}" 
                                       value="{{ old('custom_field_' . $fieldConfig['name']) }}"
                                       placeholder="{{ $fieldConfig['placeholder'] ?? '' }}"
                                       @if($fieldConfig['required']) required @endif>
                            @elseif($fieldConfig['type'] === 'select')
                                @if($fieldConfig['options'])
                                    <select class="board-form-control" 
                                            id="custom_field_{{ $fieldConfig['name'] }}" 
                                            name="custom_field_{{ $fieldConfig['name'] }}"
                                            @if($fieldConfig['required']) required @endif>
                                        <option value="">선택하세요</option>
                                        @foreach(explode(",", $fieldConfig['options']) as $option)
                                            @php $option = trim($option); @endphp
                                            @if(!empty($option))
                                                <option value="{{ $option }}" @selected(old('custom_field_' . $fieldConfig['name']) == $option)>
                                                    {{ $option }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <div class="board-form-text text-muted">셀렉박스는 선택 옵션이 필요합니다.</div>
                                @endif
                            @elseif($fieldConfig['type'] === 'checkbox')
                                @if($fieldConfig['options'])
                                    <div class="board-options-list board-options-horizontal">
                                        @foreach(explode(",", $fieldConfig['options']) as $option)
                                            @php $option = trim($option); @endphp
                                            @if(!empty($option))
                                                <div class="board-option-item">
                                                    <input type="checkbox" 
                                                           id="option_{{ $fieldConfig['name'] }}_{{ $loop->index }}" 
                                                           name="custom_field_{{ $fieldConfig['name'] }}[]" 
                                                           value="{{ $option }}"
                                                           {{ in_array($option, old('custom_field_' . $fieldConfig['name'], [])) ? 'checked' : '' }}>
                                                    <label for="option_{{ $fieldConfig['name'] }}_{{ $loop->index }}">{{ $option }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="board-checkbox-item">
                                        <input type="checkbox" 
                                               class="board-checkbox-input" 
                                               id="custom_field_{{ $fieldConfig['name'] }}" 
                                               name="custom_field_{{ $fieldConfig['name'] }}" 
                                               value="1"
                                               {{ old('custom_field_' . $fieldConfig['name']) == '1' ? 'checked' : '' }}>
                                        <label for="custom_field_{{ $fieldConfig['name'] }}" class="board-form-label">
                                            {{ $fieldConfig['label'] }}
                                        </label>
                                    </div>
                                @endif
                            @elseif($fieldConfig['type'] === 'radio')
                                @if($fieldConfig['options'])
                                    <div class="board-options-list board-options-horizontal">
                                        @foreach(explode(",", $fieldConfig['options']) as $option)
                                            @php $option = trim($option); @endphp
                                            @if(!empty($option))
                                                <div class="board-option-item">
                                                    <input type="radio" 
                                                           id="option_{{ $fieldConfig['name'] }}_{{ $loop->index }}" 
                                                           name="custom_field_{{ $fieldConfig['name'] }}" 
                                                           value="{{ $option }}"
                                                           @checked(old('custom_field_' . $fieldConfig['name']) == $option)
                                                           @if($fieldConfig['required']) required @endif>
                                                    <label for="option_{{ $fieldConfig['name'] }}_{{ $loop->index }}">{{ $option }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="board-form-text text-muted">라디오 버튼은 선택 옵션이 필요합니다.</div>
                                @endif
                            @elseif($fieldConfig['type'] === 'date')
                                <input type="date" 
                                       class="board-form-control" 
                                       id="custom_field_{{ $fieldConfig['name'] }}" 
                                       name="custom_field_{{ $fieldConfig['name'] }}" 
                                       value="{{ old('custom_field_' . $fieldConfig['name']) }}"
                                       @if($fieldConfig['required']) required @endif>
                            @elseif($fieldConfig['type'] === 'editor')
                                <textarea class="board-form-control board-form-textarea summernote-editor" 
                                          id="custom_field_{{ $fieldConfig['name'] }}" 
                                          name="custom_field_{{ $fieldConfig['name'] }}" 
                                          rows="10"
                                          @if($fieldConfig['required']) required @endif>{{ old('custom_field_' . $fieldConfig['name']) }}</textarea>
                            @endif
                            
                            @if($fieldConfig['max_length'] && in_array($fieldConfig['type'], ['text']))
                                <small class="board-form-text">최대 {{ $fieldConfig['max_length'] }}자 (영어 기준)까지 입력 가능합니다.</small>
                            @endif
                        </div>
                        @endif
                    @endforeach
                @endif
                

                @if($board->isFieldEnabled('author_name'))
                <div class="board-form-group">
                    <label for="author_name" class="board-form-label">
                        작성자
                        @if($board->isFieldRequired('author_name'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <input type="text" class="board-form-control" id="author_name" name="author_name" value="{{ old('author_name') }}" @if($board->isFieldRequired('author_name')) required @endif>
                </div>
                @endif

                @if($board->isFieldEnabled('password'))
                <div class="board-form-group">
                    <label for="password" class="board-form-label">
                        비밀번호
                        @if($board->isFieldRequired('password'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <input type="password" class="board-form-control" id="password" name="password" @if($board->isFieldRequired('password')) required @endif>
                    <small class="board-form-text">게시글 수정/삭제 시 사용할 비밀번호를 입력하세요.</small>
                </div>
                @endif

                @if($board->isFieldEnabled('is_secret'))
                <div class="board-form-group">
                    <div class="board-checkbox-item">
                        <input type="checkbox" 
                               class="board-checkbox-input" 
                               id="is_secret" 
                               name="is_secret" 
                               value="1" 
                               @checked(old('is_secret') == '1')>
                        <label for="is_secret" class="board-form-label">
                            <i class="fas fa-lock"></i> 비밀글
                        </label>
                    </div>
                    <small class="board-form-text">체크하면 본인만 조회할 수 있습니다.</small>
                </div>
                @endif

                @if($board->isFieldEnabled('attachments'))
                <div class="board-form-group">
                    <label class="board-form-label">
                        첨부파일
                        @if($board->isFieldRequired('attachments'))
                            <span class="required">*</span>
                        @endif
                    </label>
                    <div class="board-file-upload">
                        <div class="board-file-input-wrapper">
                            <input type="file" class="board-file-input" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar" @if($board->isFieldRequired('attachments')) required @endif>
                            <div class="board-file-input-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                                <span class="board-file-input-subtext">최대 5개, 각 파일 10MB 이하</span>
                            </div>
                        </div>
                        <div class="board-file-preview" id="filePreview"></div>
                    </div>
                </div>
                @endif

            <div class="board-form-actions">
                <button type="submit" class="btn btn-primary" data-skip-button="true">
                    <i class="fas fa-save"></i> 저장
                </button>
                <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'top-notices') }}" class="btn btn-secondary">취소</a>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- jQuery, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- initSummernote 오버라이드: board-post-form.js 로드 전에 실행 -->
    <script>
        // initSummernote 함수를 미리 정의하여 오버라이드 준비
        // board-post-form.js가 로드되면 즉시 오버라이드
        (function() {
            // DOMContentLoaded 이벤트를 capture phase에서 먼저 등록
            document.addEventListener('DOMContentLoaded', function(e) {
                if (typeof window.initSummernote === 'function') {
                    const originalInitSummernote = window.initSummernote;
                    window.initSummernote = function() {
                        try {
                            const contentElement = $('#content');
                            if (contentElement.length && contentElement.prop('tagName') === 'INPUT') {
                                // input 타입이면 에디터 초기화하지 않고 바로 리턴
                                console.log('[DEBUG] initSummernote: #content가 input이므로 초기화 건너뜀');
                                return;
                            }
                            return originalInitSummernote.apply(this, arguments);
                        } catch (error) {
                            console.error('[DEBUG] initSummernote 오류:', error);
                            // 오류 발생 시에도 계속 진행
                            return;
                        }
                    };
                    console.log('[DEBUG] initSummernote 오버라이드 완료 (DOMContentLoaded)');
                }
            }, true); // capture phase에서 실행
            
            // board-post-form.js 로드 직후 오버라이드 시도
            function overrideInitSummernote() {
                if (typeof window.initSummernote === 'function') {
                    const originalInitSummernote = window.initSummernote;
                    window.initSummernote = function() {
                        try {
                            const contentElement = $('#content');
                            if (contentElement.length && contentElement.prop('tagName') === 'INPUT') {
                                console.log('[DEBUG] initSummernote: #content가 input이므로 초기화 건너뜀');
                                return;
                            }
                            return originalInitSummernote.apply(this, arguments);
                        } catch (error) {
                            console.error('[DEBUG] initSummernote 오류:', error);
                            return;
                        }
                    };
                    console.log('[DEBUG] initSummernote 오버라이드 완료 (로드 직후)');
                    return true;
                }
                return false;
            }
            
            // 즉시 시도
            if (overrideInitSummernote()) {
                return;
            }
            
            // 주기적으로 체크 (최대 5초)
            let checkCount = 0;
            const maxChecks = 50;
            const interval = setInterval(function() {
                checkCount++;
                if (overrideInitSummernote() || checkCount >= maxChecks) {
                    clearInterval(interval);
                }
            }, 100);
        })();
    </script>
    
    <script src="{{ asset('js/backoffice/board-post-form.js') }}"></script>
    <script>
        // board-post-form.js 로드 직후 즉시 오버라이드
        (function() {
            function overrideInitSummernote() {
                if (typeof window.initSummernote === 'function') {
                    const originalInitSummernote = window.initSummernote;
                    window.initSummernote = function() {
                        try {
                            const contentElement = $('#content');
                            if (contentElement.length && contentElement.prop('tagName') === 'INPUT') {
                                console.log('[DEBUG] initSummernote: #content가 input이므로 초기화 건너뜀');
                                return;
                            }
                            return originalInitSummernote.apply(this, arguments);
                        } catch (error) {
                            console.error('[DEBUG] initSummernote 오류:', error);
                            return;
                        }
                    };
                    console.log('[DEBUG] initSummernote 오버라이드 완료 (스크립트 로드 직후)');
                    return true;
                }
                return false;
            }
            
            // 즉시 시도
            overrideInitSummernote();
            
            // DOMContentLoaded 후에도 한 번 더 시도
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', overrideInitSummernote);
            } else {
                overrideInitSummernote();
            }
        })();
    </script>
    <script>
        // 띠공지 게시판: #content 에디터 초기화 방지 및 스타일 제거
        $(document).ready(function() {
            // initSummernote 함수가 호출되기 전에 #content를 input으로 감지하도록 처리
            const originalInitSummernote = window.initSummernote;
            if (typeof originalInitSummernote === 'function') {
                window.initSummernote = function() {
                    const contentElement = $('#content');
                    if (contentElement.length && contentElement.prop('tagName') === 'INPUT') {
                        // input 타입이면 에디터 초기화하지 않고 바로 리턴
                        return;
                    }
                    return originalInitSummernote.apply(this, arguments);
                };
            }
            
            // #content에 적용된 높이 스타일 제거 (인라인 스타일 포함)
            const contentElement = $('#content');
            if (contentElement.length && contentElement.prop('tagName') === 'INPUT') {
                contentElement.removeAttr('style').css({
                    'height': 'auto',
                    'min-height': '38px',
                    'resize': 'none'
                });
            }
            
            // 나중에 실행될 수 있는 스타일 적용도 방지
            setTimeout(function() {
                const contentEl = $('#content');
                if (contentEl.length && contentEl.prop('tagName') === 'INPUT') {
                    contentEl.removeAttr('style').css({
                        'height': 'auto',
                        'min-height': '38px',
                        'resize': 'none'
                    });
                }
            }, 100);
            
        });
    </script>
@endsection
