@extends('backoffice.layouts.app')

@section('title', '회원비고 리스트')

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
                <a href="{{ route('backoffice.member-notes.create', request('member_id') ? ['member_id' => request('member_id')] : []) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.member-notes.index') }}" class="filter-form" id="filterForm">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="member_id" class="filter-label">회원</label>
                                <select id="member_id" name="member_id" class="filter-select">
                                    <option value="">전체</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" @selected(request('member_id') == $member->id)>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="status" class="filter-label">상태</label>
                                <select id="status" name="status" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="normal" @selected(request('status') == 'normal')>일반</option>
                                    <option value="urgent" @selected(request('status') == 'urgent')>긴급</option>
                                    <option value="caution" @selected(request('status') == 'caution')>주의</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="registration_date_from" class="filter-label">등록일 시작</label>
                                <input type="date" id="registration_date_from" name="registration_date_from" class="filter-input"
                                    value="{{ request('registration_date_from') }}">
                            </div>
                            <div class="filter-group">
                                <label for="registration_date_to" class="filter-label">등록일 끝</label>
                                <input type="date" id="registration_date_to" name="registration_date_to" class="filter-input"
                                    value="{{ request('registration_date_to') }}">
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
                                    <a href="{{ route('backoffice.member-notes.index', request()->only('member_id')) }}" class="btn btn-secondary">
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
                        <span class="list-count">Total : {{ $memberNotes->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.member-notes.index') }}" class="per-page-form">
                            <input type="hidden" name="member_id" value="{{ request('member_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="registration_date_from" value="{{ request('registration_date_from') }}">
                            <input type="hidden" name="registration_date_to" value="{{ request('registration_date_to') }}">
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

                @if($memberNotes->count() > 0)
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th class="w15">회원명</th>
                                    <th class="w10">상태</th>
                                    <th>제목</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberNotes as $index => $note)
                                    <tr>
                                        <td>{{ $memberNotes->total() - ($memberNotes->currentPage() - 1) * $memberNotes->perPage() - $loop->index }}</td>
                                        <td>{{ $note->member->name ?? '-' }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $note->status }}">
                                                @if($note->status == 'normal')
                                                    일반
                                                @elseif($note->status == 'urgent')
                                                    긴급
                                                @elseif($note->status == 'caution')
                                                    주의
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $note->korean_title }}</td>
                                        <td>{{ $note->created_at->format('Y.m.d') }}</td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.member-notes.edit', $note) }}" class="btn btn-primary btn-sm">
                                                    상세
                                                </a>
                                                <form action="{{ route('backoffice.member-notes.destroy', $note) }}" method="POST" class="d-inline" onsubmit="return confirm('해당 회원비고가 삭제됩니다. 정말 삭제하시겠습니까?');">
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
                    <x-pagination :paginator="$memberNotes" />
                @else
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th class="w15">회원명</th>
                                    <th class="w10">상태</th>
                                    <th>제목</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">등록된 회원비고가 없습니다.</td>
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
<script src="{{ asset('js/backoffice/member-note-form.js') }}"></script>
@endsection
