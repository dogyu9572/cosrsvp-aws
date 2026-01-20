@extends('layouts.kofih')

@section('title', '공지사항')

@php
$gNum = '03';
$gName = '공지사항';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">
					{{ $notice->title }}
					<dl class="date">
						<dt>등록일</dt>
						<dd>{{ \Carbon\Carbon::parse($notice->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($notice->attachments && is_array($notice->attachments) && count($notice->attachments) > 0)
					<div class="files">
						@foreach($notice->attachments as $attachment)
							@if(isset($attachment['path']) && isset($attachment['name']))
								<a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank">{{ $attachment['name'] }}</a>
							@endif
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! nl2br(e(strip_tags($notice->content))) !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('kofih.notices.index') }}" class="btn_back">목록</a>
				@if($prevNotice)
					@php
						$prevCustomFields = json_decode($prevNotice->custom_fields ?? '{}', true);
						if (!is_array($prevCustomFields)) {
							$prevCustomFields = [];
						}
						$prevTitle = $prevNotice->title;
						if (isset($prevCustomFields['title_en']) && !$prevTitle) {
							$prevTitle = $prevCustomFields['title_en'];
						}
					@endphp
					<a href="{{ route('kofih.notices.show', $prevNotice->id) }}" class="arrow prev">
						<strong>이전글</strong>
						<p>{{ $prevTitle }}</p>
					</a>
				@else
					<span class="arrow prev" style="pointer-events: none; opacity: 0.5;">
						<strong>이전글</strong>
						<p>-</p>
					</span>
				@endif
				@if($nextNotice)
					@php
						$nextCustomFields = json_decode($nextNotice->custom_fields ?? '{}', true);
						if (!is_array($nextCustomFields)) {
							$nextCustomFields = [];
						}
						$nextTitle = $nextNotice->title;
						if (isset($nextCustomFields['title_en']) && !$nextTitle) {
							$nextTitle = $nextCustomFields['title_en'];
						}
					@endphp
					<a href="{{ route('kofih.notices.show', $nextNotice->id) }}" class="arrow next">
						<strong>다음글</strong>
						<p>{{ $nextTitle }}</p>
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
