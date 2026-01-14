/**
 * 문의 관리 답변 작성 페이지 JavaScript
 */

// CSRF 토큰을 전역 변수로 설정
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Summernote 에디터 초기화 (답변 내용 필드)
function initInquirySummernote() {
    if (typeof $.fn.summernote !== 'undefined') {
        $('#reply_content').summernote({
            height: 400,
            lang: 'ko-KR',
            disableHtml: false,
            disableDragAndDrop: true,
            allowedTags: ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'strikethrough', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'td', 'th', 'iframe', 'video', 'source'],
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
                    $('#reply_content').val(contents);
                },
                onBlur: function() {
                    // 포커스 잃을 때 최종 동기화
                    const content = $('#reply_content').summernote('code');
                    $('#reply_content').val(content);
                }
            }
        });
    }
}

// 이미지 업로드 함수
function uploadImage(file, editor) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', window.csrfToken);

    $.ajax({
        url: '/backoffice/upload-image',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.url) {
                $(editor).summernote('insertImage', response.url);
            } else {
                alert('이미지 업로드에 실패했습니다.');
            }
        },
        error: function() {
            alert('이미지 업로드에 실패했습니다.');
        }
    });
}

// 답변 첨부파일 삭제 처리
let removedReplyIndices = [];
function removeExistingReplyFile(index) {
    const attachmentItem = document.querySelector(`.existing-file[data-index="${index}"]`);
    if (attachmentItem) {
        attachmentItem.remove();
        removedReplyIndices.push(index);
        
        // hidden input 업데이트
        let removeInput = document.querySelector('input[name="remove_reply_attachments[]"]');
        if (!removeInput) {
            removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_reply_attachments[]';
            document.querySelector('form').appendChild(removeInput);
        }
        
        // 배열로 처리하기 위해 각 인덱스마다 hidden input 생성
        removedReplyIndices.forEach(idx => {
            let existingRemoveInput = document.querySelector(`input[name="remove_reply_attachments[]"][value="${idx}"]`);
            if (!existingRemoveInput) {
                let newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'remove_reply_attachments[]';
                newInput.value = idx;
                document.querySelector('form').appendChild(newInput);
            }
        });
    }
}

// 답변 첨부파일 미리보기
function initReplyAttachmentsPreview() {
    $('#reply_attachments').on('change', function() {
        const files = this.files;
        const preview = $('#replyFilePreview');
        preview.empty();
        
        if (files.length > 0) {
            Array.from(files).forEach((file, index) => {
                const fileItem = $('<div class="board-attachment-item new-file">')
                    .html('<i class="fas fa-file"></i> ' + 
                          '<span class="board-attachment-name">' + file.name + '</span> ' +
                          '<span class="board-attachment-size">(' + (file.size / 1024 / 1024).toFixed(2) + 'MB)</span>');
                preview.append(fileItem);
            });
        }
    });
}

// DOM 로드 완료 시 초기화
$(document).ready(function() {
    // Summernote 에디터 초기화
    if ($('#reply_content').length > 0) {
        initInquirySummernote();
    }
    
    // 답변 첨부파일 미리보기 초기화
    if ($('#reply_attachments').length > 0) {
        initReplyAttachmentsPreview();
    }
});
