// 주소록 관리 폼 JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // addressBookId는 edit 페이지에서만 정의됨 (window 객체에서 확인)
    const addressBookId = typeof window.addressBookId !== 'undefined' ? window.addressBookId : null;
    const contactsTableBody = document.getElementById('contacts_table_body');
    const addContactRowBtn = document.getElementById('add_contact_row_btn');
    let contactRowIndex = 0;
    
    // create 페이지인지 edit 페이지인지 확인
    const isCreatePage = !addressBookId;
    
    // create 페이지: 신규 연락처 행 추가 기능
    if (isCreatePage) {
        // 초기 신규 행 하나 추가
        if (contactsTableBody) {
            addNewContactRow();
        }
        
        // 추가 버튼 클릭 이벤트
        if (addContactRowBtn) {
            addContactRowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (contactsTableBody) {
                    addNewContactRow();
                }
            });
        }
        
        // 폼 제출 시 데이터 수집
        const addressBookForm = document.getElementById('address_book_form');
        if (addressBookForm) {
            addressBookForm.addEventListener('submit', function(e) {
                collectContactData();
            });
        }
    }
    
    // 신규 연락처 행 추가 함수
    function addNewContactRow() {
        const row = document.createElement('tr');
        row.className = 'new-contact-row';
        row.dataset.index = contactRowIndex;
        
        row.innerHTML = `
            <td>신규</td>
            <td><input type="text" class="form-control contact-name-input" name="contacts[${contactRowIndex}][name]" placeholder="이름"></td>
            <td><input type="email" class="form-control contact-email-input" name="contacts[${contactRowIndex}][email]" placeholder="이메일"></td>
            <td><input type="text" class="form-control contact-phone-input" name="contacts[${contactRowIndex}][phone]" placeholder="연락처"></td>
            <td></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-contact-row-btn">삭제</button>
            </td>
        `;
        
        contactsTableBody.appendChild(row);
        
        // 삭제 버튼 이벤트
        const removeBtn = row.querySelector('.remove-contact-row-btn');
        removeBtn.addEventListener('click', function() {
            row.remove();
        });
        
        contactRowIndex++;
    }
    
    // 폼 제출 시 연락처 데이터 수집 (빈 행 제외)
    function collectContactData() {
        const newContactRows = document.querySelectorAll('.new-contact-row');
        newContactRows.forEach((row, index) => {
            const nameInput = row.querySelector('.contact-name-input');
            const emailInput = row.querySelector('.contact-email-input');
            
            // 이름과 이메일이 모두 비어있으면 name 속성 제거하여 서버에 전송되지 않도록
            if ((!nameInput.value || nameInput.value.trim() === '') && 
                (!emailInput.value || emailInput.value.trim() === '')) {
                nameInput.removeAttribute('name');
                emailInput.removeAttribute('name');
                row.querySelector('.contact-phone-input').removeAttribute('name');
            }
        });
    }
    
    // edit 페이지: 신규 연락처 행 추가 기능 및 기존 연락처 관리
    if (!isCreatePage) {
        // 신규 연락처 행 추가 기능 (create 페이지와 동일)
        if (addContactRowBtn && contactsTableBody) {
            // 추가 버튼 클릭 이벤트
            addContactRowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (contactsTableBody) {
                    addNewContactRow();
                }
            });
            
            // 폼 제출 시 데이터 수집
            const addressBookForm = document.getElementById('address_book_form');
            if (addressBookForm) {
                addressBookForm.addEventListener('submit', function(e) {
                    collectContactData();
                });
            }
        }
        
        // 기존 연락처 수정
        document.querySelectorAll('.update-contact-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const contactId = this.dataset.contactId;
                const row = this.closest('tr');
                const name = row.querySelector('.contact-name').value;
                const email = row.querySelector('.contact-email').value;
                const phone = row.querySelector('.contact-phone').value;
                
                fetch(`/backoffice/mail-address-books/${addressBookId}/update-contact/${contactId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name, email, phone })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('연락처가 수정되었습니다.');
                        location.reload();
                    } else {
                        alert('연락처 수정에 실패했습니다.');
                    }
                });
            });
        });
        
        // 연락처 삭제
        document.querySelectorAll('.delete-contact-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!confirm('연락처를 삭제하시겠습니까?')) return;
                
                const contactId = this.dataset.contactId;
                
                fetch(`/backoffice/mail-address-books/${addressBookId}/delete-contact/${contactId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('연락처 삭제에 실패했습니다.');
                    }
                });
            });
        });
        
        // 엑셀 일괄 등록
        const importExcelBtn = document.getElementById('import_excel_btn');
        if (importExcelBtn) {
            importExcelBtn.addEventListener('click', function() {
                const excelFileInput = document.getElementById('excel_file');
                if (!excelFileInput || !excelFileInput.files.length) {
                    alert('파일을 선택해주세요.');
                    return;
                }
                
                const formData = new FormData();
                formData.append('excel_file', excelFileInput.files[0]);
                
                fetch(`/backoffice/mail-address-books/${addressBookId}/import-excel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        }
        
        // edit 페이지 엑셀 파일 미리보기
        const editExcelFileInput = document.getElementById('excel_file');
        const editExcelFilePreview = document.getElementById('excel_file_preview');
        if (editExcelFileInput && editExcelFilePreview) {
            editExcelFileInput.addEventListener('change', function() {
                const files = this.files;
                editExcelFilePreview.innerHTML = '';
                
                if (files.length > 0) {
                    Array.from(files).forEach((file) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'board-file-item';
                        fileItem.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin-bottom: 8px;';
                        
                        const fileInfo = document.createElement('div');
                        fileInfo.style.cssText = 'display: flex; align-items: center; gap: 8px; flex: 1;';
                        
                        const fileIcon = document.createElement('i');
                        fileIcon.className = 'fas fa-file-excel';
                        fileIcon.style.cssText = 'color: #28a745; font-size: 18px;';
                        
                        const fileName = document.createElement('span');
                        fileName.textContent = file.name;
                        fileName.style.cssText = 'font-size: 14px; color: #333;';
                        
                        fileInfo.appendChild(fileIcon);
                        fileInfo.appendChild(fileName);
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-sm btn-danger';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.style.cssText = 'padding: 4px 8px; font-size: 12px;';
                        removeBtn.addEventListener('click', function() {
                            fileItem.remove();
                            const newInput = editExcelFileInput.cloneNode(true);
                            editExcelFileInput.parentNode.replaceChild(newInput, editExcelFileInput);
                            newInput.addEventListener('change', arguments.callee);
                        });
                        
                        fileItem.appendChild(fileInfo);
                        fileItem.appendChild(removeBtn);
                        editExcelFilePreview.appendChild(fileItem);
                    });
                }
            });
        }
    }
    
    // 엑셀 파일 선택 (create/edit 공통)
    const excelFileInput = document.getElementById('excel_file');
    const excelFilePreview = document.getElementById('excel_file_preview');
    
    if (excelFileInput && excelFilePreview) {
        excelFileInput.addEventListener('change', function() {
            const files = this.files;
            excelFilePreview.innerHTML = '';
            
            if (files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'board-file-item';
                    fileItem.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin-bottom: 8px;';
                    
                    const fileInfo = document.createElement('div');
                    fileInfo.style.cssText = 'display: flex; align-items: center; gap: 8px; flex: 1;';
                    
                    const fileIcon = document.createElement('i');
                    fileIcon.className = 'fas fa-file-excel';
                    fileIcon.style.cssText = 'color: #28a745; font-size: 18px;';
                    
                    const fileName = document.createElement('span');
                    fileName.textContent = file.name;
                    fileName.style.cssText = 'font-size: 14px; color: #333;';
                    
                    fileInfo.appendChild(fileIcon);
                    fileInfo.appendChild(fileName);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.style.cssText = 'padding: 4px 8px; font-size: 12px;';
                    removeBtn.addEventListener('click', function() {
                        fileItem.remove();
                        // input 초기화
                        const newInput = excelFileInput.cloneNode(true);
                        excelFileInput.parentNode.replaceChild(newInput, excelFileInput);
                        newInput.addEventListener('change', arguments.callee);
                    });
                    
                    fileItem.appendChild(fileInfo);
                    fileItem.appendChild(removeBtn);
                    excelFilePreview.appendChild(fileItem);
                });
            }
        });
        
        // 드래그 앤 드롭 지원
        const fileInputWrapper = excelFileInput.closest('.board-file-input-wrapper');
        if (fileInputWrapper) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileInputWrapper.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                fileInputWrapper.addEventListener(eventName, function() {
                    fileInputWrapper.style.borderColor = '#007bff';
                    fileInputWrapper.style.backgroundColor = '#e7f3ff';
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                fileInputWrapper.addEventListener(eventName, function() {
                    fileInputWrapper.style.borderColor = '';
                    fileInputWrapper.style.backgroundColor = '';
                }, false);
            });
            
            fileInputWrapper.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                excelFileInput.files = files;
                excelFileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }, false);
        }
    }
});
