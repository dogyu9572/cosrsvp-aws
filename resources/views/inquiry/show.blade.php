@php
$gNum = $gNum ?? "07";
$sNum = $sNum ?? "01";
$gName = $gName ?? "Contact Us";
$sName = $sName ?? "Contact Us";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container inquiry_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span><span><strong>{{ $sName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				@if($inquiry->reply_status === 'completed')
					<div class="reply_ico end">Answer completed</div>
				@else
					<div class="reply_ico ing">Awaiting reply</div>
				@endif
				<div class="tit">{{ $inquiry->title }}
					<dl class="date">
						<dt>Registration Date</dt>
						<dd>{{ \Carbon\Carbon::parse($inquiry->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($inquiry->attachments && count($inquiry->attachments) > 0)
					<div class="files">
						@foreach($inquiry->attachments as $attachment)
							<a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['name'] ?? '파일' }}">{{ $attachment['name'] ?? '파일' }}</a>
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! $inquiry->content !!}
				</div>
			</div>
			
			@if($inquiry->reply_status === 'completed' && $inquiry->reply_content)
				<div class="board_view reply_area">
					<div class="tit">답변입니다.
						<dl class="date">
							<dt>등록일</dt>
							<dd>{{ $inquiry->replied_at ? \Carbon\Carbon::parse($inquiry->replied_at)->format('Y.m.d') : '' }}</dd>
						</dl>
					</div>
					@if($inquiry->reply_attachments && count($inquiry->reply_attachments) > 0)
						<div class="files">
							@foreach($inquiry->reply_attachments as $attachment)
								<a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['name'] ?? '파일' }}">{{ $attachment['name'] ?? '파일' }}</a>
							@endforeach
						</div>
					@endif
					<div class="con">
						{!! $inquiry->reply_content !!}
					</div>
				</div>
			@endif
			
			<div class="board_bottom flex_center">
				<a href="{{ route('inquiries') }}" class="btn_back">List</a>
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
