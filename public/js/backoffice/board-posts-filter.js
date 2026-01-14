/**
 * 게시글 목록 필터 JavaScript (프로젝트 기수 필터)
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;
    
    const termSelect = document.getElementById('filter_project_term_id');
    const courseSelect = document.getElementById('filter_course_id');
    const institutionSelect = document.getElementById('filter_operating_institution_id');
    const periodSelect = document.getElementById('filter_project_period_id');
    const countrySelect = document.getElementById('filter_country_id');
    
    if (!termSelect) return;
    
    // 이벤트 리스너 등록
    termSelect.addEventListener('change', handleTermChange);
    courseSelect.addEventListener('change', handleCourseChange);
    institutionSelect.addEventListener('change', handleInstitutionChange);
    periodSelect.addEventListener('change', handlePeriodChange);
    
    // 초기 로드 시 기존 선택값 복원
    initializeFilters();
    
    async function initializeFilters() {
        // URL 파라미터에서 선택된 값 가져오기
        const termId = termSelect.value || getUrlParameter('filter_project_term_id');
        const courseId = courseSelect.dataset.selected || getUrlParameter('filter_course_id');
        const institutionId = institutionSelect.dataset.selected || getUrlParameter('filter_operating_institution_id');
        const periodId = periodSelect.dataset.selected || getUrlParameter('filter_project_period_id');
        const countryId = countrySelect.dataset.selected || getUrlParameter('filter_country_id');
        
        if (termId) {
            termSelect.value = termId;
            await loadCourses(termId, courseId);
            if (courseId) {
                courseSelect.value = courseId;
                await loadInstitutions(courseId, institutionId);
                if (institutionId) {
                    institutionSelect.value = institutionId;
                    await loadPeriods(institutionId, periodId);
                    if (periodId) {
                        periodSelect.value = periodId;
                        await loadCountries(periodId, countryId);
                        if (countryId) {
                            countrySelect.value = countryId;
                        }
                    }
                }
            }
        }
    }
    
    // URL 파라미터 가져오기 함수
    function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name) || '';
    }
    
    async function handleTermChange() {
        resetSelect(courseSelect);
        resetSelect(institutionSelect);
        resetSelect(periodSelect);
        resetSelect(countrySelect);
        
        const termId = termSelect.value;
        if (termId) {
            await loadCourses(termId, null);
        }
    }
    
    async function handleCourseChange() {
        resetSelect(institutionSelect);
        resetSelect(periodSelect);
        resetSelect(countrySelect);
        
        const courseId = courseSelect.value;
        if (courseId) {
            await loadInstitutions(courseId, null);
        }
    }
    
    async function handleInstitutionChange() {
        resetSelect(periodSelect);
        resetSelect(countrySelect);
        
        const institutionId = institutionSelect.value;
        if (institutionId) {
            await loadPeriods(institutionId, null);
        }
    }
    
    async function handlePeriodChange() {
        resetSelect(countrySelect);
        
        const periodId = periodSelect.value;
        if (periodId) {
            await loadCountries(periodId, null);
        }
    }
    
    async function loadCourses(termId, selectedId = null) {
        try {
            const response = await fetch(`/backoffice/courses/get-by-term/${termId}`);
            const courses = await response.json();
            const valueToSelect = selectedId || courseSelect.dataset.selected || courseSelect.value;
            populateSelect(courseSelect, courses, 'name_ko', 'name_en', valueToSelect);
        } catch (error) {
            console.error('과정 로드 오류:', error);
        }
    }
    
    async function loadInstitutions(courseId, selectedId = null) {
        try {
            const response = await fetch(`/backoffice/operating-institutions/get-by-course/${courseId}`);
            const institutions = await response.json();
            const valueToSelect = selectedId || institutionSelect.dataset.selected || institutionSelect.value;
            populateSelect(institutionSelect, institutions, 'name_ko', 'name_en', valueToSelect);
        } catch (error) {
            console.error('운영기관 로드 오류:', error);
        }
    }
    
    async function loadPeriods(institutionId, selectedId = null) {
        try {
            const response = await fetch(`/backoffice/project-periods/get-by-institution/${institutionId}`);
            const periods = await response.json();
            const valueToSelect = selectedId || periodSelect.dataset.selected || periodSelect.value;
            populateSelect(periodSelect, periods, 'name_ko', 'name_en', valueToSelect);
        } catch (error) {
            console.error('프로젝트기간 로드 오류:', error);
        }
    }
    
    async function loadCountries(periodId, selectedId = null) {
        try {
            const response = await fetch(`/backoffice/countries/get-by-period/${periodId}`);
            const countries = await response.json();
            const valueToSelect = selectedId || countrySelect.dataset.selected || countrySelect.value;
            populateSelect(countrySelect, countries, 'name_ko', 'name_en', valueToSelect);
        } catch (error) {
            console.error('국가 로드 오류:', error);
        }
    }
    
    function populateSelect(select, items, nameKoField, nameEnField, selectedValue = null) {
        const valueToSelect = selectedValue !== null ? selectedValue : (select.dataset.selected || select.value);
        select.innerHTML = '<option value="">전체</option>';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item[nameKoField] + (item[nameEnField] ? ' / ' + item[nameEnField] : '');
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
    
    function resetSelect(select) {
        select.innerHTML = '<option value="">전체</option>';
        select.value = '';
    }
});
