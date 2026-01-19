@extends('layouts.user')

@section('content')
<div id="mainContent" class="container">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			@if($memberNote)
			<div class="board_view">
				<div class="tit">{{ $memberNote->english_title ?: $memberNote->korean_title }}
					<dl class="date">
						<dt>Submission deadline</dt>
						<dd>{{ $memberNote->created_at->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@if($memberNote->files && $memberNote->files->count() > 0)
				<div class="files">
					@foreach($memberNote->files as $index => $file)
					<a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">별첨파일 {{ $index + 1 }}</a>
					@endforeach
				</div>
				@endif
				<div class="con">
					{!! $memberNote->english_content ?: $memberNote->korean_content !!}
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="javascript:void(0);" onclick="history.back();" class="btn_back">Before</a>
			</div>
			@else
			<div class="board_view">
				<div class="con">
					<p>확인할 Note가 없습니다.</p>
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="javascript:void(0);" onclick="history.back();" class="btn_back">Before</a>
			</div>
			@endif
		</div>
		
	</div>
</div>

@include('components.user-footer')
@endsection
