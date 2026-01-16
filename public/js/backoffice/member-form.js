/**
 * 회원 생성/수정 폼 JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initProjectTermCascade();
    initAddressSearch();
    initDateInputs();
    initFormSubmit();
});

/**
 * 프로젝트 기수 연쇄 선택 초기화
 */
function initProjectTermCascade() {
    const projectTermSelect = document.getElementById('project_term_id');
    const courseSelect = document.getElementById('course_id');
    const operatingInstitutionSelect = document.getElementById('operating_institution_id');
    const projectPeriodSelect = document.getElementById('project_period_id');
    const countrySelect = document.getElementById('country_id');

    if (!projectTermSelect) return;

    // 이벤트 리스너 등록
    projectTermSelect.addEventListener('change', handleTermChange);
    if (courseSelect) {
        courseSelect.addEventListener('change', handleCourseChange);
    }
    if (operatingInstitutionSelect) {
        operatingInstitutionSelect.addEventListener('change', handleInstitutionChange);
    }
    if (projectPeriodSelect) {
        projectPeriodSelect.addEventListener('change', handlePeriodChange);
    }

    // 초기 로드 시 기존 선택값 복원 (edit 페이지용)
    initializeFilters();

    async function handleTermChange() {
        resetSelect(courseSelect);
        resetSelect(operatingInstitutionSelect);
        resetSelect(projectPeriodSelect);
        resetSelect(countrySelect);
        
        const termId = projectTermSelect.value;
        if (termId) {
            await loadCourses(termId, null);
        }
    }

    async function handleCourseChange() {
        resetSelect(operatingInstitutionSelect);
        resetSelect(projectPeriodSelect);
        resetSelect(countrySelect);
        
        const courseId = courseSelect.value;
        if (courseId) {
            await loadOperatingInstitutions(courseId, null);
        }
    }

    async function handleInstitutionChange() {
        resetSelect(projectPeriodSelect);
        resetSelect(countrySelect);
        
        const institutionId = operatingInstitutionSelect.value;
        if (institutionId) {
            await loadProjectPeriods(institutionId, null);
        }
    }

    async function handlePeriodChange() {
        resetSelect(countrySelect);
        
        const periodId = projectPeriodSelect.value;
        if (periodId) {
            await loadCountries(periodId, null);
        }
    }

    // 초기 로드 시 기존 선택값 복원
    async function initializeFilters() {
        const termId = projectTermSelect.value;
        const courseId = courseSelect?.querySelector('option[selected]')?.value || courseSelect?.value;
        const institutionId = operatingInstitutionSelect?.querySelector('option[selected]')?.value || operatingInstitutionSelect?.value;
        const periodId = projectPeriodSelect?.querySelector('option[selected]')?.value || projectPeriodSelect?.value;
        const countryId = countrySelect?.querySelector('option[selected]')?.value || countrySelect?.value;
        
        if (termId) {
            await loadCourses(termId, courseId);
            if (courseId && courseSelect && courseSelect.value) {
                await loadOperatingInstitutions(courseId, institutionId);
                if (institutionId && operatingInstitutionSelect && operatingInstitutionSelect.value) {
                    await loadProjectPeriods(institutionId, periodId);
                    if (periodId && projectPeriodSelect && projectPeriodSelect.value) {
                        await loadCountries(periodId, countryId);
                    }
                }
            }
        }
    }
}

/**
 * 과정 목록 로드
 */
async function loadCourses(projectTermId, selectedId = null) {
    const courseSelect = document.getElementById('course_id');
    if (!courseSelect) return;
    
    if (!projectTermId) {
        resetSelect(courseSelect);
        return;
    }

    try {
        const response = await fetch(`/backoffice/courses/get-by-term/${projectTermId}`);
        if (!response.ok) {
            throw new Error('과정 로드 실패');
        }
        const courses = await response.json();
        const valueToSelect = selectedId || courseSelect.value;
        populateSelect(courseSelect, courses, 'name_ko', 'name_en', valueToSelect);
        courseSelect.disabled = false;
    } catch (error) {
        console.error('과정 로드 오류:', error);
        resetSelect(courseSelect);
    }
}

/**
 * 운영기관 목록 로드
 */
async function loadOperatingInstitutions(courseId, selectedId = null) {
    const institutionSelect = document.getElementById('operating_institution_id');
    if (!institutionSelect) return;
    
    if (!courseId) {
        resetSelect(institutionSelect);
        return;
    }

    try {
        const response = await fetch(`/backoffice/operating-institutions/get-by-course/${courseId}`);
        if (!response.ok) {
            throw new Error('운영기관 로드 실패');
        }
        const institutions = await response.json();
        const valueToSelect = selectedId || institutionSelect.value;
        populateSelect(institutionSelect, institutions, 'name_ko', 'name_en', valueToSelect);
        institutionSelect.disabled = false;
    } catch (error) {
        console.error('운영기관 로드 오류:', error);
        resetSelect(institutionSelect);
    }
}

/**
 * 프로젝트기간 목록 로드
 */
async function loadProjectPeriods(institutionId, selectedId = null) {
    const periodSelect = document.getElementById('project_period_id');
    if (!periodSelect) return;
    
    if (!institutionId) {
        resetSelect(periodSelect);
        return;
    }

    try {
        const response = await fetch(`/backoffice/project-periods/get-by-institution/${institutionId}`);
        if (!response.ok) {
            throw new Error('프로젝트기간 로드 실패');
        }
        const periods = await response.json();
        const valueToSelect = selectedId || periodSelect.value;
        populateSelect(periodSelect, periods, 'name_ko', 'name_en', valueToSelect);
        periodSelect.disabled = false;
    } catch (error) {
        console.error('프로젝트기간 로드 오류:', error);
        resetSelect(periodSelect);
    }
}

/**
 * 국가 목록 로드
 */
async function loadCountries(periodId, selectedId = null) {
    const countrySelect = document.getElementById('country_id');
    if (!countrySelect) return;
    
    if (!periodId) {
        resetSelect(countrySelect);
        return;
    }

    try {
        const response = await fetch(`/backoffice/countries/get-by-period/${periodId}`);
        if (!response.ok) {
            throw new Error('국가 로드 실패');
        }
        const countries = await response.json();
        const valueToSelect = selectedId || countrySelect.value;
        populateSelect(countrySelect, countries, 'name_ko', 'name_en', valueToSelect);
        countrySelect.disabled = false;
    } catch (error) {
        console.error('국가 로드 오류:', error);
        resetSelect(countrySelect);
    }
}

/**
 * Select 옵션 채우기
 */
function populateSelect(select, items, nameKoField, nameEnField, selectedValue = null) {
    if (!select || !items) return;
    
    const valueToSelect = selectedValue !== null ? selectedValue : select.value;
    select.innerHTML = '<option value="">전체</option>';
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        const displayName = item[nameKoField] + (item[nameEnField] ? ' / ' + item[nameEnField] : '');
        option.textContent = displayName;
        if (item.id == valueToSelect) {
            option.selected = true;
        }
        select.appendChild(option);
    });
    // 선택된 값이 있으면 select에도 설정
    if (valueToSelect) {
        select.value = valueToSelect;
    }
}

/**
 * Select 초기화
 */
function resetSelect(select) {
    if (!select) return;
    select.innerHTML = '<option value="">전체</option>';
    select.value = '';
    select.disabled = true;
}

/**
 * 폼 제출 전 disabled 필드 활성화
 */
function initFormSubmit() {
    const form = document.getElementById('memberForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        // disabled된 select 필드들을 활성화하여 값이 전달되도록 함
        const disabledSelects = form.querySelectorAll('select[disabled]');
        disabledSelects.forEach(select => {
            select.disabled = false;
        });
        
        // 빈 select 필드도 명시적으로 처리
        const allSelects = form.querySelectorAll('select[name]');
        allSelects.forEach(select => {
            if (!select.value && select.hasAttribute('data-original-value')) {
                // 원래 값이 있었던 경우 복원
                select.value = select.getAttribute('data-original-value');
            }
        });
    });
    
    // 페이지 로드 시 disabled select의 원래 값 저장
    const disabledSelects = form.querySelectorAll('select[disabled]');
    disabledSelects.forEach(select => {
        if (select.value) {
            select.setAttribute('data-original-value', select.value);
        }
    });
}

/**
 * 주소 찾기 초기화
 */
function initAddressSearch() {
    const addressSearchBtn = document.getElementById('hotelAddressSearchBtn');
    const hotelAddressInput = document.getElementById('hotel_address');
    const hotelAddressDetailInput = document.getElementById('hotel_address_detail');
    
    if (!addressSearchBtn || !hotelAddressInput) return;
    
    addressSearchBtn.addEventListener('click', function() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 주소 타입에 따라 주소 조합
                let addr = ''; // 주소 변수
                let extraAddr = ''; // 참고항목 변수

                // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                }

                // 주소 정보를 해당 필드에 넣는다.
                hotelAddressInput.value = addr + extraAddr;
                // 커서를 상세주소 필드로 이동한다.
                if (hotelAddressDetailInput) {
                    hotelAddressDetailInput.focus();
                }
            }
        }).open();
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
