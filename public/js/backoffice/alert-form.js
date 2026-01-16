/**
 * 알림 생성/수정 폼 JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initSummernote();
    initFileUpload();
    initExistingFileDelete();
});

/**
 * Summernote 에디터 초기화
 */
function initSummernote() {
    if (typeof $ === 'undefined' || typeof $.fn.summernote === 'undefined') {
        console.error('jQuery 또는 Summernote가 로드되지 않았습니다.');
        return;
    }

    $('.summernote-editor').summernote({
        height: 300,
        lang: 'ko-KR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                uploadImage(files[0], $(this));
            }
        }
    });
}

/**
 * 이미지 업로드
 */
function uploadImage(file, editor) {
    const formData = new FormData();
    formData.append('image', file);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/backoffice/upload-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.uploaded && data.url) {
            editor.summernote('insertImage', data.url);
        } else {
            alert('이미지 업로드에 실패했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('이미지 업로드 중 오류가 발생했습니다.');
    });
}

/**
 * 파일 업로드 초기화
 */
function initFileUpload() {
    const container = document.getElementById('file-upload-container');
    if (!container) return;

    // 파일 추가 버튼
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-file-btn')) {
            addFileInput();
        } else if (e.target.classList.contains('remove-file-btn')) {
            const item = e.target.closest('.file-upload-item');
            if (item && container.querySelectorAll('.file-upload-item').length > 1) {
                item.remove();
            }
        }
    });
}

/**
 * 파일 입력 필드 추가
 */
function addFileInput() {
    const container = document.getElementById('file-upload-container');
    const newItem = document.createElement('div');
    newItem.className = 'file-upload-item';
    newItem.innerHTML = `
        <input type="file" name="files[]" class="file-input">
        <button type="button" class="btn btn-sm btn-danger remove-file-btn">-</button>
        <button type="button" class="btn btn-sm btn-success add-file-btn">+</button>
    `;
    container.appendChild(newItem);
}

/**
 * 기존 파일 삭제 초기화
 */
function initExistingFileDelete() {
    const container = document.getElementById('existing-files');
    if (!container) return;

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-existing-file')) {
            const fileId = e.target.getAttribute('data-file-id');
            const fileItem = e.target.closest('.existing-file-item');
            const hiddenInput = fileItem.querySelector('.deleted-file-id');
            
            if (hiddenInput) {
                hiddenInput.value = fileId;
            }
            
            fileItem.style.display = 'none';
        }
    });
}
