@extends('backoffice.layouts.app')

@section('title', '회원 수정')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/members.css') }}">
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <div style="display: flex; gap: 10px; margin-left: auto;">
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('memberForm').submit();">
                저장
            </button>
            <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary btn-sm">목록</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-card">
        <div class="board-card-body">
            @if ($errors->any())
                <div class="board-alert board-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="memberForm" action="{{ route('backoffice.members.update', $member) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- 회원관리 제목 -->
                <div class="board-form-group" style="margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: bold; margin: 0;">[회원관리]</h2>
                </div>

                <!-- 관리자 작성 섹션 -->
                <div class="board-form-group" style="margin-bottom: 20px;">
                    <div class="form-category-label">관리자 작성</div>
                    
                    <!-- 프로젝트 기수 관련 필드 (한 줄에 5개) -->
                    <div class="board-form-group" style="margin-bottom: 10px;">
                        <div class="board-form-row" style="grid-template-columns: repeat(5, 1fr);">
                            <div class="board-form-col">
                                <div class="board-form-group">
                                    <label for="project_term_id" class="board-form-label">
                                        기수 <span class="required">*</span>
                                    </label>
                                    <select id="project_term_id" name="project_term_id" class="board-form-control" required tabindex="1">
                                        <option value="">전체</option>
                                        @foreach($projectTerms as $term)
                                            <option value="{{ $term->id }}" @selected(old('project_term_id', $member->project_term_id) == $term->id)>
                                                {{ $term->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="board-form-col">
                                <div class="board-form-group">
                                    <label for="course_id" class="board-form-label">과정</label>
                                    <select id="course_id" name="course_id" class="board-form-control" @if(!old('course_id', $member->course_id)) disabled @endif tabindex="2">
                                        <option value="">전체</option>
                                        @if(old('course_id', $member->course_id))
                                            @php
                                                $selectedCourse = \App\Models\Course::find(old('course_id', $member->course_id));
                                            @endphp
                                            @if($selectedCourse)
                                                <option value="{{ $selectedCourse->id }}" selected>{{ $selectedCourse->name_ko }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="board-form-col">
                                <div class="board-form-group">
                                    <label for="operating_institution_id" class="board-form-label">운영기관</label>
                                    <select id="operating_institution_id" name="operating_institution_id" class="board-form-control" @if(!old('operating_institution_id', $member->operating_institution_id)) disabled @endif tabindex="3">
                                        <option value="">전체</option>
                                        @if(old('operating_institution_id', $member->operating_institution_id))
                                            @php
                                                $selectedInstitution = \App\Models\OperatingInstitution::find(old('operating_institution_id', $member->operating_institution_id));
                                            @endphp
                                            @if($selectedInstitution)
                                                <option value="{{ $selectedInstitution->id }}" selected>{{ $selectedInstitution->name_ko }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="board-form-col">
                                <div class="board-form-group">
                                    <label for="project_period_id" class="board-form-label">프로젝트기간</label>
                                    <select id="project_period_id" name="project_period_id" class="board-form-control" @if(!old('project_period_id', $member->project_period_id)) disabled @endif tabindex="4">
                                        <option value="">전체</option>
                                        @if(old('project_period_id', $member->project_period_id))
                                            @php
                                                $selectedPeriod = \App\Models\ProjectPeriod::find(old('project_period_id', $member->project_period_id));
                                            @endphp
                                            @if($selectedPeriod)
                                                <option value="{{ $selectedPeriod->id }}" selected>{{ $selectedPeriod->name_ko }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="board-form-col">
                                <div class="board-form-group">
                                    <label for="country_id" class="board-form-label">국가</label>
                                    <select id="country_id" name="country_id" class="board-form-control" @if(!old('country_id', $member->country_id)) disabled @endif tabindex="5">
                                        <option value="">전체</option>
                                        @if(old('country_id', $member->country_id))
                                            @php
                                                $selectedCountry = \App\Models\Country::find(old('country_id', $member->country_id));
                                            @endphp
                                            @if($selectedCountry)
                                                <option value="{{ $selectedCountry->id }}" selected>{{ $selectedCountry->name_ko }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 관리자 작성 필드들 (두 컬럼) -->
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="login_id" class="board-form-label">
                                    아이디 <span class="required">*</span>
                                </label>
                                <input type="text" id="login_id" name="login_id" class="board-form-control" value="{{ old('login_id', $member->login_id) }}" required tabindex="6">
                            </div>
                            <div class="board-form-group">
                                <label for="password" class="board-form-label">비밀번호</label>
                                <button type="button" class="btn btn-warning btn-sm" onclick="resetPassword({{ $member->id }});" tabindex="9">
                                    초기화
                                </button>
                            </div>
                            <div class="board-form-group">
                                <label for="name" class="board-form-label">
                                    성명 <span class="required">*</span>
                                </label>
                                <input type="text" id="name" name="name" class="board-form-control" value="{{ old('name', $member->name) }}" required tabindex="11">
                            </div>
                            <div class="board-form-group">
                                <label for="email" class="board-form-label">이메일</label>
                                <input type="email" id="email" name="email" class="board-form-control" value="{{ old('email', $member->email) }}" tabindex="13">
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="gender" class="board-form-label">성별</label>
                                <div style="display: flex; gap: 15px;">
                                    <div class="board-checkbox-item">
                                        <input type="radio" id="gender_male" name="gender" value="male" class="board-checkbox-input" @checked(old('gender', $member->gender) == 'male') tabindex="7">
                                        <label for="gender_male" class="board-form-label">남자</label>
                                    </div>
                                    <div class="board-checkbox-item">
                                        <input type="radio" id="gender_female" name="gender" value="female" class="board-checkbox-input" @checked(old('gender', $member->gender) == 'female') tabindex="8">
                                        <label for="gender_female" class="board-form-label">여자</label>
                                    </div>
                                </div>
                            </div>
                            <div class="board-form-group">
                                <label for="phone_local" class="board-form-label">현지 전화번호</label>
                                <input type="text" id="phone_local" name="phone_local" class="board-form-control" value="{{ old('phone_local', $member->phone_local) }}" tabindex="10">
                            </div>
                            <div class="board-form-group">
                                <label for="phone_kr" class="board-form-label">한국 전화번호</label>
                                <input type="text" id="phone_kr" name="phone_kr" class="board-form-control" value="{{ old('phone_kr', $member->phone_kr) }}" tabindex="12">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 회원 작성 섹션 -->
                <div class="board-form-group" style="margin-bottom: 20px;">
                    <div class="form-category-label">회원 작성</div>
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="birth_date" class="board-form-label">생년월일</label>
                                <input type="date" id="birth_date" name="birth_date" class="board-form-control" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" tabindex="14">
                            </div>
                            <div class="board-form-group">
                                <label for="passport_number" class="board-form-label">여권번호</label>
                                <input type="text" id="passport_number" name="passport_number" class="board-form-control" value="{{ old('passport_number', $member->passport_number) }}" tabindex="16">
                            </div>
                            <div class="board-form-group">
                                <label for="passport_expiry" class="board-form-label">여권유효기간</label>
                                <input type="date" id="passport_expiry" name="passport_expiry" class="board-form-control" value="{{ old('passport_expiry', $member->passport_expiry?->format('Y-m-d')) }}" tabindex="18">
                            </div>
                            <div class="board-form-group">
                                <label for="alien_registration_number" class="board-form-label">외국인등록번호</label>
                                <input type="text" id="alien_registration_number" name="alien_registration_number" class="board-form-control" value="{{ old('alien_registration_number', $member->alien_registration_number) }}" tabindex="20">
                            </div>
                            <div class="board-form-group">
                                <label for="alien_registration_expiry" class="board-form-label">외국인등록증 유효기간</label>
                                <input type="date" id="alien_registration_expiry" name="alien_registration_expiry" class="board-form-control" value="{{ old('alien_registration_expiry', $member->alien_registration_expiry?->format('Y-m-d')) }}" tabindex="22">
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="occupation" class="board-form-label">직업</label>
                                <input type="text" id="occupation" name="occupation" class="board-form-control" value="{{ old('occupation', $member->occupation) }}" tabindex="15">
                            </div>
                            <div class="board-form-group">
                                <label for="major" class="board-form-label">전공</label>
                                <input type="text" id="major" name="major" class="board-form-control" value="{{ old('major', $member->major) }}" tabindex="17">
                            </div>
                            <div class="board-form-group">
                                <label for="affiliation" class="board-form-label">소속</label>
                                <input type="text" id="affiliation" name="affiliation" class="board-form-control" value="{{ old('affiliation', $member->affiliation) }}" tabindex="19">
                            </div>
                            <div class="board-form-group">
                                <label for="department" class="board-form-label">부서</label>
                                <input type="text" id="department" name="department" class="board-form-control" value="{{ old('department', $member->department) }}" tabindex="21">
                            </div>
                            <div class="board-form-group">
                                <label for="position" class="board-form-label">직위</label>
                                <input type="text" id="position" name="position" class="board-form-control" value="{{ old('position', $member->position) }}" tabindex="23">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 연수 관련정보 (두 컬럼) -->
                <div class="board-form-group" style="margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: bold; margin: 0;">[연수 관련정보]</h2>
                    <div class="form-category-label">관리자 작성</div>
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="hotel_name" class="board-form-label">호텔명</label>
                                <input type="text" id="hotel_name" name="hotel_name" class="board-form-control" value="{{ old('hotel_name', $member->hotel_name) }}" tabindex="27">
                            </div>
                            <div class="board-form-group" style="display: flex; align-items: flex-start; gap: 10px;">
                                <label for="hotel_address" class="board-form-label" style="margin: 0; min-width: 100px; flex-shrink: 0; padding-top: 8px;">호텔주소</label>
                                <div style="flex: 1;">
                                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                                        <input type="text" id="hotel_address" name="hotel_address" class="board-form-control" style="flex: 1;" value="{{ old('hotel_address', $member->hotel_address) }}" tabindex="29">
                                        <button type="button" class="btn btn-sm btn-secondary" id="hotelAddressSearchBtn">주소찾기</button>
                                    </div>
                                    <input type="text" id="hotel_address_detail" name="hotel_address_detail" class="board-form-control" value="{{ old('hotel_address_detail', $member->hotel_address_detail) }}" placeholder="상세주소" tabindex="30">
                                </div>
                            </div>
                            <div class="board-form-group">
                                <label for="cultural_experience" class="board-form-label">문화체험</label>
                                <textarea id="cultural_experience" name="cultural_experience" class="board-form-control board-form-textarea" rows="1" tabindex="33">{{ old('cultural_experience', $member->cultural_experience) }}</textarea>
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="training_period" class="board-form-label">연수기간</label>
                                <input type="text" id="training_period" name="training_period" class="board-form-control" value="{{ old('training_period', $member->training_period) }}" tabindex="28">
                            </div>
                            <div class="board-form-group">
                                <label for="visa_type" class="board-form-label">비자종류</label>
                                <input type="text" id="visa_type" name="visa_type" class="board-form-control" value="{{ old('visa_type', $member->visa_type) }}" tabindex="31">
                            </div>
                            <div class="board-form-group">
                                <label for="account_info" class="board-form-label">계좌번호</label>
                                <input type="text" id="account_info" name="account_info" class="board-form-control" value="{{ old('account_info', $member->account_info) }}" tabindex="32">
                            </div>
                            <div class="board-form-group">
                                <label for="insurance_status" class="board-form-label">보험가입여부</label>
                                <select id="insurance_status" name="insurance_status" class="board-form-control" tabindex="34">
                                    <option value="">선택</option>
                                    <option value="yes" @selected(old('insurance_status', $member->insurance_status) == 'yes')>예</option>
                                    <option value="no" @selected(old('insurance_status', $member->insurance_status) == 'no')>아니오</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-category-label">회원 작성</div>
                    <!-- 옷 사이즈, 특이식성 -->
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="clothing_size" class="board-form-label">옷 사이즈</label>
                                <input type="text" id="clothing_size" name="clothing_size" class="board-form-control" value="{{ old('clothing_size', $member->clothing_size) }}" tabindex="24">
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="dietary_restrictions" class="board-form-label">특이식성</label>
                                <input type="text" id="dietary_restrictions" name="dietary_restrictions" class="board-form-control" value="{{ old('dietary_restrictions', $member->dietary_restrictions) }}" tabindex="25">
                            </div>
                        </div>
                    </div>
                    <!-- 특이사항 및 요청사항 -->
                    <div class="board-form-row">
                        <div class="board-form-col" style="grid-column: 1 / -1;">
                            <div class="board-form-group">
                                <label for="special_requests" class="board-form-label">특이사항 및 요청사항</label>
                                <textarea id="special_requests" name="special_requests" class="board-form-control board-form-textarea" rows="1" tabindex="26">{{ old('special_requests', $member->special_requests) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 입출국 정보 (두 컬럼) -->
                <div class="board-form-group" style="margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: bold; margin: 0; margin-bottom: 15px;">[입출국 정보]</h2>
                    <div class="board-form-row">
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="ticket_file" class="board-form-label">항공권</label>
                                <input type="file" id="ticket_file" name="ticket_file" class="board-form-control" tabindex="35">
                                @if($member->ticket_file)
                                    @php
                                        $storedFileName = basename($member->ticket_file);
                                        $originalFileName = preg_replace('/_\d+\./', '.', $storedFileName);
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
                                        <a href="{{ route('backoffice.members.download-ticket-file', $member) }}" 
                                           target="_blank" 
                                           style="color: #4a90e2; text-decoration: none; font-size: 13px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-file-download"></i>
                                            {{ $originalFileName }}
                                        </a>
                                        <button type="button" 
                                                class="delete-ticket-file-btn"
                                                data-member-id="{{ $member->id }}"
                                                data-url="{{ route('backoffice.members.delete-ticket-file', $member) }}"
                                                style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 0; font-size: 13px; display: flex; align-items: center; gap: 5px;">
                                            <i class="fas fa-trash-alt"></i>
                                            삭제
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="board-form-group">
                                <label for="departure_location" class="board-form-label">출발지</label>
                                <input type="text" id="departure_location" name="departure_location" class="board-form-control" value="{{ old('departure_location', $member->departure_location) }}" tabindex="37">
                            </div>
                            <div class="board-form-group">
                                <label for="entry_date" class="board-form-label">입국일자</label>
                                <input type="date" id="entry_date" name="entry_date" class="board-form-control" value="{{ old('entry_date', $member->entry_date?->format('Y-m-d')) }}" tabindex="39">
                            </div>
                            <div class="board-form-group">
                                <label for="entry_flight" class="board-form-label">입국 항공편</label>
                                <input type="text" id="entry_flight" name="entry_flight" class="board-form-control" value="{{ old('entry_flight', $member->entry_flight) }}" tabindex="41">
                            </div>
                        </div>
                        <div class="board-form-col">
                            <div class="board-form-group">
                                <label for="arrival_location" class="board-form-label">도착지</label>
                                <input type="text" id="arrival_location" name="arrival_location" class="board-form-control" value="{{ old('arrival_location', $member->arrival_location) }}" tabindex="36">
                            </div>
                            <div class="board-form-group">
                                <label for="exit_date" class="board-form-label">출국일자</label>
                                <input type="date" id="exit_date" name="exit_date" class="board-form-control" value="{{ old('exit_date', $member->exit_date?->format('Y-m-d')) }}" tabindex="38">
                            </div>
                            <div class="board-form-group">
                                <label for="exit_flight" class="board-form-label">출국 항공편</label>
                                <input type="text" id="exit_flight" name="exit_flight" class="board-form-control" value="{{ old('exit_flight', $member->exit_flight) }}" tabindex="40">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="board-form-group" style="margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: bold; margin: 0; margin-bottom: 15px;">[제출서류]</h2>
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th>서류명</th>
                                    <th>파일확인</th>
                                    <th>제출마감일</th>
                                    <th>제출일</th>
                                    <th>제출여부</th>
                                    <th>보완요청</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($member->documents && $member->documents->count() > 0)
                                    @foreach($member->documents as $document)
                                        <tr>
                                            <td>{{ $document->document_name }}</td>
                                            <td>
                                                @if($document->file_path)
                                                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                                                        {{ basename($document->file_path) }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $document->submission_deadline?->format('Y.m.d') }}</td>
                                            <td>{{ $document->submitted_at?->format('Y.m.d') }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $document->status }}" id="status_badge_{{ $document->id }}">
                                                @if($document->status == 'submitted' || $document->status == 'resubmitted')
                                                    Submission
                                                @elseif($document->status == 'supplement_requested')
                                                    Rejection
                                                @else
                                                    Not submitted
                                                @endif
                                            </span>
                                        </td>
                                            <td>
                                                @if($document->status == 'supplement_requested')
                                                    <input type="text" 
                                                           id="supplement_input_{{ $document->id }}"
                                                           class="board-form-control supplement-input" 
                                                           placeholder="보완요청 내용" 
                                                           value="{{ old('supplement_request.' . $document->id, $document->supplement_request_content) }}"
                                                           data-document-id="{{ $document->id }}"
                                                           style="display: inline-block; width: auto; margin-right: 5px;"
                                                           readonly>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success complete-request-btn" 
                                                            data-document-id="{{ $document->id }}"
                                                            data-member-id="{{ $member->id }}">완료 처리</button>
                                                @else
                                                    <input type="text" 
                                                           id="supplement_input_{{ $document->id }}"
                                                           class="board-form-control supplement-input" 
                                                           placeholder="보완요청 내용" 
                                                           value="{{ old('supplement_request.' . $document->id, $document->supplement_request_content) }}"
                                                           data-document-id="{{ $document->id }}"
                                                           style="display: inline-block; width: auto; margin-right: 5px;">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-warning supplement-request-btn" 
                                                            data-document-id="{{ $document->id }}"
                                                            data-member-id="{{ $member->id }}">보완요청</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @if($countryDocument && ($countryDocument->document_name || $countryDocument->submission_deadline))
                                        <tr>
                                            <td>{{ $countryDocument->document_name ?? '-' }}</td>
                                            <td>-</td>
                                            <td>
                                                @if($countryDocument->submission_deadline)
                                                    @if($countryDocument->submission_deadline instanceof \Carbon\Carbon)
                                                        {{ $countryDocument->submission_deadline->format('Y.m.d') }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($countryDocument->submission_deadline)->format('Y.m.d') }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>-</td>
                                            <td>
                                                <span class="status-badge status-not_submitted">Not submitted</span>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="supplement_request[0]" 
                                                       class="board-form-control" 
                                                       placeholder="보완요청 내용" 
                                                       value=""
                                                       style="display: inline-block; width: auto; margin-right: 5px;"
                                                       disabled>
                                                <button type="button" class="btn btn-sm btn-warning" disabled>보완요청</button>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">등록된 서류가 없습니다.</td>
                                        </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="board-form-group" style="margin-bottom: 20px;">
                    <h2 style="font-size: 18px; font-weight: bold; margin: 0; margin-bottom: 15px;">[수정로그]</h2>
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th>수정일시</th>
                                    <th>수정자</th>
                                    <th>수정내용</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($member->modificationLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $log->modifier->name ?? '-' }}</td>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">수정 로그가 없습니다.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary">저장</button>
                    <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary">목록</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="{{ asset('js/backoffice/member-form.js') }}"></script>
<script>
function resetPassword(memberId) {
    if (!confirm('비밀번호를 초기화하시겠습니까? (기본 비밀번호: COS1234)')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("backoffice.members.reset-password", ":id") }}'.replace(':id', memberId);
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}

// 항공권 파일 삭제
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-ticket-file-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('항공권 파일을 삭제하시겠습니까?')) {
                return;
            }
            
            const url = this.getAttribute('data-url');
            const btn = this;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 파일 정보 영역만 제거 (부모 요소는 유지)
                    const fileInfoDiv = btn.closest('div[style*="display: flex"]');
                    if (fileInfoDiv) {
                        fileInfoDiv.remove();
                    }
                    // 페이지 새로고침 없이 파일 입력 필드만 남김
                } else {
                    alert(data.message || '파일 삭제에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('파일 삭제 중 오류가 발생했습니다.');
            });
        });
    });

    // 보완요청 버튼 클릭 이벤트
    const supplementButtons = document.querySelectorAll('.supplement-request-btn');
    
    supplementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.getAttribute('data-document-id');
            const memberId = this.getAttribute('data-member-id');
            const input = document.getElementById('supplement_input_' + documentId);
            const supplementContent = input ? input.value.trim() : '';
            
            if (!supplementContent) {
                alert('보완요청 내용을 입력해주세요.');
                return;
            }
            
            if (!confirm('보완요청을 하시겠습니까?')) {
                return;
            }
            
            const btn = this;
            btn.disabled = true;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            
            fetch('{{ route("backoffice.members.supplement-request", ":member") }}'.replace(':member', memberId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    document_id: documentId,
                    supplement_content: supplementContent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 상태 배지 업데이트
                    const statusBadge = document.getElementById('status_badge_' + documentId);
                    if (statusBadge) {
                        statusBadge.className = 'status-badge status-supplement_requested';
                        statusBadge.textContent = 'Rejection';
                    }
                    // 보완요청 버튼을 완료 처리 버튼으로 변경
                    const input = document.getElementById('supplement_input_' + documentId);
                    if (input) {
                        input.readOnly = true;
                    }
                    btn.className = 'btn btn-sm btn-success complete-request-btn';
                    btn.textContent = '완료 처리';
                    btn.disabled = false;
                    alert('보완요청이 완료되었습니다.');
                } else {
                    alert(data.message || '보완요청에 실패했습니다.');
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('보완요청 중 오류가 발생했습니다.');
                btn.disabled = false;
            });
        });
    });

    // 완료 처리 버튼 클릭 이벤트 (이벤트 위임 사용)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('complete-request-btn')) {
            const button = e.target;
            const documentId = button.getAttribute('data-document-id');
            const memberId = button.getAttribute('data-member-id');
            
            if (!confirm('완료 처리하시겠습니까?')) {
                return;
            }
            
            const btn = button;
            btn.disabled = true;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            
            fetch('{{ route("backoffice.members.complete-request", ":member") }}'.replace(':member', memberId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    document_id: documentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 상태 배지 업데이트
                    const statusBadge = document.getElementById('status_badge_' + documentId);
                    if (statusBadge) {
                        statusBadge.className = 'status-badge status-submitted';
                        statusBadge.textContent = 'Submission';
                    }
                    // 완료 처리 버튼을 보완요청 버튼으로 변경
                    const input = document.getElementById('supplement_input_' + documentId);
                    if (input) {
                        input.readOnly = false;
                        input.value = '';
                    }
                    btn.className = 'btn btn-sm btn-warning supplement-request-btn';
                    btn.textContent = '보완요청';
                    btn.disabled = false;
                    alert('완료 처리되었습니다.');
                } else {
                    alert(data.message || '완료 처리에 실패했습니다.');
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('완료 처리 중 오류가 발생했습니다.');
                btn.disabled = false;
            });
        }
    });
});
</script>
@endsection
