// 메일발송 폼 JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Summernote 에디터 초기화
    if (typeof $ !== 'undefined' && $.fn.summernote) {
        $('.summernote-editor').summernote({
            height: 300,
            lang: 'ko-KR'
        });
    }
    
    // 발송대상 선택에 따른 UI 전환
    const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
    
    // 초기 상태 설정
    function updateRecipientSections() {
        const selectedValue = document.querySelector('input[name="recipient_type"]:checked')?.value || 'project_term';
        
        document.querySelectorAll('.recipient-section').forEach(section => {
            section.style.display = 'none';
        });
        
        if (selectedValue === 'project_term') {
            const section = document.getElementById('project_term_section');
            if (section) section.style.display = 'block';
        } else if (selectedValue === 'address_book') {
            const section = document.getElementById('address_book_section');
            if (section) section.style.display = 'block';
        } else if (selectedValue === 'test') {
            const section = document.getElementById('test_section');
            if (section) section.style.display = 'block';
        }
    }
    
    // 초기 상태 적용
    updateRecipientSections();
    
    // 라디오 버튼 변경 이벤트
    recipientTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateRecipientSections();
        });
    });
    
    // 발송여부 선택에 따른 예약일시 표시
    const dispatchStatusRadios = document.querySelectorAll('input[name="dispatch_status"]');
    dispatchStatusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const scheduledAtSection = document.getElementById('scheduled_at_section');
            if (this.value === 'scheduled') {
                scheduledAtSection.style.display = 'block';
            } else {
                scheduledAtSection.style.display = 'none';
            }
        });
    });
    
    // 기수별 필터 추가
    const addFilterBtn = document.getElementById('add_filter_btn');
    if (addFilterBtn) {
        addFilterBtn.addEventListener('click', function() {
            const container = document.getElementById('recipient_filters_container');
            const rowCount = container.querySelectorAll('.recipient-filter-row').length;
            
            const newRow = document.createElement('div');
            newRow.className = 'recipient-filter-row';
            newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
            newRow.innerHTML = `
                <select name="recipient_filters[${rowCount}][project_term_id]" class="board-form-control">
                    <option value="">전체</option>
                </select>
                <select name="recipient_filters[${rowCount}][course_id]" class="board-form-control">
                    <option value="">전체</option>
                </select>
                <select name="recipient_filters[${rowCount}][operating_institution_id]" class="board-form-control">
                    <option value="">전체</option>
                </select>
                <select name="recipient_filters[${rowCount}][project_period_id]" class="board-form-control">
                    <option value="">전체</option>
                </select>
                <select name="recipient_filters[${rowCount}][country_id]" class="board-form-control">
                    <option value="">전체</option>
                </select>
                <button type="button" class="btn btn-danger btn-sm remove-filter-btn">삭제</button>
            `;
            
            container.appendChild(newRow);
            
            // 삭제 버튼 이벤트 추가
            newRow.querySelector('.remove-filter-btn').addEventListener('click', function() {
                if (container.querySelectorAll('.recipient-filter-row').length > 1) {
                    newRow.remove();
                } else {
                    alert('최소 1개 이상의 조건이 필요합니다.');
                }
            });
        });
    }
    
    // 기수별 필터 삭제
    document.querySelectorAll('.remove-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = document.getElementById('recipient_filters_container');
            if (container.querySelectorAll('.recipient-filter-row').length > 1) {
                this.closest('.recipient-filter-row').remove();
            } else {
                alert('최소 1개 이상의 조건이 필요합니다.');
            }
        });
    });
    
    // 파일 업로드 미리보기 (기존 alert-form.js와 유사한 로직)
    const fileInput = document.getElementById('files');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function() {
            filePreview.innerHTML = '';
            Array.from(this.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'board-file-item';
                fileItem.innerHTML = `
                    <span>${file.name}</span>
                    <span class="file-size">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                `;
                filePreview.appendChild(fileItem);
            });
        });
    }
    
    // 기존 파일 삭제
    document.querySelectorAll('.delete-file-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileId = this.dataset.fileId;
            const fileItem = this.closest('.board-file-item');
            const deletedInput = fileItem.querySelector('.deleted-file-input');
            
            if (confirm('파일을 삭제하시겠습니까?')) {
                deletedInput.value = fileId;
                fileItem.style.display = 'none';
            }
        });
    });
});
