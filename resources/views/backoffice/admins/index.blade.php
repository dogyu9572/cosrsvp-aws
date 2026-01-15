@extends('backoffice.layouts.app')

@section('title', '관리자 관리')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/common/buttons.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/admins.css') }}">
@endsection

@section('content')
<div class="board-container admins-page">
    <div class="board-page-header">
        <div class="board-page-buttons">
            <button type="button" id="bulk-delete-btn" class="btn btn-danger">
                <i class="fas fa-trash"></i> 선택 삭제
            </button>
            <a href="{{ route('backoffice.admins.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> 새 관리자 추가
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="board-card">
        <div class="board-card-body">
            <!-- 검색 필터 -->
            <div class="admin-filter">
                <form method="GET" action="{{ route('backoffice.admins.index') }}" class="filter-form">
                    <!-- 첫 번째 줄 -->
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="role" class="filter-label">관리자 등급</label>
                            <select id="role" name="role" class="filter-select">
                                <option value="">전체</option>
                                <option value="super_admin" @selected(request('role') == 'super_admin')>총괄관리자</option>
                                <option value="admin" @selected(request('role') == 'admin')>일반관리자</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="is_active" class="filter-label">사용여부</label>
                            <select id="is_active" name="is_active" class="filter-select">
                                <option value="">전체</option>
                                <option value="1" @selected(request('is_active') == '1')>사용</option>
                                <option value="0" @selected(request('is_active') == '0')>미사용</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="search_type" class="filter-label">검색</label>
                            <div class="search-input-wrapper">
                                <select id="search_type" name="search_type" class="filter-select search-type-select">
                                    <option value="">전체</option>
                                    <option value="name" @selected(request('search_type') == 'name')>성명</option>
                                    <option value="department" @selected(request('search_type') == 'department')>부서</option>
                                    <option value="position" @selected(request('search_type') == 'position')>직위</option>
                                    <option value="contact" @selected(request('search_type') == 'contact')>연락처</option>
                                    <option value="email" @selected(request('search_type') == 'email')>이메일</option>
                                </select>
                                <input type="text" id="keyword" name="keyword" class="filter-input search-keyword-input"
                                    placeholder="검색어를 입력하세요" value="{{ request('keyword') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <div class="filter-buttons">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> 검색
                                </button>
                                <a href="{{ route('backoffice.admins.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> 초기화
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($admins->count() > 0)
                <!-- 목록 개수 선택 -->
                <div class="admin-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $admins->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.admins.index') }}" class="per-page-form">
                            @foreach(request()->except('per_page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <label for="per_page" class="per-page-label">목록 개수:</label>
                            <select id="per_page" name="per_page" class="per-page-select" onchange="this.form.submit()">
                                <option value="10" @selected(request('per_page', 10) == 10)>10</option>
                                <option value="20" @selected(request('per_page') == 20)>20</option>
                                <option value="50" @selected(request('per_page') == 50)>50</option>
                                <option value="100" @selected(request('per_page') == 100)>100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>번호</th>
                                <th>관리자 등급</th>
                                <th>ID</th>
                                <th>성명</th>
                                <th>부서</th>
                                <th>직위</th>
                                <th>연락처</th>
                                <th>이메일</th>
                                <th>사용여부</th>
                                <th>등록일</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $index => $admin)
                                <tr>
                                    <td>
                                        @if($admin->role !== 'super_admin')
                                            <input type="checkbox" name="selected_admins[]" value="{{ $admin->id }}" class="form-check-input admin-checkbox">
                                        @endif
                                    </td>
                                    <td>{{ $admins->total() - ($admins->currentPage() - 1) * $admins->perPage() - $loop->index }}</td>
                                    <td>
                                        @if($admin->role === 'super_admin')
                                            총괄관리자
                                        @else
                                            일반관리자
                                        @endif
                                    </td>
                                    <td>{{ $admin->login_id ?: '-' }}</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->department ?: '-' }}</td>
                                    <td>{{ $admin->position ?: '-' }}</td>
                                    <td>{{ $admin->contact ?: '-' }}</td>
                                    <td>{{ $admin->email ?: '-' }}</td>
                                    <td>
                                        <span class="status-badge {{ $admin->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $admin->is_active ? '사용' : '미사용' }}
                                        </span>
                                    </td>
                                    <td>{{ $admin->created_at->format('Y.m.d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.admins.edit', $admin) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> 수정
                                            </a>
                                            @if($admin->role !== 'super_admin')
                                                <form action="{{ route('backoffice.admins.destroy', $admin) }}" method="POST" class="d-inline" onsubmit="return confirm('이 관리자를 삭제하시겠습니까?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> 삭제
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$admins" />
            @else
                <div class="table-responsive">
                    <table class="board-table">
                        <thead>
                            <tr>
                                <th class="w5 board-checkbox-column">
                                    <input type="checkbox" id="select-all" class="form-check-input" disabled>
                                </th>
                                <th>번호</th>
                                <th>관리자 등급</th>
                                <th>ID</th>
                                <th>성명</th>
                                <th>부서</th>
                                <th>직위</th>
                                <th>연락처</th>
                                <th>이메일</th>
                                <th>사용여부</th>
                                <th>등록일</th>
                                <th>관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="12" class="text-center">등록된 관리자가 없습니다.</td>
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
<script src="{{ asset('js/backoffice/admins.js') }}"></script>
@endsection
