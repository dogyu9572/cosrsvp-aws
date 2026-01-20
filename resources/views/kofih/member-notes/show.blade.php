@extends('layouts.kofih')

@section('title', '회원')

@php
$gNum = '01';
$gName = '회원';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">
					{{ $note->korean_title ?? $note->english_title ?? '-' }}
					<dl class="date">
						<dt>등록일</dt>
						<dd>{{ $note->created_at->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($note->files && $note->files->count() > 0)
					<div class="files">
						@foreach($note->files as $file)
							<a href="{{ Storage::url($file->file_path) }}" target="_blank">{{ $file->file_name }}</a>
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! nl2br(e(strip_tags($note->korean_content ?? $note->english_content ?? ''))) !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('kofih.member-notes.index') }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="btn_back">목록</a>
				@if($prevNote)
					<a href="{{ route('kofih.member-notes.show', $prevNote->id) }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="arrow prev">
						<strong>이전글</strong>
						<p>{{ $prevNote->korean_title ?? $prevNote->english_title ?? '-' }}</p>
					</a>
				@else
					<span class="arrow prev" style="pointer-events: none; opacity: 0.5;">
						<strong>이전글</strong>
						<p>-</p>
					</span>
				@endif
				@if($nextNote)
					<a href="{{ route('kofih.member-notes.show', $nextNote->id) }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="arrow next">
						<strong>다음글</strong>
						<p>{{ $nextNote->korean_title ?? $nextNote->english_title ?? '-' }}</p>
					</a>
				@else
					<span class="arrow next" style="pointer-events: none; opacity: 0.5;">
						<strong>다음글</strong>
						<p>-</p>
					</span>
				@endif
			</div>
		</div>
		
	</div>
</div>
@endsection