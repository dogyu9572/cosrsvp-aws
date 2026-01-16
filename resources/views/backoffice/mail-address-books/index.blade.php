@extends('backoffice.layouts.app')

@section('title', '주소록 리스트')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/mail-address-books.css') }}">
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
                <a href="{{ route('backoffice.mail-address-books.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> 등록
                </a>
            </div>
        </div>

        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.mail-address-books.index') }}" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="keyword" class="filter-label">주소록명 검색</label>
                                <input type="text" id="keyword" name="keyword" class="filter-input"
                                    placeholder="주소록명을 입력하세요" value="{{ request('keyword') }}">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                    <a href="{{ route('backoffice.mail-address-books.index') }}" class="btn btn-secondary">
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
                        <span class="list-count">Total : {{ $addressBooks->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.mail-address-books.index') }}" class="per-page-form">
                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">
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

                @if($addressBooks->count() > 0)
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th>주소록명</th>
                                    <th class="w10">등록인원</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($addressBooks as $index => $addressBook)
                                    <tr>
                                        <td>{{ $addressBooks->total() - ($addressBooks->currentPage() - 1) * $addressBooks->perPage() - $loop->index }}</td>
                                        <td>{{ $addressBook->name }}</td>
                                        <td>{{ $addressBook->total_recipients_count }}명</td>
                                        <td>{{ $addressBook->created_at->format('Y.m.d') }}</td>
                                        <td>
                                            <div class="board-btn-group">
                                                <a href="{{ route('backoffice.mail-address-books.edit', $addressBook) }}" class="btn btn-primary btn-sm">
                                                    상세
                                                </a>
                                                <form action="{{ route('backoffice.mail-address-books.destroy', $addressBook) }}" method="POST" class="d-inline" onsubmit="return confirm('해당 주소록이 삭제됩니다. 정말 삭제하시겠습니까?');">
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
                    <x-pagination :paginator="$addressBooks" />
                @else
                    <div class="table-responsive">
                        <table class="board-table">
                            <thead>
                                <tr>
                                    <th class="w5">번호</th>
                                    <th>주소록명</th>
                                    <th class="w10">등록인원</th>
                                    <th class="w10">등록일</th>
                                    <th class="w10">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">등록된 주소록이 없습니다.</td>
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
<script src="{{ asset('js/backoffice/mail-address-book-form.js') }}"></script>
@endsection
