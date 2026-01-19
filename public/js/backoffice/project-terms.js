// 프로젝트 기수 관리 JavaScript

// 전역 상태 관리
const state = {
    selectedCourseId: null,
    selectedInstitutionId: null,
    selectedPeriodId: null,
    selectedCountryId: null,
    selectedScheduleId: null,
    currentCategoryType: null,
    currentCategoryId: null
};

document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    loadReferenceMaterials();
});

// 이벤트 리스너 초기화
function initializeEventListeners() {
    // 과정 등록 폼 처리
    const courseForm = document.getElementById('courseForm');
    if (courseForm) {
        courseForm.addEventListener('submit', handleCourseSubmit);
    }

    // 운영기관 등록 폼 처리
    const operatingInstitutionForm = document.getElementById('operatingInstitutionForm');
    if (operatingInstitutionForm) {
        operatingInstitutionForm.addEventListener('submit', handleOperatingInstitutionSubmit);
    }

    // 프로젝트기간 등록 폼 처리
    const projectPeriodForm = document.getElementById('projectPeriodForm');
    if (projectPeriodForm) {
        projectPeriodForm.addEventListener('submit', handleProjectPeriodSubmit);
    }

    // 국가 등록 폼 처리
    const countryForm = document.getElementById('countryForm');
    if (countryForm) {
        countryForm.addEventListener('submit', handleCountrySubmit);
    }

    // 일정 등록 폼 처리
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', handleScheduleSubmit);
    }

    // 과정 클릭 이벤트 (동적으로 추가된 요소 포함)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.course-link')) {
            e.preventDefault();
            const courseId = parseInt(e.target.closest('.course-link').dataset.courseId);
            handleCourseClick(courseId);
        }
        
        if (e.target.closest('.institution-link')) {
            e.preventDefault();
            e.stopPropagation();
            const institutionId = parseInt(e.target.closest('.institution-link').dataset.institutionId);
            handleInstitutionClick(institutionId);
        }
        
        if (e.target.closest('.period-link')) {
            e.preventDefault();
            const periodId = parseInt(e.target.closest('.period-link').dataset.periodId);
            handlePeriodClick(periodId);
        }
        
        if (e.target.closest('.country-link')) {
            e.preventDefault();
            const countryId = parseInt(e.target.closest('.country-link').dataset.countryId);
            handleCountryClick(countryId);
        }
        
        if (e.target.closest('.schedule-link')) {
            e.preventDefault();
            e.stopPropagation();
            const scheduleId = parseInt(e.target.closest('.schedule-link').dataset.scheduleId);
            handleScheduleClick(scheduleId);
        }
    });

    // 카테고리 수정 폼 제출
    document.addEventListener('submit', function(e) {
        if (e.target.matches('#categoryEditFormCourse, #categoryEditFormInstitution, #categoryEditFormPeriod, #categoryEditFormCountry, #categoryEditFormSchedule')) {
            e.preventDefault();
            handleCategoryEditSubmit(e);
        }
    });

    // 카테고리 삭제 버튼
    document.addEventListener('click', function(e) {
        if (e.target.closest('#deleteCategoryBtn')) {
            e.preventDefault();
            handleCategoryDelete();
        }
    });

    // 순서 변경 버튼 (현재 카테고리 섹션의 버튼만 사용)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.order-controls .order-up-btn')) {
            e.preventDefault();
            handleOrderChange('up');
        }
        if (e.target.closest('.order-controls .order-down-btn')) {
            e.preventDefault();
            handleOrderChange('down');
        }
    });
}

// 과정 등록 폼 제출 처리
function handleCourseSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            location.reload(); // 페이지 새로고침으로 테이블 업데이트
        } else {
            alert(data.message || '과정 등록 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('과정 등록 중 오류가 발생했습니다.');
    });
}

// 운영기관 등록 폼 제출 처리
function handleOperatingInstitutionSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            // 운영기관 목록 다시 로드
            if (state.selectedCourseId) {
                loadOperatingInstitutions(state.selectedCourseId);
            }
        } else {
            alert(data.message || '운영기관 등록 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('운영기관 등록 중 오류가 발생했습니다.');
    });
}

// 프로젝트기간 등록 폼 제출 처리
function handleProjectPeriodSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            loadProjectPeriods(state.selectedInstitutionId);
        } else {
            alert(data.message || '프로젝트기간 등록 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('프로젝트기간 등록 중 오류가 발생했습니다.');
    });
}

// 국가 등록 폼 제출 처리
function handleCountrySubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            loadCountries(state.selectedPeriodId);
        } else {
            alert(data.message || '국가 등록 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('국가 등록 중 오류가 발생했습니다.');
    });
}

// 일정 등록 폼 제출 처리
function handleScheduleSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
            loadSchedules(state.selectedCountryId);
        } else {
            alert(data.message || '일정 등록 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('일정 등록 중 오류가 발생했습니다.');
    });
}

// 과정 클릭 처리
function handleCourseClick(courseId) {
    // 선택 상태 업데이트
    state.selectedCourseId = courseId;
    state.selectedInstitutionId = null;
    state.selectedPeriodId = null;
    state.selectedCountryId = null;
    state.selectedScheduleId = null;
    
    // 모든 등록 폼 숨기기
    hideAllRegisterSections();
    disableScheduleRegisterForm();
    
    // 운영기관 등록 폼 표시
    showRegisterSection('operatingInstitutionRegisterSection');
    document.getElementById('operating_institution_course_id').value = courseId;
    
    // 다른 과정의 운영기관 모두 제거
    document.querySelectorAll('tr[data-course-id]').forEach(row => {
        if (row.getAttribute('data-course-id') != courseId) {
            const cell = row.querySelector('.operating-institution-cell');
            if (cell) {
                cell.innerHTML = '';
            }
            // 하위 항목도 모두 제거
            const periodCell = row.querySelector('.project-period-cell');
            if (periodCell) periodCell.innerHTML = '';
            const countryCell = row.querySelector('.country-cell');
            if (countryCell) countryCell.innerHTML = '';
            const scheduleCell = row.querySelector('.schedule-cell');
            if (scheduleCell) scheduleCell.innerHTML = '';
        }
    });
    
    // 운영기관 목록 로드
    loadOperatingInstitutions(courseId);
    
    // 과정 선택 하이라이트
    highlightSelectedItem('.course-link', courseId, 'course');
    
    // 과정 수정 폼 로드
    loadCourseForEdit(courseId);
}

// 운영기관 클릭 처리
function handleInstitutionClick(institutionId) {
    state.selectedInstitutionId = institutionId;
    state.selectedPeriodId = null;
    state.selectedCountryId = null;
    state.selectedScheduleId = null;
    
    hideAllRegisterSections();
    disableScheduleRegisterForm();
    showRegisterSection('projectPeriodRegisterSection');
    document.getElementById('project_period_institution_id').value = institutionId;
    
    // 선택한 운영기관의 정보 가져오기
    const institutionItem = document.querySelector(`.institution-item[data-institution-id="${institutionId}"]`);
    if (institutionItem) {
        const row = institutionItem.closest('tr');
        if (row) {
            // 같은 과정 내 다른 운영기관의 프로젝트기간, 국가, 일정 모두 제거
            const courseId = institutionItem.getAttribute('data-course-id');
            const allInstitutionItems = row.querySelectorAll(`.institution-item[data-course-id="${courseId}"]`);
            allInstitutionItems.forEach(item => {
                if (item.getAttribute('data-institution-id') != institutionId) {
                    const itemRow = item.closest('tr');
                    if (itemRow) {
                        const periodCell = itemRow.querySelector('.project-period-cell');
                        if (periodCell) periodCell.innerHTML = '';
                        const countryCell = itemRow.querySelector('.country-cell');
                        if (countryCell) countryCell.innerHTML = '';
                        const scheduleCell = itemRow.querySelector('.schedule-cell');
                        if (scheduleCell) scheduleCell.innerHTML = '';
                    }
                }
            });
            
            // 선택한 운영기관의 하위 항목 초기화
            const periodCell = row.querySelector('.project-period-cell');
            if (periodCell) periodCell.innerHTML = '';
            const countryCell = row.querySelector('.country-cell');
            if (countryCell) countryCell.innerHTML = '';
            const scheduleCell = row.querySelector('.schedule-cell');
            if (scheduleCell) scheduleCell.innerHTML = '';
        }
    }
    
    loadProjectPeriods(institutionId);
    highlightSelectedItem('.institution-link', institutionId, 'institution');
    
    // 운영기관 수정 폼 로드
    loadInstitutionForEdit(institutionId);
}

// 프로젝트기간 클릭 처리
function handlePeriodClick(periodId) {
    state.selectedPeriodId = periodId;
    state.selectedCountryId = null;
    state.selectedScheduleId = null;
    
    hideAllRegisterSections();
    disableScheduleRegisterForm();
    showRegisterSection('countryRegisterSection');
    document.getElementById('country_period_id').value = periodId;
    
    // 선택한 프로젝트기간의 정보 가져오기
    const periodItem = document.querySelector(`.period-item[data-period-id="${periodId}"]`);
    if (periodItem) {
        const row = periodItem.closest('tr');
        if (row) {
            const institutionId = periodItem.getAttribute('data-institution-id');
            const countryCell = row.querySelector('.country-cell');
            const scheduleCell = row.querySelector('.schedule-cell');
            
            // 같은 운영기관 내 다른 프로젝트기간의 국가, 일정 모두 제거
            const allPeriodItems = row.querySelectorAll(`.period-item[data-institution-id="${institutionId}"]`);
            allPeriodItems.forEach(item => {
                if (item.getAttribute('data-period-id') != periodId) {
                    if (countryCell) {
                        const existingCountries = countryCell.querySelectorAll(`.country-item[data-period-id="${item.getAttribute('data-period-id')}"]`);
                        existingCountries.forEach(countryItem => {
                            const countryId = countryItem.getAttribute('data-country-id');
                            countryItem.remove();
                            
                            // 해당 국가의 일정도 제거
                            if (scheduleCell) {
                                const existingSchedules = scheduleCell.querySelectorAll(`.schedule-item[data-country-id="${countryId}"]`);
                                existingSchedules.forEach(scheduleItem => scheduleItem.remove());
                            }
                        });
                    }
                }
            });
            
            // 선택한 프로젝트기간의 하위 항목 초기화 (이미 위에서 countryCell, scheduleCell 정의됨)
            if (countryCell) {
                // 현재 프로젝트기간의 기존 국가 제거
                const existingCountries = countryCell.querySelectorAll(`.country-item[data-period-id="${periodId}"]`);
                existingCountries.forEach(item => item.remove());
            }
            if (scheduleCell) {
                // 현재 프로젝트기간의 기존 국가들에 해당하는 일정은 위에서 이미 제거됨
            }
        }
    }
    
    loadCountries(periodId);
    highlightSelectedItem('.period-link', periodId, 'period');
    
    // 프로젝트기간 수정 폼 로드
    loadPeriodForEdit(periodId);
}

// 국가 클릭 처리
function handleCountryClick(countryId) {
    state.selectedCountryId = countryId;
    state.selectedScheduleId = null;
    
    hideAllRegisterSections();
    showRegisterSection('scheduleRegisterSection');
    enableScheduleRegisterForm();
    document.getElementById('schedule_country_id').value = countryId;
    
    // 선택한 국가의 정보 가져오기
    const countryItem = document.querySelector(`.country-item[data-country-id="${countryId}"]`);
    if (countryItem) {
        const row = countryItem.closest('tr');
        if (row) {
            const periodId = countryItem.getAttribute('data-period-id');
            // 같은 프로젝트기간 내 다른 국가의 일정 모두 제거
            const allCountryItems = row.querySelectorAll(`.country-item[data-period-id="${periodId}"]`);
            allCountryItems.forEach(item => {
                if (item.getAttribute('data-country-id') != countryId) {
                    const scheduleCell = row.querySelector('.schedule-cell');
                    if (scheduleCell) {
                        const existingSchedules = scheduleCell.querySelectorAll(`.schedule-item[data-country-id="${item.getAttribute('data-country-id')}"]`);
                        existingSchedules.forEach(scheduleItem => scheduleItem.remove());
                    }
                }
            });
            
            // 선택한 국가의 하위 항목 초기화
            const scheduleCell = row.querySelector('.schedule-cell');
            if (scheduleCell) {
                const existingSchedules = scheduleCell.querySelectorAll(`.schedule-item[data-country-id="${countryId}"]`);
                existingSchedules.forEach(item => item.remove());
            }
        }
    }
    
    loadSchedules(countryId);
    highlightSelectedItem('.country-link', countryId, 'country');
    
    // 국가 수정 폼 로드
    loadCountryForEdit(countryId);
}

// 일정 클릭 처리
function handleScheduleClick(scheduleId) {
    state.selectedScheduleId = scheduleId;
    
    // 일정 등록 섹션은 숨기지 않고 비활성화만 함
    hideAllRegisterSections();
    const scheduleSection = document.getElementById('scheduleRegisterSection');
    if (scheduleSection) {
        scheduleSection.style.display = 'block';
        disableScheduleRegisterForm();
    }
    loadScheduleForEdit(scheduleId);
    highlightSelectedItem('.schedule-link', scheduleId, 'schedule');
}

// 운영기관 목록 로드
function loadOperatingInstitutions(courseId) {
    fetch(`/backoffice/operating-institutions/get-by-course/${courseId}`)
        .then(response => response.json())
        .then(data => {
            const row = document.querySelector(`tr[data-course-id="${courseId}"]`);
            if (row) {
                const cell = row.querySelector('.operating-institution-cell');
                if (cell) {
                    cell.innerHTML = '';
                    if (data && data.length > 0) {
                        data.forEach(institution => {
                            const div = document.createElement('div');
                            div.className = 'institution-item';
                            div.setAttribute('data-institution-id', institution.id);
                            div.setAttribute('data-course-id', courseId);
                            div.style.marginBottom = '0rem';
                            div.innerHTML = `
                                <a href="#" class="institution-link" data-institution-id="${institution.id}" style="color: #333; text-decoration: none;">
                                    ${institution.name_ko || ''}${institution.name_en ? ' / ' + institution.name_en : ''}
                                </a>
                            `;
                            cell.appendChild(div);
                        });
                    } else {
                        cell.innerHTML = '';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 프로젝트기간 목록 로드
function loadProjectPeriods(institutionId) {
    fetch(`/backoffice/project-periods/get-by-institution/${institutionId}`)
        .then(response => response.json())
        .then(data => {
            const institutionItem = document.querySelector(`.institution-item[data-institution-id="${institutionId}"]`);
            if (institutionItem) {
                const row = institutionItem.closest('tr');
                if (row) {
                    const cell = row.querySelector('.project-period-cell');
                    if (cell) {
                        // 같은 institution의 기존 period 제거
                        const existingPeriods = cell.querySelectorAll(`.period-item[data-institution-id="${institutionId}"]`);
                        existingPeriods.forEach(item => item.remove());
                        
                        // 새 항목 추가
                        if (data && data.length > 0) {
                            data.forEach(period => {
                                const div = document.createElement('div');
                                div.className = 'period-item';
                                div.setAttribute('data-period-id', period.id);
                                div.setAttribute('data-institution-id', institutionId);
                                div.style.marginBottom = '0.25rem';
                                div.innerHTML = `
                                    <a href="#" class="period-link" data-period-id="${period.id}" style="color: #333; text-decoration: none;">
                                        ${period.name_ko || ''}${period.name_en ? ' / ' + period.name_en : ''}
                                    </a>
                                `;
                                cell.appendChild(div);
                            });
                            
                            // 빈 메시지 제거
                            const emptyMsg = cell.querySelector('span[style*="color: #6c757d"]');
                            if (emptyMsg) {
                                emptyMsg.remove();
                            }
                        } else {
                            // 다른 institution의 period가 없으면 빈 메시지 표시
                            if (cell.querySelectorAll('.period-item').length === 0) {
                                cell.innerHTML = '';
                            }
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 국가 목록 로드
function loadCountries(periodId) {
    fetch(`/backoffice/countries/get-by-period/${periodId}`)
        .then(response => response.json())
        .then(data => {
            const periodItem = document.querySelector(`.period-item[data-period-id="${periodId}"]`);
            if (periodItem) {
                const row = periodItem.closest('tr');
                if (row) {
                    const cell = row.querySelector('.country-cell');
                    if (cell) {
                        // 같은 period의 기존 country 제거
                        const existingCountries = cell.querySelectorAll(`.country-item[data-period-id="${periodId}"]`);
                        existingCountries.forEach(item => item.remove());
                        
                        if (data && data.length > 0) {
                            data.forEach(country => {
                                const div = document.createElement('div');
                                div.className = 'country-item';
                                div.setAttribute('data-country-id', country.id);
                                div.setAttribute('data-period-id', periodId);
                                div.style.marginBottom = '0rem';
                                div.innerHTML = `
                                    <a href="#" class="country-link" data-country-id="${country.id}" style="color: #333; text-decoration: none;">
                                        ${country.name_ko || ''}${country.name_en ? ' / ' + country.name_en : ''}
                                    </a>
                                `;
                                cell.appendChild(div);
                            });
                            
                            const emptyMsg = cell.querySelector('span[style*="color: #6c757d"]');
                            if (emptyMsg) {
                                emptyMsg.remove();
                            }
                        } else {
                            if (cell.querySelectorAll('.country-item').length === 0) {
                                cell.innerHTML = '';
                            }
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 일정 목록 로드
function loadSchedules(countryId) {
    fetch(`/backoffice/schedules/get-by-country/${countryId}`)
        .then(response => response.json())
        .then(data => {
            const countryItem = document.querySelector(`.country-item[data-country-id="${countryId}"]`);
            if (countryItem) {
                const row = countryItem.closest('tr');
                if (row) {
                    const cell = row.querySelector('.schedule-cell');
                    if (cell) {
                        // 같은 country의 기존 schedule 제거
                        const existingSchedules = cell.querySelectorAll(`.schedule-item[data-country-id="${countryId}"]`);
                        existingSchedules.forEach(item => item.remove());
                        
                        if (data && data.length > 0) {
                            data.forEach(schedule => {
                                const div = document.createElement('div');
                                div.className = 'schedule-item';
                                div.setAttribute('data-schedule-id', schedule.id);
                                div.setAttribute('data-country-id', countryId);
                                div.style.marginBottom = '0.25rem';
                                div.innerHTML = `
                                    <a href="#" class="schedule-link" data-schedule-id="${schedule.id}" style="color: #333; text-decoration: none;">
                                        ${schedule.name_ko || ''}${schedule.name_en ? ' / ' + schedule.name_en : ''}
                                    </a>
                                `;
                                cell.appendChild(div);
                            });
                            
                            const emptyMsg = cell.querySelector('span[style*="color: #6c757d"]');
                            if (emptyMsg) {
                                emptyMsg.remove();
                            }
                        } else {
                            if (cell.querySelectorAll('.schedule-item').length === 0) {
                                cell.innerHTML = '';
                            }
                        }
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 등록 섹션 표시/숨김
function showRegisterSection(sectionId) {
    document.querySelectorAll('.register-section').forEach(section => {
        section.style.display = 'none';
    });
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
        
        // 운영기관 등록 섹션인 경우 담당자 정보 섹션 초기화
        if (sectionId === 'operatingInstitutionRegisterSection') {
        }
    }
}

function hideAllRegisterSections() {
    document.querySelectorAll('.register-section').forEach(section => {
        section.style.display = 'none';
    });
}

// 일정 등록 폼 비활성화
function disableScheduleRegisterForm() {
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        const inputs = scheduleForm.querySelectorAll('input, button[type="submit"]');
        inputs.forEach(input => {
            input.disabled = true;
        });
    }
}

// 일정 등록 폼 활성화
function enableScheduleRegisterForm() {
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        const inputs = scheduleForm.querySelectorAll('input, button[type="submit"]');
        inputs.forEach(input => {
            input.disabled = false;
        });
    }
}

// 현재 카테고리 수정 영역 표시/숨김
function showCurrentCategorySection() {
    const section = document.getElementById('currentCategorySection');
    if (section) {
        section.style.display = 'block';
    }
}

function hideCurrentCategorySection() {
    const section = document.getElementById('currentCategorySection');
    if (section) {
        section.style.display = 'none';
    }
    document.querySelectorAll('.edit-form').forEach(form => {
        form.style.display = 'none';
    });
}

// 선택된 항목 하이라이트
function highlightSelectedItem(selector, id, type) {
    // 모든 하이라이트 제거
    document.querySelectorAll('.selected-item').forEach(item => {
        item.classList.remove('selected-item');
    });
    
    // 선택된 항목 하이라이트 (행 전체가 아닌 항목만)
    const item = document.querySelector(`${selector}[data-${type}-id="${id}"]`);
    if (item) {
        if (type === 'course') {
            item.closest('.course-item')?.classList.add('selected-item');
        } else if (type === 'institution') {
            item.closest('.institution-item')?.classList.add('selected-item');
            // 상위: 과정 하이라이트
            const institutionItem = item.closest('.institution-item');
            const courseId = institutionItem?.dataset.courseId;
            if (courseId) {
                const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
                courseItem?.classList.add('selected-item');
            }
        } else if (type === 'period') {
            item.closest('.period-item')?.classList.add('selected-item');
            // 상위: 운영기관, 과정 하이라이트
            const periodItem = item.closest('.period-item');
            const institutionId = periodItem?.dataset.institutionId;
            if (institutionId) {
                const institutionItem = document.querySelector(`.institution-item[data-institution-id="${institutionId}"]`);
                institutionItem?.classList.add('selected-item');
                const courseId = institutionItem?.dataset.courseId;
                if (courseId) {
                    const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
                    courseItem?.classList.add('selected-item');
                }
            }
        } else if (type === 'country') {
            item.closest('.country-item')?.classList.add('selected-item');
            // 상위: 프로젝트기간, 운영기관, 과정 하이라이트
            const countryItem = item.closest('.country-item');
            const periodId = countryItem?.dataset.periodId;
            if (periodId) {
                const periodItem = document.querySelector(`.period-item[data-period-id="${periodId}"]`);
                periodItem?.classList.add('selected-item');
                const institutionId = periodItem?.dataset.institutionId;
                if (institutionId) {
                    const institutionItem = document.querySelector(`.institution-item[data-institution-id="${institutionId}"]`);
                    institutionItem?.classList.add('selected-item');
                    const courseId = institutionItem?.dataset.courseId;
                    if (courseId) {
                        const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
                        courseItem?.classList.add('selected-item');
                    }
                }
            }
        } else if (type === 'schedule') {
            item.closest('.schedule-item')?.classList.add('selected-item');
            // 상위: 국가, 프로젝트기간, 운영기관, 과정 하이라이트
            const scheduleItem = item.closest('.schedule-item');
            const countryId = scheduleItem?.dataset.countryId;
            if (countryId) {
                const countryItem = document.querySelector(`.country-item[data-country-id="${countryId}"]`);
                countryItem?.classList.add('selected-item');
                const periodId = countryItem?.dataset.periodId;
                if (periodId) {
                    const periodItem = document.querySelector(`.period-item[data-period-id="${periodId}"]`);
                    periodItem?.classList.add('selected-item');
                    const institutionId = periodItem?.dataset.institutionId;
                    if (institutionId) {
                        const institutionItem = document.querySelector(`.institution-item[data-institution-id="${institutionId}"]`);
                        institutionItem?.classList.add('selected-item');
                        const courseId = institutionItem?.dataset.courseId;
                        if (courseId) {
                            const courseItem = document.querySelector(`.course-item[data-course-id="${courseId}"]`);
                            courseItem?.classList.add('selected-item');
                        }
                    }
                }
            }
        }
    }
}

// 카테고리 수정 폼 제출 처리
function handleCategoryEditSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const categoryType = form.dataset.type;
    const categoryIdInput = form.querySelector('input[name="id"]');
    const categoryId = categoryIdInput ? categoryIdInput.value : null;
    
    if (!categoryId) {
        alert('카테고리 ID가 없습니다.');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('_method', 'PUT');
    
    let url = '';
    if (categoryType === 'course') {
        url = `/backoffice/courses/${categoryId}`;
    } else if (categoryType === 'operating_institution') {
        url = `/backoffice/operating-institutions/${categoryId}`;
    } else if (categoryType === 'project_period') {
        url = `/backoffice/project-periods/${categoryId}`;
    } else if (categoryType === 'country') {
        url = `/backoffice/countries/${categoryId}`;
    } else if (categoryType === 'schedule') {
        url = `/backoffice/schedules/${categoryId}`;
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || '수정 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('수정 중 오류가 발생했습니다.');
    });
}

// 카테고리 삭제 처리
function handleCategoryDelete() {
    const categoryType = state.currentCategoryType;
    const categoryId = state.currentCategoryId;
    
    if (!categoryType || !categoryId) {
        return;
    }
    
    let message = '정말 삭제하시겠습니까?';
    if (categoryType === 'operating_institution') {
        message = '운영기관이 삭제됩니다. 운영기관 삭제 시 하위메뉴가 전부 사라집니다. 정말 삭제하시겠습니까?';
    }
    
    if (!confirm(message)) {
        return;
    }
    
    let url = '';
    if (categoryType === 'course') {
        url = `/backoffice/courses/${categoryId}`;
    } else if (categoryType === 'operating_institution') {
        url = `/backoffice/operating-institutions/${categoryId}`;
    } else if (categoryType === 'project_period') {
        url = `/backoffice/project-periods/${categoryId}`;
    } else if (categoryType === 'country') {
        url = `/backoffice/countries/${categoryId}`;
    } else if (categoryType === 'schedule') {
        url = `/backoffice/schedules/${categoryId}`;
    }
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || '삭제 중 오류가 발생했습니다.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('삭제 중 오류가 발생했습니다.');
    });
}

// 각 항목의 순서 변경 처리 (항목 옆 버튼 클릭 시)
function handleItemOrderChange(type, id, direction) {
    let currentItem = null;
    let siblingItem = null;
    let container = null;
    
    if (type === 'course') {
        currentItem = document.querySelector(`.course-item[data-course-id="${id}"]`)?.closest('tr');
        container = currentItem?.parentElement;
        if (direction === 'up') {
            siblingItem = currentItem?.previousElementSibling;
        } else {
            siblingItem = currentItem?.nextElementSibling;
        }
    } else if (type === 'operating_institution') {
        currentItem = document.querySelector(`.institution-item[data-institution-id="${id}"]`);
        container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.institution-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (type === 'project_period') {
        currentItem = document.querySelector(`.period-item[data-period-id="${id}"]`);
        container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.period-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (type === 'country') {
        currentItem = document.querySelector(`.country-item[data-country-id="${id}"]`);
        container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.country-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (type === 'schedule') {
        currentItem = document.querySelector(`.schedule-item[data-schedule-id="${id}"]`);
        container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.schedule-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    }
    
    if (!currentItem || !siblingItem || !container) {
        return;
    }
    
    // DOM에서 위치 변경
    if (direction === 'up') {
        container.insertBefore(currentItem, siblingItem);
    } else {
        // 아래로 이동: currentItem을 siblingItem 다음에 삽입
        container.insertBefore(currentItem, siblingItem.nextSibling);
    }
    
    // 서버에 순서 업데이트
    updateItemOrderOnServer(type, container, type === 'course');
}

// 순서 변경 처리 (현재 카테고리 섹션의 버튼)
function handleOrderChange(direction) {
    const categoryType = state.currentCategoryType;
    const categoryId = state.currentCategoryId;
    
    if (!categoryType || !categoryId) {
        return;
    }
    
    // 현재 항목의 형제 항목 찾기
    let currentItem = null;
    let siblingItem = null;
    
    if (categoryType === 'course') {
        currentItem = document.querySelector(`.course-link[data-course-id="${categoryId}"]`)?.closest('tr');
        if (direction === 'up') {
            siblingItem = currentItem?.previousElementSibling;
        } else {
            siblingItem = currentItem?.nextElementSibling;
        }
    } else if (categoryType === 'operating_institution') {
        currentItem = document.querySelector(`.institution-link[data-institution-id="${categoryId}"]`)?.closest('.institution-item');
        const container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.institution-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (categoryType === 'project_period') {
        currentItem = document.querySelector(`.period-link[data-period-id="${categoryId}"]`)?.closest('.period-item');
        const container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.period-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (categoryType === 'country') {
        currentItem = document.querySelector(`.country-link[data-country-id="${categoryId}"]`)?.closest('.country-item');
        const container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.country-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    } else if (categoryType === 'schedule') {
        currentItem = document.querySelector(`.schedule-link[data-schedule-id="${categoryId}"]`)?.closest('.schedule-item');
        const container = currentItem?.parentElement;
        const items = Array.from(container?.querySelectorAll('.schedule-item') || []);
        const currentIndex = items.indexOf(currentItem);
        if (direction === 'up' && currentIndex > 0) {
            siblingItem = items[currentIndex - 1];
        } else if (direction === 'down' && currentIndex < items.length - 1) {
            siblingItem = items[currentIndex + 1];
        }
    }
    
    if (!currentItem || !siblingItem) {
        return;
    }
    
    // DOM에서 위치 변경
    const parent = currentItem.parentElement;
    if (direction === 'up') {
        parent.insertBefore(currentItem, siblingItem);
    } else {
        // 아래로 이동: currentItem을 siblingItem 다음에 삽입
        parent.insertBefore(currentItem, siblingItem.nextSibling);
    }
    
    // 서버에 순서 업데이트 요청
    updateOrderOnServer(categoryType, currentItem, siblingItem);
}

// 각 항목의 순서를 서버에 업데이트
function updateItemOrderOnServer(type, container, isCourse) {
    let url = '';
    let items = [];
    
    if (type === 'course') {
        url = '/backoffice/courses/update-order';
        items = Array.from(container.querySelectorAll('tr[data-course-id]'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.courseId),
            order: index + 1
        }));
    } else if (type === 'operating_institution') {
        url = '/backoffice/operating-institutions/update-order';
        items = Array.from(container.querySelectorAll('.institution-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.institutionId),
            order: index + 1
        }));
    } else if (type === 'project_period') {
        url = '/backoffice/project-periods/update-order';
        items = Array.from(container.querySelectorAll('.period-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.periodId),
            order: index + 1
        }));
    } else if (type === 'country') {
        url = '/backoffice/countries/update-order';
        items = Array.from(container.querySelectorAll('.country-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.countryId),
            order: index + 1
        }));
    } else if (type === 'schedule') {
        url = '/backoffice/schedules/update-order';
        items = Array.from(container.querySelectorAll('.schedule-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.scheduleId),
            order: index + 1
        }));
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ orders: items })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('순서 변경 중 오류가 발생했습니다.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('순서 변경 중 오류가 발생했습니다.');
        location.reload();
    });
}

// 서버에 순서 업데이트 (현재 카테고리 섹션용)
function updateOrderOnServer(categoryType, currentItem, siblingItem) {
    let url = '';
    let currentId = null;
    let siblingId = null;
    
    if (categoryType === 'course') {
        url = '/backoffice/courses/update-order';
        currentId = parseInt(currentItem.dataset.courseId);
        siblingId = parseInt(siblingItem.dataset.courseId);
    } else if (categoryType === 'operating_institution') {
        url = '/backoffice/operating-institutions/update-order';
        currentId = parseInt(currentItem.dataset.institutionId);
        siblingId = parseInt(siblingItem.dataset.institutionId);
    } else if (categoryType === 'project_period') {
        url = '/backoffice/project-periods/update-order';
        currentId = parseInt(currentItem.dataset.periodId);
        siblingId = parseInt(siblingItem.dataset.periodId);
    } else if (categoryType === 'country') {
        url = '/backoffice/countries/update-order';
        currentId = parseInt(currentItem.dataset.countryId);
        siblingId = parseInt(siblingItem.dataset.countryId);
    } else if (categoryType === 'schedule') {
        url = '/backoffice/schedules/update-order';
        currentId = parseInt(currentItem.dataset.scheduleId);
        siblingId = parseInt(siblingItem.dataset.scheduleId);
    }
    
    // 모든 항목의 순서 수집
    const container = currentItem.parentElement;
    let items = [];
    if (categoryType === 'course') {
        items = Array.from(container.querySelectorAll('tr[data-course-id]'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.courseId),
            order: index + 1
        }));
    } else if (categoryType === 'operating_institution') {
        items = Array.from(container.querySelectorAll('.institution-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.institutionId),
            order: index + 1
        }));
    } else if (categoryType === 'project_period') {
        items = Array.from(container.querySelectorAll('.period-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.periodId),
            order: index + 1
        }));
    } else if (categoryType === 'country') {
        items = Array.from(container.querySelectorAll('.country-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.countryId),
            order: index + 1
        }));
    } else if (categoryType === 'schedule') {
        items = Array.from(container.querySelectorAll('.schedule-item'));
        items = items.map((item, index) => ({
            id: parseInt(item.dataset.scheduleId),
            order: index + 1
        }));
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ orders: items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 성공 시 아무 작업도 하지 않음 (이미 DOM에서 변경됨)
        } else {
            alert('순서 변경 중 오류가 발생했습니다.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('순서 변경 중 오류가 발생했습니다.');
        location.reload();
    });
}

// 과정 수정을 위한 데이터 로드
function loadCourseForEdit(courseId) {
    fetch(`/backoffice/courses/${courseId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                showEditForm('course', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 운영기관 수정을 위한 데이터 로드
function loadInstitutionForEdit(institutionId) {
    fetch(`/backoffice/operating-institutions/${institutionId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                showEditForm('operating_institution', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 프로젝트기간 수정을 위한 데이터 로드
function loadPeriodForEdit(periodId) {
    fetch(`/backoffice/project-periods/${periodId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                showEditForm('project_period', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 국가 수정을 위한 데이터 로드
function loadCountryForEdit(countryId) {
    fetch(`/backoffice/countries/${countryId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                showEditForm('country', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 일정 수정을 위한 데이터 로드
function loadScheduleForEdit(scheduleId) {
    fetch(`/backoffice/schedules/${scheduleId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                showEditForm('schedule', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// 수정 폼 표시
function showEditForm(type, data) {
    showCurrentCategorySection();
    
    state.currentCategoryType = type;
    state.currentCategoryId = data.id;
    
    // 모든 수정 폼 숨기기
    document.querySelectorAll('.edit-form').forEach(form => {
        form.style.display = 'none';
    });
    
    // 해당 타입의 수정 폼 표시
    let formId = '';
    if (type === 'course') {
        formId = 'editCourseForm';
        const form = document.querySelector('#editCourseForm form');
        if (form) {
            document.getElementById('editNameKo').value = data.name_ko || '';
            document.getElementById('editNameEn').value = data.name_en || '';
            const hiddenId = form.querySelector('input[name="id"]');
            if (hiddenId) hiddenId.value = data.id;
        }
    } else if (type === 'operating_institution') {
        formId = 'editOperatingInstitutionForm';
        const form = document.querySelector('#editOperatingInstitutionForm form');
        if (form) {
            document.getElementById('editInstitutionNameKo').value = data.name_ko || '';
            document.getElementById('editInstitutionNameEn').value = data.name_en || '';
            document.getElementById('editCosmojinManagerName').value = data.cosmojin_manager_name || '';
            document.getElementById('editCosmojinManagerPhone').value = data.cosmojin_manager_phone || '';
            document.getElementById('editCosmojinManagerEmail').value = data.cosmojin_manager_email || '';
            document.getElementById('editKofhiManagerName').value = data.kofhi_manager_name || '';
            document.getElementById('editKofhiManagerPhone').value = data.kofhi_manager_phone || '';
            document.getElementById('editKofhiManagerEmail').value = data.kofhi_manager_email || '';
            const hiddenId = form.querySelector('input[name="id"]');
            if (hiddenId) hiddenId.value = data.id;
        }
    } else if (type === 'project_period') {
        formId = 'editProjectPeriodForm';
        const form = document.querySelector('#editProjectPeriodForm form');
        if (form) {
            document.getElementById('editPeriodNameKo').value = data.name_ko || '';
            document.getElementById('editPeriodNameEn').value = data.name_en || '';
            const hiddenId = form.querySelector('input[name="id"]');
            if (hiddenId) hiddenId.value = data.id;
        }
    } else if (type === 'country') {
        formId = 'editCountryForm';
        const form = document.querySelector('#editCountryForm form');
        if (form) {
            document.getElementById('editCountryNameKo').value = data.name_ko || '';
            document.getElementById('editCountryNameEn').value = data.name_en || '';
            // 참고자료 목록 로드 후 선택값 설정
            loadReferenceMaterials().then(() => {
                document.getElementById('editCountryReferenceMaterial').value = data.reference_material_id || '';
            });
            document.getElementById('editCountryDocumentName').value = data.document_name || '';
            // 제출마감일 형식 변환 (YYYY-MM-DD 형식으로)
            if (data.submission_deadline) {
                const deadlineDate = new Date(data.submission_deadline);
                if (!isNaN(deadlineDate.getTime())) {
                    const year = deadlineDate.getFullYear();
                    const month = String(deadlineDate.getMonth() + 1).padStart(2, '0');
                    const day = String(deadlineDate.getDate()).padStart(2, '0');
                    document.getElementById('editCountrySubmissionDeadline').value = `${year}-${month}-${day}`;
                } else {
                    document.getElementById('editCountrySubmissionDeadline').value = '';
                }
            } else {
                document.getElementById('editCountrySubmissionDeadline').value = '';
            }
            const hiddenId = form.querySelector('input[name="id"]');
            if (hiddenId) hiddenId.value = data.id;
        }
    } else if (type === 'schedule') {
        formId = 'editScheduleForm';
        const form = document.querySelector('#editScheduleForm form');
        if (form) {
            document.getElementById('editScheduleNameKo').value = data.name_ko || '';
            document.getElementById('editScheduleNameEn').value = data.name_en || '';
            
            // 날짜 형식 변환 (YYYY-MM-DD 형식으로)
            const formatDateForInput = (dateString) => {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            document.getElementById('editScheduleStartDate').value = formatDateForInput(data.start_date);
            document.getElementById('editScheduleEndDate').value = formatDateForInput(data.end_date);
            const hiddenId = form.querySelector('input[name="id"]');
            if (hiddenId) hiddenId.value = data.id;
        }
    }
    
    const form = document.getElementById(formId);
    if (form) {
        form.style.display = 'block';
    }
    
    // 순서 변경 버튼 표시 (과정, 운영기관, 프로젝트기간, 국가, 일정관리)
    const orderControls = document.querySelector('.order-controls');
    if (orderControls) {
        if (type === 'course' || type === 'operating_institution' || type === 'project_period' || type === 'country' || type === 'schedule') {
            orderControls.style.display = 'flex';
        } else {
            orderControls.style.display = 'none';
        }
    }
}

// 참고자료 목록 로드
function loadReferenceMaterials() {
    return fetch('/backoffice/project-terms/reference-materials', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const select = document.getElementById('editCountryReferenceMaterial');
                if (select) {
                    // 기존 옵션 유지 (첫 번째 "선택하세요" 옵션)
                    const firstOption = select.querySelector('option[value=""]');
                    select.innerHTML = '';
                    if (firstOption) {
                        select.appendChild(firstOption);
                    } else {
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = '선택하세요';
                        select.appendChild(defaultOption);
                    }
                    
                    // 참고자료 목록 추가
                    result.data.forEach(reference => {
                        const option = document.createElement('option');
                        option.value = reference.id;
                        option.textContent = reference.title || `참고자료 #${reference.id}`;
                        select.appendChild(option);
                    });
                }
            }
            return result;
        })
        .catch(error => {
            console.error('참고자료 목록 로드 실패:', error);
            return { success: false };
        });
}
