@extends('layouts.user')

@section('content')
<div id="mainContent" class="container gallery_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">{{ $gallery->title }}
					<dl class="date">
						<dt>Registration Date</dt>
						<dd>{{ \Carbon\Carbon::parse($gallery->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($gallery->attachments && count($gallery->attachments) > 0)
					<div class="files">
						@foreach($gallery->attachments as $attachment)
							<a href="{{ asset('storage/' . $attachment['path']) }}" download="{{ $attachment['name'] ?? '파일' }}">{{ $attachment['name'] ?? '파일' }}</a>
						@endforeach
					</div>
				@endif
				<div class="con">
					{!! $gallery->content !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('gallery') }}" class="btn_back">List</a>
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
