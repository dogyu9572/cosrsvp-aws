@extends('backoffice.layouts.app')

@section('title', '문의 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/inquiries.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container inquiries-index">
        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.inquiries.index') }}" class="filter-form" id="filterForm">
                        <!-- 프로젝트 기수 필터 -->
                        <div class="filter-row" style="margin-bottom: 15px;">
                            <div class="filter-group" style="flex: 1; min-width: 150px;">
                                <label for="filter_project_term_id" class="filter-label">기수</label>
                                <select id="filter_project_term_id" name="filter_project_term_id" class="filter-select project-term-filter" data-level="term">
                                    <option value="">전체</option>
                                    @foreach(\App\Models\ProjectTerm::active()->orderBy('created_at', 'desc')->get() as $term)
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
                        
                        <!-- 등록일, 답변일, 답변여부 필터 -->
                        <div class="filter-row" style="margin-bottom: 15px;">
                            <div class="filter-group">
                                <label for="start_date" class="filter-label">등록일 시작</label>
                                <input type="date" id="start_date" name="start_date" class="filter-input"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="end_date" class="filter-label">등록일 끝</label>
                                <input type="date" id="end_date" name="end_date" class="filter-input"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="reply_start_date" class="filter-label">답변일 시작</label>
                                <input type="date" id="reply_start_date" name="reply_start_date" class="filter-input"
                                    value="{{ request('reply_start_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="reply_end_date" class="filter-label">답변일 끝</label>
                                <input type="date" id="reply_end_date" name="reply_end_date" class="filter-input"
                                    value="{{ request('reply_end_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="reply_status" class="filter-label">답변여부</label>
                                <select id="reply_status" name="reply_status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="pending" @selected(request('reply_status') == 'pending')>미완료</option>
                                    <option value="completed" @selected(request('reply_status') == 'completed')>완료</option>
                                </select>
                            </div>
                        </div>

                        <!-- 검색 필터 -->
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색 구분</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="title" @selected(request('search_type') == 'title')>제목</option>
                                    <option value="content" @selected(request('search_type') == 'content')>내용</option>
                                    <option value="author" @selected(request('search_type') == 'author')>작성자</option>
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
                                    <a href="{{ route('backoffice.inquiries.index') }}" class="btn btn-secondary">
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
                        <span class="list-count">Total : {{ $inquiries->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.inquiries.index') }}" class="per-page-form">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="reply_start_date" value="{{ request('reply_start_date') }}">
                            <input type="hidden" name="reply_end_date" value="{{ request('reply_end_date') }}">
                            <input type="hidden" name="reply_status" value="{{ request('reply_status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type') }}">
                            <input type="hidden" name="filter_project_term_id" value="{{ request('filter_project_term_id') }}">
                            <input type="hidden" name="filter_course_id" value="{{ request('filter_course_id') }}">
                            <input type="hidden" name="filter_operating_institution_id" value="{{ request('filter_operating_institution_id') }}">
                            <input type="hidden" name="filter_project_period_id" value="{{ request('filter_project_period_id') }}">
                            <input type="hidden" name="filter_country_id" value="{{ request('filter_country_id') }}">
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

                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5">번호</th>
                                <th>프로젝트 기수</th>
                                <th class="w10">답변여부</th>
                                <th>제목</th>
                                <th class="w10">작성자</th>
                                <th class="w10">등록일</th>
                                <th class="w10">답변일</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inquiries as $inquiry)
                                <tr>
                                    <td>
                                        @php
                                            $inquiryNumber = $inquiries->total() - ($inquiries->currentPage() - 1) * $inquiries->perPage() - $loop->index;
                                        @endphp
                                        {{ $inquiryNumber }}
                                    </td>
                                    <td>
                                        {{ $inquiry->project_term_display_text ?? '전체' }}
                                    </td>
                                    <td>
                                        @if($inquiry->reply_status === 'completed')
                                            <span class="badge badge-success">완료</span>
                                        @else
                                            <span class="badge badge-warning">미완료</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('backoffice.inquiries.show', $inquiry->id) }}">
                                            {{ $inquiry->title }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $inquiry->user->name ?? '알 수 없음' }}
                                    </td>
                                    <td>{{ $inquiry->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        @if($inquiry->replied_at)
                                            {{ $inquiry->replied_at->format('Y.m.d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.inquiries.show', $inquiry->id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> 상세
                                            </a>
                                            <form
                                                action="{{ route('backoffice.inquiries.destroy', $inquiry->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('해당 문의가 삭제됩니다. 정말 삭제하시겠습니까?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> 삭제
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">등록된 문의가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$inquiries" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-posts-filter.js') }}"></script>
@endsection
