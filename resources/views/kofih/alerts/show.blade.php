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
					{{ $alert->korean_title ?? $alert->english_title ?? '-' }}
					<dl class="date">
						<dt>Registration Date</dt>
						<dd>{{ $alert->created_at->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($alert->files && $alert->files->count() > 0)
					<div class="files">
						@foreach($alert->files as $file)
							<a href="{{ Storage::url($file->file_path) }}" target="_blank">{{ $file->file_name }}</a>
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! nl2br(e(strip_tags($alert->korean_content ?? $alert->english_content ?? ''))) !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('kofih.alerts.index') }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="btn_back">목록</a>
				@if($prevAlert)
					<a href="{{ route('kofih.alerts.show', $prevAlert->id) }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="arrow prev">
						<strong>이전글</strong>
						<p>{{ $prevAlert->korean_title ?? $prevAlert->english_title ?? '-' }}</p>
					</a>
				@else
					<span class="arrow prev" style="pointer-events: none; opacity: 0.5;">
						<strong>이전글</strong>
						<p>-</p>
					</span>
				@endif
				@if($nextAlert)
					<a href="{{ route('kofih.alerts.show', $nextAlert->id) }}{{ request('member_id') ? '?member_id=' . request('member_id') : '' }}" class="arrow next">
						<strong>다음글</strong>
						<p>{{ $nextAlert->korean_title ?? $nextAlert->english_title ?? '-' }}</p>
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