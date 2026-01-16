/**
 * 회원 리스트 페이지 JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initSelectAll();
    initEmailSend();
    initDateInputs();
});

/**
 * 전체 선택 초기화
 */
function initSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const memberCheckboxes = document.querySelectorAll('.member-checkbox');

    if (!selectAllCheckbox) return;

    // 전체 선택/해제
    selectAllCheckbox.addEventListener('change', function() {
        memberCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // 개별 체크박스 변경 시
    memberCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = Array.from(memberCheckboxes).filter(cb => cb.checked).length;
            selectAllCheckbox.checked = checkedCount === memberCheckboxes.length;
        });
    });
}

/**
 * 메일 발송 초기화
 */
function initEmailSend() {
    const sendEmailBtn = document.getElementById('send-email-btn');
    const mailListSelect = document.getElementById('mail_list_id');

    if (!sendEmailBtn) return;

    sendEmailBtn.addEventListener('click', function() {
        const selectedMembers = Array.from(document.querySelectorAll('.member-checkbox'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedMembers.length === 0) {
            alert('메일을 발송할 회원을 선택해주세요.');
            return;
        }

        const mailListId = mailListSelect ? mailListSelect.value : '';
        if (!mailListId) {
            alert('메일리스트를 선택해주세요.');
            return;
        }

        if (confirm(`체크한 회원들에게 해당 메일이 발송됩니다. 정말 발송하시겠습니까?`)) {
            sendEmailToMembers(selectedMembers, mailListId);
        }
    });
}

/**
 * 회원에게 메일 발송
 */
function sendEmailToMembers(memberIds, mailListId) {
    const formData = new FormData();
    memberIds.forEach(id => formData.append('member_ids[]', id));
    formData.append('mail_list_id', mailListId);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch('/backoffice/members/send-email', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('메일이 발송되었습니다.');
            location.reload();
        } else {
            alert('메일 발송 중 오류가 발생했습니다: ' + (data.message || '알 수 없는 오류'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('메일 발송 중 오류가 발생했습니다.');
    });
}

/**
 * 날짜 입력 필드 초기화 (인풋박스 어디를 클릭해도 달력 열기)
 */
function initDateInputs() {
    // 모든 date, month 타입 인풋 선택
    const dateInputs = document.querySelectorAll('input[type="date"], input[type="month"]');
    
    dateInputs.forEach(input => {
        // 클릭 이벤트 추가
        input.addEventListener('click', function(e) {
            // 이미 포커스가 있는 경우에도 달력이 열리도록
            if (this.showPicker) {
                // showPicker() 메서드가 지원되는 경우 (최신 브라우저)
                try {
                    this.showPicker();
                } catch (error) {
                    // showPicker()가 실패하면 focus()로 대체
                    this.focus();
                }
            } else {
                // showPicker()가 지원되지 않는 경우 focus() 사용
                this.focus();
            }
        });
        
        // 포커스 이벤트도 추가 (탭 키로 이동했을 때도 달력이 열리도록)
        input.addEventListener('focus', function() {
            if (this.showPicker) {
                try {
                    this.showPicker();
                } catch (error) {
                    // 에러 무시
                }
            }
        });
    });
}
