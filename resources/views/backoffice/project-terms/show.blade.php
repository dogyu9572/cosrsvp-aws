@extends('backoffice.layouts.app')

@section('title', '프로젝트 기수 상세')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <style>
        .board-table tbody tr:hover {
            background-color: inherit !important;
        }
        .board-table tbody tr {
            height: auto;
            min-height: 40px;
        }
        .board-table tbody tr td {
            vertical-align: top;
            padding: 0.75rem;
            height: 150px;
        }
        .selected-item {
            font-weight: normal !important;
        }
        .selected-item a {
            font-weight: normal !important;
        }
        .institution-item,
        .period-item,
        .country-item,
        .schedule-item {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger board-hidden-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.project-terms.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> 목록으로
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 기수 정보 -->
                <div class="board-form-row" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e9ecef;">
                    <div class="board-form-col board-form-col-6">
                        <div class="board-form-group">
                            <label class="board-form-label">기수명</label>
                            <input type="text" class="board-form-control" value="{{ $projectTerm->name }}" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>
                </div>

                <!-- 과정 등록 -->
                <div id="courseRegisterSection" class="register-section" style="margin-bottom: 2rem;">
                    <h4 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: #333;">과정 등록하기</h4>
                    <form id="courseForm" method="POST" action="{{ route('backoffice.courses.store') }}">
                        @csrf
                        <input type="hidden" name="project_term_id" value="{{ $projectTerm->id }}">
                        <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="course_name_ko" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                <input type="text" id="course_name_ko" name="name_ko" class="board-form-control" required style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="course_name_en" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                <input type="text" id="course_name_en" name="name_en" class="board-form-control" style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex-shrink: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 추가
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 운영기관 등록 -->
                <div id="operatingInstitutionRegisterSection" class="register-section" style="display: none; margin-bottom: 2rem;">
                    <h4 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: #333;">운영기관 등록하기</h4>
                    <form id="operatingInstitutionForm" method="POST" action="{{ route('backoffice.operating-institutions.store') }}">
                        @csrf
                        <input type="hidden" id="operating_institution_course_id" name="course_id" value="">
                        <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="institution_name_ko" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                <input type="text" id="institution_name_ko" name="name_ko" class="board-form-control" required style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="institution_name_en" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                <input type="text" id="institution_name_en" name="name_en" class="board-form-control" style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex-shrink: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 추가
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 프로젝트기간 등록 -->
                <div id="projectPeriodRegisterSection" class="register-section" style="display: none; margin-bottom: 2rem;">
                    <h4 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: #333;">프로젝트기간 등록하기</h4>
                    <form id="projectPeriodForm" method="POST" action="{{ route('backoffice.project-periods.store') }}">
                        @csrf
                        <input type="hidden" id="project_period_institution_id" name="operating_institution_id" value="">
                        <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="period_name_ko" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                <input type="text" id="period_name_ko" name="name_ko" class="board-form-control" required style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="period_name_en" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                <input type="text" id="period_name_en" name="name_en" class="board-form-control" style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex-shrink: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 추가
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 국가 등록 -->
                <div id="countryRegisterSection" class="register-section" style="display: none; margin-bottom: 2rem;">
                    <h4 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: #333;">국가 등록하기</h4>
                    <form id="countryForm" method="POST" action="{{ route('backoffice.countries.store') }}">
                        @csrf
                        <input type="hidden" id="country_period_id" name="project_period_id" value="">
                        <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="country_name_ko" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                <input type="text" id="country_name_ko" name="name_ko" class="board-form-control" required style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="country_name_en" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                <input type="text" id="country_name_en" name="name_en" class="board-form-control" style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex-shrink: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 추가
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 일정 등록 -->
                <div id="scheduleRegisterSection" class="register-section" style="display: none; margin-bottom: 2rem;">
                    <h4 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: #333;">일정 등록하기</h4>
                    <form id="scheduleForm" method="POST" action="{{ route('backoffice.schedules.store') }}">
                        @csrf
                        <input type="hidden" id="schedule_country_id" name="country_id" value="">
                        <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="schedule_name_ko" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                <input type="text" id="schedule_name_ko" name="name_ko" class="board-form-control" required style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                <label for="schedule_name_en" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                <input type="text" id="schedule_name_en" name="name_en" class="board-form-control" style="flex: 1;">
                            </div>
                            <div class="board-form-col" style="flex-shrink: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> 추가
                                </button>
                            </div>
                        </div>                        
                    </form>
                </div>

                <!-- 과정 리스트 및 계층 구조 관리 -->
                <div style="margin-top: 2rem;">
                    
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w20">과정</th>
                                    <th class="w20">운영기관</th>
                                    <th class="w20">프로젝트기간</th>
                                    <th class="w20">국가</th>
                                    <th class="w20">일정관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projectTerm->courses as $course)
                                    <tr data-course-id="{{ $course->id }}">
                                        <td>
                                            <div class="course-item" data-course-id="{{ $course->id }}">
                                                <a href="#" class="course-link" data-course-id="{{ $course->id }}" style="color: #333; text-decoration: none;">
                                                    {{ $course->name_ko }}
                                                    @if($course->name_en)
                                                        / {{ $course->name_en }}
                                                    @endif
                                                </a>
                                            </div>
                                        </td>
                                        <td class="operating-institution-cell" data-course-id="{{ $course->id }}">
                                        </td>
                                        <td class="project-period-cell">
                                        </td>
                                        <td class="country-cell">
                                        </td>
                                        <td class="schedule-cell">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding: 2rem; color: #6c757d;">등록된 과정이 없습니다.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 현재 선택된 카테고리 수정 영역 -->
                <div class="current-category-section" id="currentCategorySection" style="display: none; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #333;">현재 카테고리</h4>
                        <div class="order-controls" style="display: none;">
                            <button type="button" class="btn btn-sm btn-secondary order-up-btn" title="위로 이동">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary order-down-btn" title="아래로 이동">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- 과정 수정 폼 -->
                    <div id="editCourseForm" class="edit-form" style="display: none;">
                        <form id="categoryEditForm" data-type="course">
                            <input type="hidden" id="editCategoryType" name="type" value="course">
                            <input type="hidden" id="editCategoryId" name="id">
                            <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editNameKo" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                    <input type="text" id="editNameKo" name="name_ko" class="board-form-control" required style="flex: 1;">
                                </div>
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editNameEn" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                    <input type="text" id="editNameEn" name="name_en" class="board-form-control" style="flex: 1;">
                                </div>
                            </div>
                            <div class="board-form-row">
                                <div class="board-form-col">
                                    <div class="board-form-group">
                                        <div class="board-btn-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 저장
                                            </button>
                                            <button type="button" class="btn btn-danger" id="deleteCategoryBtn">
                                                <i class="fas fa-trash"></i> 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 운영기관 수정 폼 -->
                    <div id="editOperatingInstitutionForm" class="edit-form" style="display: none;">
                        <form id="categoryEditFormInstitution" data-type="operating_institution">
                            <input type="hidden" name="type" value="operating_institution">
                            <input type="hidden" name="id">
                            <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editInstitutionNameKo" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                    <input type="text" id="editInstitutionNameKo" name="name_ko" class="board-form-control" required style="flex: 1;">
                                </div>
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editInstitutionNameEn" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                    <input type="text" id="editInstitutionNameEn" name="name_en" class="board-form-control" style="flex: 1;">
                                </div>
                            </div>
                            <div class="board-form-row" style="margin-top: 1rem;">
                                <div class="board-form-col board-form-col-6">
                                    <h6 style="margin: 0 0 1rem 0; font-size: 0.875rem; font-weight: 600; color: #333;">코스모진 담당자</h6>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">이름:</label>
                                        <input type="text" id="editCosmojinManagerName" name="cosmojin_manager_name" class="board-form-control" style="flex: 1;">
                                    </div>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">전화번호:</label>
                                        <input type="text" id="editCosmojinManagerPhone" name="cosmojin_manager_phone" class="board-form-control" style="flex: 1;">
                                    </div>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">이메일:</label>
                                        <input type="email" id="editCosmojinManagerEmail" name="cosmojin_manager_email" class="board-form-control" style="flex: 1;">
                                    </div>
                                </div>
                                <div class="board-form-col board-form-col-6">
                                    <h6 style="margin: 0 0 1rem 0; font-size: 0.875rem; font-weight: 600; color: #333;">KOFHI 담당자</h6>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">이름:</label>
                                        <input type="text" id="editKofhiManagerName" name="kofhi_manager_name" class="board-form-control" style="flex: 1;">
                                    </div>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">전화번호:</label>
                                        <input type="text" id="editKofhiManagerPhone" name="kofhi_manager_phone" class="board-form-control" style="flex: 1;">
                                    </div>
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                                        <label class="board-form-label" style="margin: 0; min-width: 70px; font-weight: normal;">이메일:</label>
                                        <input type="email" id="editKofhiManagerEmail" name="kofhi_manager_email" class="board-form-control" style="flex: 1;">
                                    </div>
                                </div>
                            </div>
                            <div class="board-form-row">
                                <div class="board-form-col">
                                    <div class="board-form-group">
                                        <div class="board-btn-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 저장
                                            </button>
                                            <button type="button" class="btn btn-danger" id="deleteCategoryBtn">
                                                <i class="fas fa-trash"></i> 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 프로젝트기간 수정 폼 -->
                    <div id="editProjectPeriodForm" class="edit-form" style="display: none;">
                        <form id="categoryEditFormPeriod" data-type="project_period">
                            <input type="hidden" name="type" value="project_period">
                            <input type="hidden" name="id">
                            <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editPeriodNameKo" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                    <input type="text" id="editPeriodNameKo" name="name_ko" class="board-form-control" required style="flex: 1;">
                                </div>
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editPeriodNameEn" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                    <input type="text" id="editPeriodNameEn" name="name_en" class="board-form-control" style="flex: 1;">
                                </div>
                            </div>
                            <div class="board-form-row">
                                <div class="board-form-col">
                                    <div class="board-form-group">
                                        <div class="board-btn-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 저장
                                            </button>
                                            <button type="button" class="btn btn-danger" id="deleteCategoryBtn">
                                                <i class="fas fa-trash"></i> 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 국가 수정 폼 -->
                    <div id="editCountryForm" class="edit-form" style="display: none;">
                        <form id="categoryEditFormCountry" data-type="country">
                            <input type="hidden" name="type" value="country">
                            <input type="hidden" name="id">
                            <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editCountryNameKo" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                    <input type="text" id="editCountryNameKo" name="name_ko" class="board-form-control" required style="flex: 1;">
                                </div>
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editCountryNameEn" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                    <input type="text" id="editCountryNameEn" name="name_en" class="board-form-control" style="flex: 1;">
                                </div>
                            </div>
                            <div class="board-form-row" style="margin-top: 1rem;">
                                <div class="board-form-col">
                                    <div class="board-form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                                        <label for="editCountryReferenceMaterial" class="board-form-label" style="margin: 0; min-width: 50px; font-weight: normal;">참고자료:</label>
                                        <select id="editCountryReferenceMaterial" name="reference_material_id" class="board-form-control" style="flex: 1; max-width: 400px;">
                                            <option value="">선택하세요</option>
                                            <!-- 추후 reference_materials 테이블 생성 후 연동 -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="board-form-row" style="margin-top: 1rem;">
                                <div class="board-form-col">
                                    <div class="board-form-group">
                                        <div class="board-btn-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 저장
                                            </button>
                                            <button type="button" class="btn btn-danger" id="deleteCategoryBtn">
                                                <i class="fas fa-trash"></i> 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 일정 수정 폼 -->
                    <div id="editScheduleForm" class="edit-form" style="display: none;">
                        <form id="categoryEditFormSchedule" data-type="schedule">
                            <input type="hidden" name="type" value="schedule">
                            <input type="hidden" name="id">
                            <div class="board-form-row" style="display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editScheduleNameKo" class="board-form-label" style="margin: 0; min-width: 50px;">국문:</label>
                                    <input type="text" id="editScheduleNameKo" name="name_ko" class="board-form-control" required style="flex: 1;">
                                </div>
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label for="editScheduleNameEn" class="board-form-label" style="margin: 0; min-width: 50px;">영문:</label>
                                    <input type="text" id="editScheduleNameEn" name="name_en" class="board-form-control" style="flex: 1;">
                                </div>
                            </div>
                            <div class="board-form-row" style="margin-top: 1rem; display: flex; align-items: center; gap: 1rem;">
                                <div class="board-form-col" style="flex: 1; display: flex; align-items: center; gap: 0.5rem;">
                                    <label class="board-form-label" style="margin: 0; min-width: 50px;">일정:</label>
                                    <input type="date" id="editScheduleStartDate" name="start_date" class="board-form-control" style="flex: 1; max-width: 200px;">
                                    <span style="margin: 0 0.5rem;">~</span>
                                    <input type="date" id="editScheduleEndDate" name="end_date" class="board-form-control" style="flex: 1; max-width: 200px;">
                                </div>
                            </div>
                            <div class="board-form-row" style="margin-top: 1rem;">
                                <div class="board-form-col">
                                    <div class="board-form-group">
                                        <div class="board-btn-group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> 저장
                                            </button>
                                            <button type="button" class="btn btn-danger" id="deleteCategoryBtn">
                                                <i class="fas fa-trash"></i> 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/project-terms.js') }}"></script>
@endsection
