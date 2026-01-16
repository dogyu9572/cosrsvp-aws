@extends('layouts.user')

@section('content')
<div id="mainContent" class="container notice_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">{{ $notice->title }}
					<dl class="date">
						<dt>Date</dt>
						<dd>{{ \Carbon\Carbon::parse($notice->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($notice->attachments && count($notice->attachments) > 0)
					<div class="files">
						@foreach($notice->attachments as $attachment)
							<a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['name'] ?? '파일' }}">{{ $attachment['name'] ?? '파일' }}</a>
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! $notice->content !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('notices') }}" class="btn_back">List</a>
				@if($prevNext['prev'])
					<a href="{{ route('notices.show', $prevNext['prev']->id) }}" class="arrow prev"><strong>Prev</strong><p>{{ $prevNext['prev']->title }}</p></a>
				@else
					<span class="arrow prev"><strong>Prev</strong><p>No previous post</p></span>
				@endif
				@if($prevNext['next'])
					<a href="{{ route('notices.show', $prevNext['next']->id) }}" class="arrow next"><strong>Next</strong><p>{{ $prevNext['next']->title }}</p></a>
				@else
					<span class="arrow next"><strong>Next</strong><p>No next post</p></span>
				@endif
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
