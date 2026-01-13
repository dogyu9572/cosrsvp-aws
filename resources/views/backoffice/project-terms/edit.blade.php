@extends('backoffice.layouts.app')

@section('title', '프로젝트 기수 수정')

@section('content')
@if(session('error'))
    <div class="alert alert-danger hidden-alert">
        {{ session('error') }}
    </div>
@endif

<div class="board-container">
    <div class="board-header">
        <h2>프로젝트 기수 수정</h2>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <form method="POST" action="{{ route('backoffice.project-terms.update', $projectTerm) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">기수명 <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $projectTerm->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $projectTerm->is_active) ? 'checked' : '' }}>
                        사용여부
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">저장</button>
                    <a href="{{ route('backoffice.project-terms.index') }}" class="btn btn-secondary">목록</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
