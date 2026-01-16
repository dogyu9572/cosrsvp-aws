@extends('backoffice.layouts.app')

@section('title', '메일발송 리스트')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/mails.css') }}">
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
                <a href="{{ route('backoffice.mails.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.mails.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="recipient_type" class="filter-label">발송대상</label>
                                <select id="recipient_type" name="recipient_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="project_term" @selected(request('recipient_type') == 'project_term')>기수별 발송</option>
                                    <option value="address_book" @selected(request('recipient_type') == 'address_book')>주소록발송</option>
                                    <option value="test" @selected(request('recipient_type') == 'test')>테스트 발송</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="search_type" class="filter-label">검색</label>
                                <select id="search_type" name="search_type" class="filter-select">
                                    <option value="">전체</option>
                                    <option value="title" @selected(request('search_type') == 'title')>제목</option>
                                    <option value="dispatch_subject" @selected(request('search_type') == 'dispatch_subject')>발송제목</option>
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
                                    <a href="{{ route('backoffice.mails.index') }}" class="btn btn-secondary">
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
                        <span class="list-count">Total : {{ $mails->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.mails.index') }}" class="per-page-form">
                            <input type="hidden" name="recipient_type" value="{{ request('recipient_type') }}">
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

                @if($mails->count() > 0)
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th>발송대상</th>
                                    <th>제목</th>
                                    <th>발송제목</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mails as $index => $mail)
                                    <tr>
                                        <td>{{ $mails->total() - ($mails->currentPage() - 1) * $mails->perPage() - $loop->index }}</td>
                                        <td>
                                            @if($mail->recipient_type === 'project_term')
                                                기수별 발송
                                            @elseif($mail->recipient_type === 'address_book')
                                                주소록발송
                                            @else
                                                테스트 발송
                                            @endif
                                        </td>
                                        <td>{{ $mail->title }}</td>
                                        <td>{{ $mail->dispatch_subject }}</td>
                                        <td>{{ $mail->created_at->format('Y.m.d') }}</td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.mails.edit', $mail) }}" class="btn btn-primary btn-sm">
                                                    상세
                                                </a>
                                                <form action="{{ route('backoffice.mails.destroy', $mail) }}" method="POST" class="d-inline" onsubmit="return confirm('해당 메일이 삭제됩니다. 정말 삭제하시겠습니까?');">
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
                    <x-pagination :paginator="$mails" />
                @else
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th>발송대상</th>
                                    <th>제목</th>
                                    <th>발송제목</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">등록된 메일이 없습니다.</td>
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
<script src="{{ asset('js/backoffice/mail-form.js') }}"></script>
@endsection
