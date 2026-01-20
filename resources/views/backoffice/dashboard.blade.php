@extends('backoffice.layouts.app')

@section('title', $pageTitle ?? '')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-content">
    <!-- 대시보드 헤더 -->
    <div class="dashboard-header">
        <div class="dashboard-welcome">
            <p>{{ auth()->user()->name ?? '관리자' }}님, 환영합니다!</p>
            <p>{{ date('Y년 m월 d일') }} 백오피스 대시보드 현황입니다.</p>
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('backoffice.setting.index') }}" class="dashboard-action-btn">
                <i class="fas fa-cog"></i> 환경설정
            </a>
            <a href="{{ url('/') }}" target="_blank" class="dashboard-action-btn">
                <i class="fas fa-home"></i> 사이트 방문
            </a>
        </div>
    </div>

    <!-- 통계 요약 -->
    <div class="stats-row">
        <div class="stat-card stat-boards">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3>활성 게시판</h3>
                <p class="stat-number">{{ $totalBoards }}</p>
            </div>
        </div>

        <div class="stat-card stat-posts">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3>총 게시글</h3>
                <p class="stat-number">{{ $totalPosts ?? 0 }}</p>
            </div>
        </div>

        <div class="stat-card stat-banners">
            <div class="stat-icon">
                <i class="fas fa-image"></i>
            </div>
            <div class="stat-info">
                <h3>활성 배너</h3>
                <p class="stat-number">{{ $activeBanners ?? 0 }}</p>
            </div>
        </div>

        <div class="stat-card stat-popups">
            <div class="stat-icon">
                <i class="fas fa-window-restore"></i>
            </div>
            <div class="stat-info">
                <h3>활성 팝업</h3>
                <p class="stat-number">{{ $activePopups ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- 데이터 그리드 -->
    <div class="dashboard-grid">
        <!-- 코드 번호 관리 -->
        <div class="grid-item grid-col-12">
            <div class="grid-item-header">
                <h3>코드 번호 관리</h3>
            </div>
            <div class="grid-item-body">
                <div style="padding: 20px;">
                    @if(($accessCodes ?? collect())->isEmpty())
                        <!-- 코드가 없을 때: 등록 폼 -->
                        <div style="max-width: 500px; margin: 0 auto;">
                            <form id="codeAddForm" style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex: 1;">
                                    <label for="newCode" style="display: block; margin-bottom: 5px; font-weight: 600;">코드</label>
                                    <input type="text" id="newCode" name="code" class="form-control" placeholder="코드를 입력하세요" required style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <button type="submit" class="btn btn-primary" style="padding: 8px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    코드 등록
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- 코드가 있을 때: 코드 목록 -->
                        @foreach($accessCodes as $code)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <strong style="font-size: 18px;">{{ $code->code }}</strong>
                                    </div>
                                    <div>
                                        <span class="table-badge badge-{{ $code->is_active ? 'success' : 'secondary' }}">
                                            {{ $code->is_active ? '활성' : '비활성' }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn-edit-code" data-code-id="{{ $code->id }}">
                                    <i class="fas fa-edit"></i> 수정
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- 접속 통계 그래프 -->
    <div class="stats-chart-section">
        <div class="grid-item grid-col-12">
            <div class="grid-item-header">
                <h3>방문객 통계</h3>
                <div class="chart-controls">
                    <button class="chart-type-btn active" data-type="daily">일별</button>
                    <button class="chart-type-btn" data-type="monthly">월별</button>
                </div>
            </div>
            <div class="grid-item-body">
                <div class="visitor-summary">
                    <div class="visitor-stat">
                        <span class="visitor-label">오늘 방문객</span>
                        <span class="visitor-number">{{ $visitorStats['today_visitors'] ?? 0 }}</span>
                    </div>
                    <div class="visitor-stat">
                        <span class="visitor-label">총 방문객</span>
                        <span class="visitor-number">{{ number_format($visitorStats['total_visitors'] ?? 0) }}</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="visitorChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 코드 추가/수정 모달 -->
<div id="codeModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="modalTitle">코드 수정</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="codeForm">
                <input type="hidden" id="codeId" name="id">
                <div class="form-group">
                    <label for="code">코드 <span class="required">*</span></label>
                    <input type="text" id="code" name="code" class="form-control" required>
                    <small class="form-text text-muted">대소문자 구분 없이 입력 가능합니다.</small>
                </div>
                <div class="form-group">
                    <label for="is_active">활성화 여부</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1">활성</option>
                        <option value="0">비활성</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">저장</button>
                    <button type="button" class="btn btn-secondary" id="cancelBtn">취소</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #888;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group .required {
    color: red;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-edit-code {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    background-color: #007bff;
    color: white;
}

.btn-edit-code:hover {
    background-color: #0056b3;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/backoffice/dashboard.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('codeModal');
    const codeForm = document.getElementById('codeForm');
    const codeAddForm = document.getElementById('codeAddForm');
    const cancelBtn = document.getElementById('cancelBtn');
    const closeBtn = document.querySelector('.close');
    const modalTitle = document.getElementById('modalTitle');

    // 코드 등록 폼 제출
    if (codeAddForm) {
        codeAddForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(codeAddForm);
            const data = Object.fromEntries(formData);
            data.is_active = '1';

            fetch('/backoffice/access-codes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    const errorMsg = result.errors ? Object.values(result.errors).flat().join('\n') : result.message;
                    alert(errorMsg || '코드 등록에 실패했습니다.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('코드 등록 중 오류가 발생했습니다.');
            });
        });
    }

    // 모달 열기 (수정)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit-code')) {
            const codeId = e.target.closest('.btn-edit-code').dataset.codeId;
            fetch(`/backoffice/access-codes`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const code = data.data.find(c => c.id == codeId);
                        if (code) {
                            modalTitle.textContent = '코드 수정';
                            document.getElementById('codeId').value = code.id;
                            document.getElementById('code').value = code.code;
                            document.getElementById('is_active').value = code.is_active ? '1' : '0';
                            modal.style.display = 'block';
                        }
                    }
                });
        }
    });

    // 모달 닫기
    function closeModal() {
        modal.style.display = 'none';
        codeForm.reset();
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    window.addEventListener('click', function(e) {
        if (e.target == modal) {
            closeModal();
        }
    });

    // 폼 제출
    codeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const codeId = document.getElementById('codeId').value;
        const formData = new FormData(codeForm);
        const data = Object.fromEntries(formData);
        
        const url = `/backoffice/access-codes/${codeId}`;
        const method = 'PUT';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message || '처리 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('처리 중 오류가 발생했습니다.');
        });
    });
});
</script>
@endsection
