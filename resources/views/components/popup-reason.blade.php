<div class="popup" id="pop_reason">
	<div class="dm" onclick="layerHide('pop_reason');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_reason');">Close</button>
		<div class="tit">Submit documents</div>
		<div class="scroll">
			<form id="documentUploadForm" enctype="multipart/form-data">
				<input type="hidden" id="document_id" name="document_id" value="">
				<div class="con file_wrap">
					<div class="tt" id="document_name">Self-introduction</div>
					<label class="input_file">
						<input type="file" id="document_file" name="file" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png">
						<span>File attachment</span>
					</label>
					<div class="file_list"></div>
					<div class="error_message" style="color: red; margin-top: 10px; display: none;"></div>
				</div>
				<button type="submit" class="btn_clo">Submit</button>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {
    let currentDocumentId = null;
    let currentDocumentName = '';
    
    // 파일 선택 시 파일명 표시
    $('#pop_reason .file_wrap input[type="file"]').on('change', function () {
        const file = this.files[0];
        const $wrap = $(this).closest('.file_wrap');
        const $list = $wrap.find('.file_list');
        const $error = $wrap.find('.error_message');
        
        $error.hide();
        
        if (!file) {
            $list.empty();
            $wrap.removeClass('on');
            return;
        }
        
        // 파일 크기 체크 (10MB)
        if (file.size > 10 * 1024 * 1024) {
            $error.text('파일 크기는 10MB 이하여야 합니다.').show();
            $(this).val('');
            return;
        }
        
        // 파일 확장자 체크
        const allowedExtensions = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            $error.text('pdf, doc, docx, zip, rar, jpg, jpeg, png 파일만 업로드 가능합니다.').show();
            $(this).val('');
            return;
        }
        
        const fileName = file.name;
        $list.empty();
        $list.append(`<button type="button" class="file_item">${fileName}</button>`);
        $wrap.addClass('on');
    });
    
    // 파일 삭제
    $(document).on('click', '#pop_reason .file_list .file_item', function () {
        const $wrap = $(this).closest('.file_wrap');
        $(this).remove();
        $wrap.removeClass('on');
        $wrap.find('input[type="file"]').val('');
        $wrap.find('.error_message').hide();
    });
    
    // 폼 제출
    $('#documentUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        const documentId = $('#document_id').val();
        const fileInput = $('#document_file')[0];
        
        if (!fileInput.files || !fileInput.files[0]) {
            alert('파일을 선택해주세요.');
            return;
        }
        
        const formData = new FormData();
        if (documentId) {
            formData.append('document_id', documentId);
        }
        formData.append('file', fileInput.files[0]);
        
        // CSRF 토큰 추가
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        // 제출 버튼 비활성화
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('제출 중...');
        
        $.ajax({
            url: '{{ route("member-documents.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message || '문서가 성공적으로 제출되었습니다.');
                    layerHide('pop_reason');
                    location.reload();
                } else {
                    alert(response.message || '문서 제출에 실패했습니다.');
                    $submitBtn.prop('disabled', false).text('Submit');
                }
            },
            error: function(xhr) {
                let errorMessage = '문서 제출 중 오류가 발생했습니다.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                $submitBtn.prop('disabled', false).text('Submit');
            }
        });
    });
    
    // 전역 함수: 문서 업로드 모달 표시
    window.showDocumentUpload = function(documentId, documentName) {
        currentDocumentId = documentId || 0;
        currentDocumentName = documentName || 'Self-introduction';
        
        $('#document_id').val(documentId || '');
        $('#document_name').text(documentName || 'Self-introduction');
        $('#document_file').val('');
        $('#pop_reason .file_list').empty();
        $('#pop_reason .file_wrap').removeClass('on');
        $('#pop_reason .error_message').hide();
        
        layerShow('pop_reason');
    };
});
</script>
