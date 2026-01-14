/**
 * 게시글 작성/수정 페이지 JavaScript (범용)
 */

// CSRF 토큰을 전역 변수로 설정
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Summernote 에디터 초기화
function initSummernote() {
    if (typeof $.fn.summernote !== 'undefined') {
        $('#content').summernote({
            height: 400,
            lang: 'ko-KR',
            // HTML 태그 필터링 비활성화
            disableHtml: false,
            // 드래그 앤 드롭 비활성화 (썸네일/첨부파일과 충돌 방지)
            disableDragAndDrop: true,
            // 허용할 HTML 태그 설정
            allowedTags: ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'strike', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'td', 'th', 'iframe', 'video', 'source'],
            // 허용할 속성 설정
            allowedAttributes: {
                '*': ['style', 'class', 'id'],
                'iframe': ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'title', 'allow'],
                'img': ['src', 'alt', 'width', 'height'],
                'a': ['href', 'target'],
                'table': ['border', 'cellpadding', 'cellspacing'],
                'td': ['colspan', 'rowspan']
            },
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color', 'forecolor', 'backcolor']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video', 'table']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['help']]
            ],
            fontNames: ['맑은 고딕', '굴림체', '바탕체', 'Arial', 'Times New Roman', 'Courier New', 'Verdana'],
            fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72'],
            callbacks: {
                onImageUpload: function(files) {
                    for (let i = 0; i < files.length; i++) {
                        uploadImage(files[i], this);
                    }
                },
                onInit: function() {
                    // Summernote 초기화 완료
                },
                onChange: function(contents, $editable) {
                    // 콘텐츠 변경 시 실제 textarea에 동기화
                    $('#content').val(contents);
                },
                onBlur: function() {
                    // 포커스 잃을 때 최종 동기화
                    const content = $('#content').summernote('code');
                    $('#content').val(content);
                }
            }
        });
        
        // 드롭다운 이벤트 강제 활성화 및 재클릭 문제 해결
        setTimeout(function() {
            // 모든 드롭다운 버튼에 대해 이벤트 재설정
            $('.note-btn-group .dropdown-toggle').off('click.dropdownFix').on('click.dropdownFix', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // 현재 드롭다운 상태 확인
                const $this = $(this);
                const $dropdown = $this.next('.note-dropdown-menu');
                
                // 다른 드롭다운 닫기
                $('.note-dropdown-menu').not($dropdown).hide();
                
                // 현재 드롭다운 토글
                if ($dropdown.is(':visible')) {
                    $dropdown.hide();
                } else {
                    $dropdown.show();
                }
            });
            
            // 드롭다운 외부 클릭 시 닫기
            $(document).on('click.dropdownFix', function(e) {
                if (!$(e.target).closest('.note-btn-group').length) {
                    $('.note-dropdown-menu').hide();
                }
            });
            
            // 드롭다운 메뉴 항목 클릭 시 메뉴 닫기
            $('.note-dropdown-menu .note-btn').on('click', function() {
                $(this).closest('.note-dropdown-menu').hide();
            });
            
        }, 1000);
        
        // Summernote tooltip 완전 비활성화
        setTimeout(function() {
            // 모든 버튼의 title과 data-original-title 속성 제거
            $('.note-toolbar .note-btn').each(function() {
                $(this).removeAttr('title').removeAttr('data-original-title');
            });
            
            // tooltip 관련 이벤트 제거
            $('.note-toolbar .note-btn').off('mouseenter mouseleave');
            
            // Bootstrap tooltip 비활성화
            $('.note-toolbar .note-btn').tooltip('dispose');
        }, 1500);
        
        // Summernote 드래그 앤 드롭 완전 비활성화
        setTimeout(function() {
            // Summernote 에디터 영역의 모든 드래그 앤 드롭 이벤트 제거
            $('.note-editable').off('dragover dragenter dragleave drop');
            $('.note-editable').on('dragover dragenter dragleave drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
            
            // Summernote 에디터 영역의 드래그 앤 드롭 관련 CSS 클래스 제거
            $('.note-editable').removeClass('note-drag-over');
        }, 2000);
        
        // Summernote 모달 문제 해결 - modal-backdrop 즉시 제거
        setTimeout(function() {
            // modal-backdrop 즉시 제거 및 모달 스타일 적용
            setInterval(function() {
                // modal-backdrop 즉시 제거
                $('.modal-backdrop').remove();
                
                $('.note-modal').each(function() {
                    const $modal = $(this);
                    if ($modal.is(':visible')) {
                        $modal.css({
                            'z-index': '999999',
                            'position': 'fixed',
                            'top': '0',
                            'left': '0',
                            'width': '100%',
                            'height': '100%',
                            'background-color': 'rgba(0, 0, 0, 0.5)',
                            'pointer-events': 'auto'
                        });
                        
                        $modal.find('.modal-dialog').css({
                            'z-index': '1000000',
                            'position': 'relative',
                            'margin': '50px auto',
                            'pointer-events': 'auto'
                        });
                        
                        $modal.find('.modal-content').css({
                            'z-index': '1000001',
                            'pointer-events': 'auto',
                            'position': 'relative'
                        });
                        
                        // 모달 내부 모든 요소에 클릭 이벤트 강제 활성화
                        $modal.find('*').css('pointer-events', 'auto');
                    }
                });
            }, 10);
        }, 100);
        
        // Bootstrap 모달 이벤트에서 modal-backdrop만 제거
        $(document).on('show.bs.modal', '.note-modal', function(e) {
            // modal-backdrop만 제거, 모달은 정상 동작
            setTimeout(function() {
                $('.modal-backdrop').remove();
            }, 10);
        });
        
        $(document).on('shown.bs.modal', '.note-modal', function(e) {
            // modal-backdrop만 제거, 모달은 정상 동작
            $('.modal-backdrop').remove();
        });
        
    } else {
        console.error('Summernote가 로드되지 않았습니다!');
        // 폴백: 일반 textarea로 표시
        $('#content').show().attr('style', 'height: 400px; resize: vertical;');
    }
}

// 커스텀 필드 에디터 초기화
function initCustomFieldEditors() {
    if (typeof $.fn.summernote !== 'undefined') {
        $('.summernote-editor').each(function() {
            $(this).summernote({
                height: 300,
                lang: 'ko-KR',
                // HTML 태그 필터링 비활성화
                disableHtml: false,
                // 드래그 앤 드롭 비활성화 (썸네일/첨부파일과 충돌 방지)
                disableDragAndDrop: true,
                // 허용할 HTML 태그 설정
                allowedTags: ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'strike', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'td', 'th', 'iframe', 'video', 'source'],
                // 허용할 속성 설정
                allowedAttributes: {
                    '*': ['style', 'class', 'id'],
                    'iframe': ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'title', 'allow'],
                    'img': ['src', 'alt', 'width', 'height'],
                    'a': ['href', 'target'],
                    'table': ['border', 'cellpadding', 'cellspacing'],
                    'td': ['colspan', 'rowspan']
                },
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color', 'forecolor', 'backcolor']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'table']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                fontNames: ['맑은 고딕', '굴림체', '바탕체', 'Arial', 'Times New Roman', 'Courier New', 'Verdana'],
                fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72'],
                callbacks: {
                    onImageUpload: function(files) {
                        for (let i = 0; i < files.length; i++) {
                            uploadImage(files[i], this);
                        }
                    },
                    onChange: function(contents, $editable) {
                        // 커스텀 필드 에디터 콘텐츠 변경 시 실제 textarea에 동기화
                        const editorId = $editable.attr('id');
                        if (editorId) {
                            $('#' + editorId).val(contents);
                        }
                    },
                    onBlur: function() {
                        // 포커스 잃을 때 최종 동기화
                        const content = $(this).summernote('code');
                        const editorId = $(this).attr('id');
                        if (editorId) {
                            $('#' + editorId).val(content);
                        }
                    }
                }
            });
        });
        
    }
}

// 이미지 업로드 함수
function uploadImage(file, editor) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', window.csrfToken);
    
    fetch('/backoffice/upload-image', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.uploaded) {
            $(editor).summernote('insertImage', result.url);
        } else {
            alert('이미지 업로드에 실패했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('이미지 업로드 중 오류가 발생했습니다.');
    });
}


// URL을 embed 코드로 변환
function convertToEmbedCode(url) {
    let videoId = '';
    let embedUrl = '';
    
    // YouTube URL 처리 - 다양한 형식 지원
    if (url.includes('youtube.com/watch')) {
        const match = url.match(/[?&]v=([^&]+)/);
        if (match) {
            videoId = match[1];
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
        }
    } else if (url.includes('youtu.be/')) {
        const match = url.match(/youtu\.be\/([^?&]+)/);
        if (match) {
            videoId = match[1];
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
        }
    } else if (url.includes('youtube.com/embed/')) {
        // 이미 embed URL인 경우
        const match = url.match(/youtube\.com\/embed\/([^?&]+)/);
        if (match) {
            videoId = match[1];
            embedUrl = url; // 그대로 사용
        }
    }
    // Vimeo URL 처리
    else if (url.includes('vimeo.com/')) {
        const match = url.match(/vimeo\.com\/(\d+)/);
        if (match) {
            videoId = match[1];
            embedUrl = `https://player.vimeo.com/video/${videoId}`;
        }
    }
    
    if (embedUrl) {
        return `<div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; margin: 20px 0;">
            <iframe src="${embedUrl}" 
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                    frameborder="0" 
                    allowfullscreen>
            </iframe>
        </div>`;
    }
    
    return null;
}

// 썸네일 이미지 관리 클래스
class ThumbnailManager {
    constructor() {
        this.thumbnailInput = document.getElementById('thumbnail');
        this.thumbnailPreview = document.getElementById('thumbnailPreview');
        this.thumbnailUpload = this.thumbnailInput?.closest('.board-file-upload');
        this.maxFileSize = 5 * 1024 * 1024; // 5MB
        this.allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (this.thumbnailInput && this.thumbnailUpload) {
            this.init();
        }
    }
    
    init() {
        // 파일 선택 이벤트
        this.thumbnailInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.handleThumbnail(file);
            }
        });
        
        // 드래그 앤 드롭 이벤트 (썸네일 전용)
        this.thumbnailUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단
            this.thumbnailUpload.classList.add('board-file-drag-over');
        });
        
        this.thumbnailUpload.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단
            this.thumbnailUpload.classList.remove('board-file-drag-over');
        });
        
        this.thumbnailUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단 - 첨부파일 영역과 완전 분리
            this.thumbnailUpload.classList.remove('board-file-drag-over');
            
            const file = e.dataTransfer.files[0]; // 첫 번째 파일만
            if (file) {
                this.handleThumbnail(file);
            }
        });
    }
    
    // 썸네일 파일 처리
    handleThumbnail(file) {
        // 파일 타입 체크
        if (!this.allowedTypes.includes(file.type)) {
            alert('이미지 파일만 업로드 가능합니다. (JPG, PNG, GIF)');
            this.thumbnailInput.value = '';
            return;
        }
        
        // 파일 크기 체크
        if (file.size > this.maxFileSize) {
            alert('썸네일 이미지는 5MB 이하만 가능합니다.');
            this.thumbnailInput.value = '';
            return;
        }
        
        // FileList를 DataTransfer로 변환
        const dt = new DataTransfer();
        dt.items.add(file);
        this.thumbnailInput.files = dt.files;
        
        // 미리보기 생성
        this.updateThumbnailPreview(file);
    }
    
    // 썸네일 미리보기 업데이트
    updateThumbnailPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.thumbnailPreview.innerHTML = `
                <div class="board-file-item">
                    <div class="board-file-info">
                        <img src="${e.target.result}" alt="썸네일 미리보기" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">
                        <span class="board-file-name">${file.name}</span>
                        <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                    </div>
                    <button type="button" class="board-file-remove" onclick="thumbnailManager.removeThumbnail()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
    
    // 썸네일 제거
    removeThumbnail() {
        // 파일 입력 필드 초기화
        if (this.thumbnailInput) {
            this.thumbnailInput.value = '';
        }
        
        // 기존 썸네일 숨겨진 입력 필드 제거
        const existingThumbnailInput = document.querySelector('input[name="existing_thumbnail"]');
        if (existingThumbnailInput) {
            existingThumbnailInput.remove();
        }
        
        // 미리보기 영역 초기화
        if (this.thumbnailPreview) {
            this.thumbnailPreview.innerHTML = '';
        }
    }
}

// 첨부파일 관리 클래스
class FileManager {
    constructor() {
        this.fileInput = document.getElementById('attachments');
        this.filePreview = document.getElementById('filePreview');
        this.fileUpload = this.fileInput?.closest('.board-file-upload');
        this.maxFiles = 5;
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        
        if (this.fileInput && this.fileUpload) {
            this.init();
        }
    }
    
    init() {
        // 파일 선택 이벤트
        this.fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                this.replaceAllFiles(files);
            }
        });
        
        // 드래그 앤 드롭 이벤트 (첨부파일 전용)
        this.fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단
            this.fileUpload.classList.add('board-file-drag-over');
        });
        
        this.fileUpload.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단
            this.fileUpload.classList.remove('board-file-drag-over');
        });
        
        this.fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation(); // 이벤트 전파 차단 - 썸네일 영역과 완전 분리
            this.fileUpload.classList.remove('board-file-drag-over');
            
            const files = Array.from(e.dataTransfer.files);
            this.handleFiles(files);
        });
    }
    
    // 파일 교체 함수 (기존 파일을 모두 지우고 새 파일로 교체)
    replaceAllFiles(files) {
        // 파일 개수 제한 체크
        if (files.length > this.maxFiles) {
            alert(`최대 ${this.maxFiles}개까지만 선택할 수 있습니다.`);
            this.fileInput.value = '';
            return;
        }
        
        // 파일 크기 체크
        const oversizedFiles = files.filter(file => file.size > this.maxFileSize);
        if (oversizedFiles.length > 0) {
            alert('10MB 이상인 파일이 있습니다. 10MB 이하의 파일만 선택해주세요.');
            this.fileInput.value = '';
            return;
        }
        
        // FileList를 DataTransfer로 변환
        const dt = new DataTransfer();
        files.forEach(file => dt.items.add(file));
        this.fileInput.files = dt.files;
        
        // 미리보기 생성
        this.updateFilePreview();
    }
    
    // 파일 처리 함수 (드래그 앤 드롭용 - 기존 파일에 추가)
    handleFiles(files) {
        // 파일 개수 제한 체크
        if (files.length > this.maxFiles) {
            alert(`최대 ${this.maxFiles}개까지만 선택할 수 있습니다.`);
            return;
        }
        
        // 파일 크기 체크
        const oversizedFiles = files.filter(file => file.size > this.maxFileSize);
        if (oversizedFiles.length > 0) {
            alert('10MB 이상인 파일이 있습니다. 10MB 이하의 파일만 선택해주세요.');
            return;
        }
        
        // 중복 파일 체크 (파일명 기준)
        const existingFiles = Array.from(this.fileInput.files);
        const newFiles = files.filter(newFile => 
            !existingFiles.some(existingFile => 
                existingFile.name === newFile.name && 
                existingFile.size === newFile.size
            )
        );
        
        if (newFiles.length === 0) {
            alert('이미 추가된 파일입니다.');
            return;
        }
        
        // 기존 파일과 새 파일 합치기
        const allFiles = [...existingFiles, ...newFiles];
        
        if (allFiles.length > this.maxFiles) {
            alert(`최대 ${this.maxFiles}개까지만 선택할 수 있습니다.`);
            return;
        }
        
        // FileList를 DataTransfer로 변환
        const dt = new DataTransfer();
        allFiles.forEach(file => dt.items.add(file));
        this.fileInput.files = dt.files;
        
        // 미리보기 생성
        this.updateFilePreview();
    }
    
    // 파일 미리보기 업데이트
    updateFilePreview() {
        const files = Array.from(this.fileInput.files);
        this.filePreview.innerHTML = '';
        
        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'board-file-item';
            fileItem.innerHTML = `
                <div class="board-file-info">
                    <i class="fas fa-file"></i>
                    <span class="board-file-name">${file.name}</span>
                    <span class="board-file-size">(${(file.size / 1024 / 1024).toFixed(2)}MB)</span>
                </div>
                <button type="button" class="board-file-remove" onclick="fileManager.removeFile(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            this.filePreview.appendChild(fileItem);
        });
    }
    
    // 파일 제거 함수
    removeFile(index) {
        const dt = new DataTransfer();
        const files = Array.from(this.fileInput.files);
        
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        
        this.fileInput.files = dt.files;
        
        // 미리보기 업데이트
        this.updateFilePreview();
    }
    
    // 기존 첨부파일 제거 함수
    removeExistingFile(index) {
        const existingFiles = document.querySelectorAll('.board-attachment-list .existing-file');
        const targetFile = existingFiles[index];
        const hiddenInput = targetFile.querySelector('input[name="existing_attachments[]"]');
        
        if (hiddenInput) {
            hiddenInput.remove(); // 숨겨진 입력 필드 제거
            targetFile.remove(); // 화면에서 제거
        }
    }
}

// 모달창 내 Insert Video 버튼 기능 교체
$(document).ready(function() {
    // 모달이 나타날 때마다 체크하여 Insert Video 버튼 기능 교체
    setInterval(function() {
        const insertBtn = $('.note-video-btn[value="Insert Video"]');
        if (insertBtn.length > 0 && !insertBtn.data('custom-attached')) {
            insertBtn.data('custom-attached', true);
            insertBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // 모달창의 URL 입력값 가져오기
                const videoUrl = $('.note-video-url').val();
                
                if (videoUrl && videoUrl.trim() !== '') {
                    const embedCode = convertToEmbedCode(videoUrl.trim());
                    
                    if (embedCode) {
                        // HTML 문자열을 DOM 요소로 변환
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = embedCode;
                        const videoElement = tempDiv.firstElementChild;
                        
                        $('#content').summernote('insertNode', videoElement);
                        
                        // 모달창 닫기
                        $('.modal').modal('hide');
                    } else {
                        alert('지원하지 않는 비디오 URL입니다. YouTube 또는 Vimeo URL을 입력해주세요.');
                    }
                } else {
                    alert('비디오 URL을 입력해주세요.');
                }
                
                return false;
            });
        }
    }, 500);
});

// 폼 제출 전 콘텐츠 동기화 함수
function syncEditorContent() {
    // 메인 에디터 콘텐츠 동기화
    if ($('#content').length && typeof $('#content').summernote === 'function') {
        const content = $('#content').summernote('code');
        $('#content').val(content);
    }
    
    // 커스텀 필드 에디터들 콘텐츠 동기화
    $('.summernote-editor').each(function() {
        const editorId = $(this).attr('id');
        if (editorId && typeof $(this).summernote === 'function') {
            const content = $(this).summernote('code');
            $('#' + editorId).val(content);
        }
    });
}

// 프로젝트 기수 선택 관리 클래스
class ProjectTermSelector {
    constructor(container) {
        this.container = container;
        this.fieldName = container.dataset.fieldName;
        this.selects = {
            term: container.querySelector('[data-level="term"]'),
            course: container.querySelector('[data-level="course"]'),
            institution: container.querySelector('[data-level="institution"]'),
            period: container.querySelector('[data-level="period"]'),
            country: container.querySelector('[data-level="country"]')
        };
        this.hiddenInput = container.querySelector('input[type="hidden"]');
        this.init();
    }
    
    init() {
        // 인스턴스를 컨테이너에 저장 (폼 제출 시 접근용)
        this.container._projectTermSelector = this;
        
        // 각 선택 박스에 change 이벤트 추가
        this.selects.term.addEventListener('change', () => this.handleTermChange());
        this.selects.course.addEventListener('change', () => this.handleCourseChange());
        this.selects.institution.addEventListener('change', () => this.handleInstitutionChange());
        this.selects.period.addEventListener('change', () => this.handlePeriodChange());
        this.selects.country.addEventListener('change', () => this.handleCountryChange());
        
        // 수정 모드: 기존 선택값 복원 (term select의 value 또는 dataset.selected 확인)
        const termId = this.selects.term.value || this.selects.term.dataset.selected;
        if (termId) {
            // term select에 value가 없으면 설정
            if (!this.selects.term.value && termId) {
                this.selects.term.value = termId;
            }
            this.loadInitialData();
        }
    }
    
    async loadInitialData() {
        const termId = this.selects.term.value;
        if (termId) {
            await this.loadCourses(termId);
            const courseId = this.selects.course.dataset.selected || this.selects.course.value;
            if (courseId) {
                this.selects.course.value = courseId;
                await this.loadInstitutions(courseId);
                const institutionId = this.selects.institution.dataset.selected || this.selects.institution.value;
                if (institutionId) {
                    this.selects.institution.value = institutionId;
                    await this.loadPeriods(institutionId);
                    const periodId = this.selects.period.dataset.selected || this.selects.period.value;
                    if (periodId) {
                        this.selects.period.value = periodId;
                        await this.loadCountries(periodId);
                        const countryId = this.selects.country.dataset.selected || this.selects.country.value;
                        if (countryId) {
                            this.selects.country.value = countryId;
                        }
                    }
                }
            }
        }
        this.updateHiddenInput();
    }
    
    async handleTermChange() {
        this.resetSelect(this.selects.course);
        this.resetSelect(this.selects.institution);
        this.resetSelect(this.selects.period);
        this.resetSelect(this.selects.country);
        
        const termId = this.selects.term.value;
        if (termId) {
            await this.loadCourses(termId);
        }
        this.updateHiddenInput();
        this.triggerStudentUpdate();
    }
    
    async handleCourseChange() {
        this.resetSelect(this.selects.institution);
        this.resetSelect(this.selects.period);
        this.resetSelect(this.selects.country);
        
        const courseId = this.selects.course.value;
        if (courseId) {
            await this.loadInstitutions(courseId);
        }
        this.updateHiddenInput();
        this.triggerStudentUpdate();
    }
    
    async handleInstitutionChange() {
        this.resetSelect(this.selects.period);
        this.resetSelect(this.selects.country);
        
        const institutionId = this.selects.institution.value;
        if (institutionId) {
            await this.loadPeriods(institutionId);
        }
        this.updateHiddenInput();
        this.triggerStudentUpdate();
    }
    
    async handlePeriodChange() {
        this.resetSelect(this.selects.country);
        
        const periodId = this.selects.period.value;
        if (periodId) {
            await this.loadCountries(periodId);
        }
        this.updateHiddenInput();
        this.triggerStudentUpdate();
    }
    
    handleCountryChange() {
        this.updateHiddenInput();
        this.triggerStudentUpdate();
    }
    
    async loadCourses(termId) {
        try {
            const response = await fetch(`/backoffice/courses/get-by-term/${termId}`);
            const courses = await response.json();
            this.populateSelect(this.selects.course, courses, 'name_ko', 'name_en');
            this.selects.course.disabled = false;
        } catch (error) {
            console.error('과정 로드 오류:', error);
        }
    }
    
    async loadInstitutions(courseId) {
        try {
            const response = await fetch(`/backoffice/operating-institutions/get-by-course/${courseId}`);
            const institutions = await response.json();
            this.populateSelect(this.selects.institution, institutions, 'name_ko', 'name_en');
            this.selects.institution.disabled = false;
        } catch (error) {
            console.error('운영기관 로드 오류:', error);
        }
    }
    
    async loadPeriods(institutionId) {
        try {
            const response = await fetch(`/backoffice/project-periods/get-by-institution/${institutionId}`);
            const periods = await response.json();
            this.populateSelect(this.selects.period, periods, 'name_ko', 'name_en');
            this.selects.period.disabled = false;
        } catch (error) {
            console.error('프로젝트기간 로드 오류:', error);
        }
    }
    
    async loadCountries(periodId) {
        try {
            const response = await fetch(`/backoffice/countries/get-by-period/${periodId}`);
            const countries = await response.json();
            this.populateSelect(this.selects.country, countries, 'name_ko', 'name_en');
            this.selects.country.disabled = false;
        } catch (error) {
            console.error('국가 로드 오류:', error);
        }
    }
    
    populateSelect(select, items, nameKoField, nameEnField) {
        select.innerHTML = '<option value="">전체</option>';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item[nameKoField] + (item[nameEnField] ? ' / ' + item[nameEnField] : '');
            select.appendChild(option);
        });
    }
    
    resetSelect(select) {
        select.innerHTML = '<option value="">전체</option>';
        select.disabled = true;
        select.value = '';
    }
    
    updateHiddenInput() {
        const data = {
            project_term_id: this.selects.term.value || null,
            course_id: this.selects.course.value || null,
            operating_institution_id: this.selects.institution.value || null,
            project_period_id: this.selects.period.value || null,
            country_id: this.selects.country.value || null
        };
        this.hiddenInput.value = JSON.stringify(data);
    }
    
    triggerStudentUpdate() {
        // 학생 선택 필드에 변경 이벤트 전달
        const event = new CustomEvent('projectTermChanged', {
            detail: {
                projectTermId: this.selects.term.value,
                courseId: this.selects.course.value,
                institutionId: this.selects.institution.value,
                periodId: this.selects.period.value,
                countryId: this.selects.country.value
            }
        });
        document.dispatchEvent(event);
    }
}

// 학생 선택 관리 클래스
class StudentSelector {
    constructor(container) {
        this.container = container;
        this.fieldName = container.dataset.fieldName;
        this.emptyMessage = container.querySelector('.student-list-empty');
        this.studentList = container.querySelector('.student-list');
        this.hiddenInput = container.querySelector('input[type="hidden"]');
        this.init();
    }
    
    init() {
        // 프로젝트 기수 변경 이벤트 리스닝
        document.addEventListener('projectTermChanged', (e) => this.handleProjectTermChange(e.detail));
    }
    
    async handleProjectTermChange(data) {
        // 조건이 하나라도 있으면 학생 로드
        if (data.projectTermId || data.courseId || data.institutionId || data.periodId || data.countryId) {
            await this.loadStudents(data);
        } else {
            this.showEmptyMessage();
        }
    }
    
    async loadStudents(filters) {
        try {
            const params = new URLSearchParams();
            if (filters.projectTermId) params.append('project_term_id', filters.projectTermId);
            if (filters.courseId) params.append('course_id', filters.courseId);
            if (filters.institutionId) params.append('operating_institution_id', filters.institutionId);
            if (filters.periodId) params.append('project_period_id', filters.periodId);
            if (filters.countryId) params.append('country_id', filters.countryId);
            
            const response = await fetch(`/backoffice/board-posts/get-members-by-project-term?${params}`);
            const students = await response.json();
            
            this.renderStudents(students);
        } catch (error) {
            console.error('학생 로드 오류:', error);
            this.showEmptyMessage();
        }
    }
    
    renderStudents(students) {
        if (!students || students.length === 0) {
            this.showEmptyMessage();
            return;
        }
        
        this.emptyMessage.style.display = 'none';
        this.studentList.style.display = 'block';
        this.studentList.innerHTML = '';
        
        // 기존 선택된 학생 ID 배열 가져오기
        const selectedIds = this.getSelectedStudentIds();
        
        students.forEach(student => {
            const div = document.createElement('div');
            div.className = 'board-checkbox-item';
            div.style.marginBottom = '8px';
            div.innerHTML = `
                <input type="checkbox" 
                       class="board-checkbox-input student-checkbox" 
                       id="student_${this.fieldName}_${student.id}" 
                       value="${student.id}"
                       ${selectedIds.includes(student.id) ? 'checked' : ''}>
                <label for="student_${this.fieldName}_${student.id}" class="board-form-label">
                    ${student.name}
                </label>
            `;
            this.studentList.appendChild(div);
        });
        
        // 체크박스 변경 이벤트 리스닝
        this.studentList.querySelectorAll('.student-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.updateHiddenInput());
        });
    }
    
    showEmptyMessage() {
        this.emptyMessage.style.display = 'block';
        this.studentList.style.display = 'none';
        this.studentList.innerHTML = '';
        this.updateHiddenInput();
    }
    
    getSelectedStudentIds() {
        try {
            const value = this.hiddenInput.value;
            if (!value) return [];
            const data = JSON.parse(value);
            return data.student_ids || [];
        } catch {
            return [];
        }
    }
    
    updateHiddenInput() {
        const selectedIds = Array.from(this.studentList.querySelectorAll('.student-checkbox:checked'))
            .map(cb => parseInt(cb.value));
        this.hiddenInput.value = JSON.stringify({ student_ids: selectedIds });
    }
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    // jQuery 로드 확인 후 Summernote 초기화
    if (typeof $ !== 'undefined') {
        initSummernote();
        initCustomFieldEditors(); // 커스텀 필드 에디터도 초기화
    } else {
        // jQuery가 로드되지 않은 경우 대기
        const checkJQuery = setInterval(() => {
            if (typeof $ !== 'undefined') {
                clearInterval(checkJQuery);
                initSummernote();
                initCustomFieldEditors(); // 커스텀 필드 에디터도 초기화
            }
        }, 100);
    }
    
    // 썸네일 관리자 초기화 (첨부파일과 완전 분리)
    window.thumbnailManager = new ThumbnailManager();
    
    // 첨부파일 관리자 초기화 (썸네일과 완전 분리)
    window.fileManager = new FileManager();
    
    // 기존 첨부파일 제거 함수를 전역으로 사용할 수 있도록 설정
    window.removeExistingFile = function(index) {
        window.fileManager.removeExistingFile(index);
    };
    
    // 썸네일 제거 함수를 전역으로 사용할 수 있도록 설정
    window.removeThumbnail = function() {
        if (window.thumbnailManager) {
            // 썸네일 매니저의 제거 함수 호출 (기존 썸네일 처리 포함)
            window.thumbnailManager.removeThumbnail();
        }
    };
    
    // 프로젝트 기수 선택 초기화
    document.querySelectorAll('.project-term-selector').forEach(container => {
        new ProjectTermSelector(container);
    });
    
    // 학생 선택 초기화
    document.querySelectorAll('.student-selector').forEach(container => {
        new StudentSelector(container);
    });
    
    // 표출일자 필드 초기화
    document.querySelectorAll('.display-date-range-selector').forEach(container => {
        initDisplayDateRange(container);
    });
    
    // 저장 버튼 클릭 시 폼 제출 강제 (공통)
    function initFormSubmitHandler() {
        if (typeof $ !== 'undefined') {
            $('button[type="submit"]').off('click.formSubmit').on('click.formSubmit', function(e) {
                // 기본 폼 제출 방지 (중복 제출 방지)
                e.preventDefault();
                e.stopPropagation();
                
                // submitting 상태 제거
                const $button = $(this);
                $button.removeClass('submitting');
                $button.prop('disabled', false);
                
                // 약간의 지연 후 폼 제출 강제
                setTimeout(function() {
                    const $form = $button.closest('form');
                    if ($form.length && !$form.data('submitting')) {
                        $form.data('submitting', true);
                        $form[0].submit();
                    }
                }, 50);
            });
        }
    }
    
    // jQuery가 로드된 경우 즉시 실행, 아니면 대기
    if (typeof $ !== 'undefined') {
        initFormSubmitHandler();
    } else {
        const checkJQueryForSubmit = setInterval(() => {
            if (typeof $ !== 'undefined') {
                clearInterval(checkJQueryForSubmit);
                initFormSubmitHandler();
            }
        }, 100);
    }
    
    // 표출일자 입력 박스 전체 클릭 시 달력 열기 (공통)
    document.querySelectorAll('.display-date-input').forEach(function(input) {
        input.addEventListener('click', function(e) {
            if (this.disabled) return;
            // showPicker() 메서드가 지원되는 경우 사용
            if (typeof this.showPicker === 'function') {
                try {
                    this.showPicker();
                } catch (err) {
                    // showPicker가 실패하면 기본 동작 (focus) 사용
                    this.focus();
                }
            } else {
                // showPicker가 지원되지 않으면 focus로 달력 열기 시도
                this.focus();
            }
        });
    });
    
    // 폼 제출 전 콘텐츠 동기화 및 커스텀 필드 업데이트
    if (typeof $ !== 'undefined') {
        $('form').off('submit.formSync').on('submit.formSync', function(e) {
            // 이미 제출 중이면 중복 제출 방지
            if ($(this).data('submitting')) {
                e.preventDefault();
                return false;
            }
            
            // 에디터 콘텐츠 동기화
            syncEditorContent();
            
            // 프로젝트 기수 필드 업데이트
            document.querySelectorAll('.project-term-selector').forEach(container => {
                const selector = container._projectTermSelector;
                if (selector) {
                    selector.updateHiddenInput();
                }
            });
            
            // 표출일자 필드 업데이트
            document.querySelectorAll('.display-date-range-selector').forEach(container => {
                const fieldName = container.dataset.fieldName;
                const checkbox = container.querySelector(`#custom_field_${fieldName}_use`);
                const startDateInput = container.querySelector(`#custom_field_${fieldName}_start`);
                const endDateInput = container.querySelector(`#custom_field_${fieldName}_end`);
                const hiddenInput = container.querySelector(`#custom_field_${fieldName}`);
                
                // 체크박스가 있으면 체크박스 상태 사용, 없으면 항상 true
                if (startDateInput && endDateInput && hiddenInput) {
                    const useDisplayDate = checkbox ? checkbox.checked : true;
                    const data = {
                        use_display_date: useDisplayDate,
                        start_date: startDateInput.value || '',
                        end_date: endDateInput.value || ''
                    };
                    hiddenInput.value = JSON.stringify(data);
                }
            });
            
            // submitting 상태 제거
            $(this).find('button[type="submit"]').removeClass('submitting').prop('disabled', false);
        });
    }
});

// 표출일자 필드 제어 함수
function initDisplayDateRange(container) {
    const fieldName = container.dataset.fieldName;
    const checkbox = container.querySelector(`#custom_field_${fieldName}_use`);
    const startDateInput = container.querySelector(`#custom_field_${fieldName}_start`);
    const endDateInput = container.querySelector(`#custom_field_${fieldName}_end`);
    const hiddenInput = container.querySelector(`#custom_field_${fieldName}`);
    
    // 체크박스가 없어도 동작하도록 수정
    if (!startDateInput || !endDateInput || !hiddenInput) {
        return;
    }
    
    // 체크박스가 있으면 체크박스 변경 시 날짜 필드 활성/비활성화
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            const isChecked = this.checked;
            startDateInput.disabled = !isChecked;
            endDateInput.disabled = !isChecked;
            
            if (!isChecked) {
                // 체크 해제 시 날짜 필드 초기화
                startDateInput.value = '';
                endDateInput.value = '';
            }
            
            updateDisplayDateHiddenInput();
        });
    }
    
    // 날짜 변경 시 hidden 필드 업데이트
    startDateInput.addEventListener('change', updateDisplayDateHiddenInput);
    endDateInput.addEventListener('change', updateDisplayDateHiddenInput);
    
    // hidden 필드 업데이트 함수
    function updateDisplayDateHiddenInput() {
        const useDisplayDate = checkbox ? checkbox.checked : true;
        const data = {
            use_display_date: useDisplayDate,
            start_date: startDateInput.value || '',
            end_date: endDateInput.value || ''
        };
        hiddenInput.value = JSON.stringify(data);
    }
    
    // 초기 상태 설정
    updateDisplayDateHiddenInput();
}