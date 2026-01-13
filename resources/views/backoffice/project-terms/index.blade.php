@extends('backoffice.layouts.app')

@section('title', '프로젝트 기수 관리')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
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
        <div class="board-card">
            <div class="board-card-body">
                <!-- 검색 및 등록 필터 -->
                <div class="board-filter">
                    <form method="GET" action="{{ route('backoffice.project-terms.index') }}" class="filter-form" id="search-form">
                        <div class="filter-row">
                            <div class="filter-group" style="flex: 1;">
                                <label for="search" class="filter-label">기수명 검색</label>
                                <input type="text" id="search" name="search" class="filter-input"
                                    placeholder="기수명을 입력하세요" value="{{ $search ?? '' }}" style="width: 100%; min-width: 300px;">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> 검색
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="inline-create-form" class="filter-form" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #dee2e6;">
                        <div class="filter-row">
                            <div class="filter-group" style="flex: 1;">
                                <label for="new_term_name" class="filter-label">기수명 등록</label>
                                <input type="text" id="new_term_name" name="name" class="filter-input"
                                    placeholder="새 기수명을 입력하세요" required style="width: 100%; min-width: 300px;">
                            </div>
                            <div class="filter-group">
                                <div class="filter-buttons">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-plus"></i> 등록
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 목록 개수 선택 -->
                <div class="board-list-header">
                    <div class="list-info">
                        <span class="list-count">Total : {{ $terms->total() }}</span>
                    </div>
                    <div class="list-controls">
                        <form method="GET" action="{{ route('backoffice.project-terms.index') }}" class="per-page-form">
                            <input type="hidden" name="search" value="{{ $search ?? '' }}">
                            <label for="per_page" class="per-page-label">표시 개수:</label>
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
                                <th>기수명</th>
                                <th class="w15">등록일</th>
                                <th class="w15">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terms as $term)
                                <tr>
                                    <td>
                                        @php
                                            $termNumber = $terms->total() - ($terms->currentPage() - 1) * $terms->perPage() - $loop->index;
                                        @endphp
                                        {{ $termNumber }}
                                    </td>
                                    <td>{{ $term->name }}</td>
                                    <td>{{ $term->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="board-btn-group">
                                            <a href="{{ route('backoffice.project-terms.show', $term) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> 상세
                                            </a>
                                            <form action="{{ route('backoffice.project-terms.destroy', $term) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('정말 이 기수를 삭제하시겠습니까?');">
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
                                    <td colspan="4" class="text-center">등록된 기수가 없습니다.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <x-pagination :paginator="$terms" />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inlineCreateForm = document.getElementById('inline-create-form');
            const newTermNameInput = document.getElementById('new_term_name');

            if (inlineCreateForm) {
                inlineCreateForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const name = newTermNameInput.value.trim();
                    if (!name) {
                        alert('기수명을 입력하세요.');
                        return;
                    }

                    // 버튼 비활성화
                    const submitBtn = inlineCreateForm.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 등록 중...';

                    // AJAX로 등록 처리
                    fetch('{{ route("backoffice.project-terms.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            name: name,
                            is_active: true
                        })
                    })
                    .then(response => {
                        if (!response.ok && response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(JSON.stringify(data));
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // 성공 시 입력 필드 초기화 및 페이지 새로고침
                        newTermNameInput.value = '';
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        try {
                            const errorData = JSON.parse(error.message);
                            if (errorData.errors) {
                                // 유효성 검사 오류
                                const errorMessage = Object.values(errorData.errors).flat().join('\n');
                                alert(errorMessage);
                            } else if (errorData.message) {
                                alert(errorData.message);
                            } else {
                                alert('기수 등록 중 오류가 발생했습니다.');
                            }
                        } catch (e) {
                            alert('기수 등록 중 오류가 발생했습니다.');
                        }
                        // 버튼 복원
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });
            }
        });
    </script>
@endsection
