@extends('backoffice.layouts.app')

@section('title', $board->name ?? '게시판')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backoffice/sorting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/modal.css') }}">
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success board-hidden-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="board-container">
        <div class="board-page-header">
            <div class="board-page-buttons">
                <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                    <i class="fas fa-trash"></i> 선택 삭제
                </button>
                <a href="{{ route('backoffice.board-posts.create', $board->slug ?? 'schedules') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 등록
                </a>              
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'schedules') }}" class="filter-form" id="filterForm">
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
                        
                        <!-- 기존 필터 -->
                        <div class="filter-row">
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
                                <label for="schedule_start_date" class="filter-label">일정 시작</label>
                                <input type="date" id="schedule_start_date" name="schedule_start_date" class="filter-input"
                                    value="{{ request('schedule_start_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="schedule_end_date" class="filter-label">일정 끝</label>
                                <input type="date" id="schedule_end_date" name="schedule_end_date" class="filter-input"
                                    value="{{ request('schedule_end_date') }}">
                            </div>
                            <div class="filter-group">
                                <label for="filter_status" class="filter-label">진행상황</label>
                                <select id="filter_status" name="filter_status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="scheduled" @selected(request('filter_status') == 'scheduled')>진행예정</option>
                                    <option value="in_progress" @selected(request('filter_status') == 'in_progress')>진행중</option>
                                    <option value="closed" @selected(request('filter_status') == 'closed')>종료</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색 구분</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="title" @selected(request('search_type') == 'title')>제목</option>
                                    <option value="content" @selected(request('search_type') == 'content')>내용</option>
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
                                    <a href="{{ route('backoffice.board-posts.index', $board->slug ?? 'schedules') }}"
                                        class="btn btn-secondary">
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
                        <span class="list-count">Total : {{ $posts->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.board-posts.index', $board->slug ?? 'schedules') }}" class="per-page-form">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="schedule_start_date" value="{{ request('schedule_start_date') }}">
                            <input type="hidden" name="schedule_end_date" value="{{ request('schedule_end_date') }}">
                            <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                            <input type="hidden" name="search_type" value="{{ request('search_type') }}">
                            <input type="hidden" name="filter_project_term_id" value="{{ request('filter_project_term_id') }}">
                            <input type="hidden" name="filter_course_id" value="{{ request('filter_course_id') }}">
                            <input type="hidden" name="filter_operating_institution_id" value="{{ request('filter_operating_institution_id') }}">
                            <input type="hidden" name="filter_project_period_id" value="{{ request('filter_project_period_id') }}">
                            <input type="hidden" name="filter_country_id" value="{{ request('filter_country_id') }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
                            <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                                <option value="10" @selected(request('per_page', 15) == 10)>10개</option>
                                <option value="20" @selected(request('per_page', 15) == 20)>20개</option>
                                <option value="50" @selected(request('per_page', 15) == 50)>50개</option>
                                <option value="100" @selected(request('per_page', 15) == 100)>100개</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="board-table {{ $board->enable_sorting ? 'sortable-table' : '' }}">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                @if($board->enable_sorting)
                                    <th class="w5">순서</th>
                                @endif
                                <th class="w5">번호</th>
                                <th>프로젝트 기수</th>
                                <th class="w10">진행상황</th>
                                <th>제목</th>
                                <th class="w15">일정</th>
                                <th class="w10">등록일</th>
                                <th class="w10">관리</th>
                            </tr>
                        </thead>
                        <tbody @if($board->enable_sorting) id="sortable-tbody" @endif>
                            @forelse($posts as $post)
                                <tr @if($board->enable_sorting) data-post-id="{{ $post->id }}" @endif>
                                    <td>
                                        <input type="checkbox" name="selected_posts[]" value="{{ $post->id }}" class="form-check-input post-checkbox">
                                    </td>
                                    @if($board->enable_sorting)
                                        <td class="sort-handle-cell">
                                            <i class="fas fa-grip-vertical sort-handle" title="드래그하여 순서 변경"></i>
                                        </td>
                                    @endif
                                    <td>
                                        @if ($post->is_notice)
                                            <span class="board-notice-badge">공지</span>
                                        @else
                                            @php
                                                $postNumber = $posts->total() - ($posts->currentPage() - 1) * $posts->perPage() - $loop->index;
                                            @endphp
                                            {{ $postNumber }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $post->project_term_display_text ?? '전체' }}
                                    </td>
                                    <td>
                                        @php
                                            // 진행상황 계산 (일정 날짜 기준)
                                            $status = 'scheduled';
                                            $customFields = $post->custom_fields ? json_decode($post->custom_fields, true) : [];
                                            $displayDateField = $customFields['display_date'] ?? $customFields['display_date_range'] ?? null;
                                            
                                            if ($displayDateField) {
                                                $displayDateData = is_string($displayDateField) ? json_decode($displayDateField, true) : $displayDateField;
                                                if (is_array($displayDateData) && !empty($displayDateData['start_date']) && !empty($displayDateData['end_date'])) {
                                                    try {
                                                        $startDate = \Carbon\Carbon::parse($displayDateData['start_date']);
                                                        $endDate = \Carbon\Carbon::parse($displayDateData['end_date']);
                                                        $now = \Carbon\Carbon::now();
                                                        
                                                        if ($now < $startDate) {
                                                            $status = 'scheduled';
                                                        } elseif ($now >= $startDate && $now <= $endDate) {
                                                            $status = 'in_progress';
                                                        } else {
                                                            $status = 'closed';
                                                        }
                                                    } catch (\Exception $e) {
                                                        // 파싱 오류 시 기본값 사용
                                                    }
                                                }
                                            }
                                            
                                            $statusLabels = ['scheduled' => '진행예정', 'in_progress' => '진행중', 'closed' => '종료'];
                                            $statusColors = ['scheduled' => 'badge-secondary', 'in_progress' => 'badge-primary', 'closed' => 'badge-danger'];
                                        @endphp
                                        <span class="badge {{ $statusColors[$status] ?? 'badge-secondary' }}">
                                            {{ $statusLabels[$status] ?? '진행예정' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $post->title }}
                                    </td>
                                    <td>
                                        {{ $post->display_date_text ?? '' }}
                                    </td>
                                    <td>{{ $post->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.board-posts.edit', [$board->slug ?? 'schedules', $post->id]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            <form
                                                action="{{ route('backoffice.board-posts.destroy', [$board->slug ?? 'schedules', $post->id]) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('정말 이 게시글을 삭제하시겠습니까?');">
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
                                    <td colspan="{{ $board->enable_sorting ? '9' : '8' }}" class="text-center">등록된 게시글이 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$posts" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backoffice/board-posts.js') }}"></script>
    <script src="{{ asset('js/backoffice/board-posts-filter.js') }}"></script>
    @if($board->enable_sorting)
        <script src="{{ asset('js/backoffice/sorting.js') }}"></script>
    @endif
@endsection
