@extends('backoffice.layouts.app')

@section('title', '회원 리스트')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <a href="{{ route('backoffice.members.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 회원생성
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.members.index') }}" class="filter-form" id="filterForm">
                        <!-- 프로젝트 기수 필터 -->
                        <div class="filter-row" style="margin-bottom: 15px;">
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_project_term_id" class="filter-label">기수</label>
                                <select id="filter_project_term_id" name="filter_project_term_id" class="filter-select project-term-filter" data-level="term">
                                    <option value="">전체</option>
                                    @foreach($projectTerms as $term)
                                        <option value="{{ $term->id }}" @selected(request('filter_project_term_id') == $term->id)>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_course_id" class="filter-label">과정</label>
                                <select id="filter_course_id" name="filter_course_id" class="filter-select project-term-filter" data-level="course" data-selected="{{ request('filter_course_id') }}">
                                    <option value="">전체</option>
                                </select>
                            </div>
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_operating_institution_id" class="filter-label">운영기관</label>
                                <select id="filter_operating_institution_id" name="filter_operating_institution_id" class="filter-select project-term-filter" data-level="institution" data-selected="{{ request('filter_operating_institution_id') }}">
                                    <option value="">전체</option>
                                </select>
                            </div>
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_project_period_id" class="filter-label">프로젝트기간</label>
                                <select id="filter_project_period_id" name="filter_project_period_id" class="filter-select project-term-filter" data-level="period" data-selected="{{ request('filter_project_period_id') }}">
                                    <option value="">전체</option>
                                </select>
                            </div>
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_country_id" class="filter-label">국가</label>
                                <select id="filter_country_id" name="filter_country_id" class="filter-select project-term-filter" data-level="country" data-selected="{{ request('filter_country_id') }}">
                                    <option value="">전체</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- 기존 필터 -->
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="join_date_from" class="filter-label">가입일 시작</label>
                                <input type="date" id="join_date_from" name="join_date_from" class="filter-input"
                                    value="{{ request('join_date_from') }}">
                            </div>
                            <div class="filter-group">
                                <label for="join_date_to" class="filter-label">가입일 끝</label>
                                <input type="date" id="join_date_to" name="join_date_to" class="filter-input"
                                    value="{{ request('join_date_to') }}">
                            </div>
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색 구분</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="name" @selected(request('search_type') == 'name')>회원명</option>
                                    <option value="email" @selected(request('search_type') == 'email')>이메일</option>
                                    <option value="phone" @selected(request('search_type') == 'phone')>휴대폰</option>
                                    <option value="passport" @selected(request('search_type') == 'passport')>여권번호</option>
                                    <option value="alien_registration" @selected(request('search_type') == 'alien_registration')>외국인등록번호</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">검색어</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input"
                                    placeholder="검색어를 입력하세요" value="{{ request('keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.members.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> 초기화
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 목록 개수 선택 -->
                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $members->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem;">
                            <label for="mail_list_id" style="font-size: 14px; color: #495057; margin: 0; white-space: nowrap;">메일발송:</label>
                            <select id="mail_list_id" name="mail_list_id" class="per-page-select" style="min-width: 150px;">
                                <option value="">@메일리스트</option>
                                <!-- TODO: 메일리스트 옵션 추가 -->
                            </select>
                            <button type="button" id="send-email-btn" class="btn btn-primary btn-sm">
                                메일전송
                            </button>
                        </div>
                        <form method="GET" action="{{ route('backoffice.members.index') }}" class="per-page-form">
                            <input type="hidden" name="filter_project_term_id" value="{{ request('filter_project_term_id') }}">
                            <input type="hidden" name="filter_course_id" value="{{ request('filter_course_id') }}">
                            <input type="hidden" name="filter_operating_institution_id" value="{{ request('filter_operating_institution_id') }}">
                            <input type="hidden" name="filter_project_period_id" value="{{ request('filter_project_period_id') }}">
                            <input type="hidden" name="filter_country_id" value="{{ request('filter_country_id') }}">
                            <input type="hidden" name="join_date_from" value="{{ request('join_date_from') }}">
                            <input type="hidden" name="join_date_to" value="{{ request('join_date_to') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type') }}">
                            <label for="per_page" class="per-page-label">목록갯수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                                <option value="10" @selected(request('per_page', 10) == 10)>10개</option>
                                <option value="20" @selected(request('per_page', 10) == 20)>20개</option>
                                <option value="50" @selected(request('per_page', 10) == 50)>50개</option>
                                <option value="100" @selected(request('per_page', 10) == 100)>100개</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if($members->count() > 0)
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5 board-checkbox-column">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th class="w5">번호</th>
                                    <th>프로젝트 기수</th>
                                    <th class="w15">회원명</th>
                                    <th>이메일</th>
                                    <th>휴대폰</th>
                                    <th class="w10">가입일</th>
                                    <th class="w10">회원비고</th>
                                    <th class="w10">알림</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $index => $member)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_members[]" value="{{ $member->id }}" class="form-check-input member-checkbox">
                                        </td>
                                        <td>{{ $members->total() - ($members->currentPage() - 1) * $members->perPage() - $loop->index }}</td>
                                        <td>{{ $member->projectTerm->name ?? '-' }}</td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email ?: '-' }}</td>
                                        <td>{{ $member->phone_kr ?: ($member->phone_local ?: '-') }}</td>
                                        <td>{{ $member->created_at->format('Y.m.d') }}</td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.member-notes.index', ['member_id' => $member->id]) }}" class="btn btn-primary btn-sm">
                                                    작성
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.alerts.index', ['member_id' => $member->id]) }}" class="btn btn-primary btn-sm">
                                                    작성
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.members.show', $member) }}" class="btn btn-primary btn-sm">
                                                    상세
                                                </a>
                                                <form action="{{ route('backoffice.members.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('해당 회원정보가 삭제됩니다. 정말 삭제하시겠습니까?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        삭제
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <x-pagination :paginator="$members" />
                @else
                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input" disabled>
                                </th>
                                <th class="w5">번호</th>
                                <th>프로젝트 기수</th>
                                <th class="w15">회원명</th>
                                <th>이메일</th>
                                <th>휴대폰</th>
                                <th class="w10">가입일</th>
                                <th class="w10">회원비고</th>
                                <th class="w10">알림</th>
                                <th class="w10">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center">등록된 회원이 없습니다.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/member-list.js') }}"></script>
<script src="{{ asset('js/backoffice/board-posts-filter.js') }}"></script>
@endsection
