@extends('layouts.kofih')

@section('title', '갤러리')

@php
$gNum = '05';
$gName = '갤러리';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">{{ $gallery->title }}
					<dl class="date">
						<dt>등록일</dt>
						<dd>{{ \Carbon\Carbon::parse($gallery->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($gallery->attachments && is_array($gallery->attachments) && count($gallery->attachments) > 0)
					<div class="files">
						@foreach($gallery->attachments as $attachment)
							@if(isset($attachment['path']) && isset($attachment['name']))
								<a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" download="{{ $attachment['name'] }}">{{ $attachment['name'] }}</a>
							@endif
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! $gallery->content !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('kofih.gallery.index') }}" class="btn_back">목록</a>
			</div>
		</div>
		
	</div>
</div>
@endsection
