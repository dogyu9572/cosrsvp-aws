@extends('layouts.user')

@section('content')
<div id="mainContent" class="container">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			@if($referenceMaterial)
			<div class="board_view">
				<div class="tit">{{ $referenceMaterial->title }}
					<dl class="date">
						<dt>Registration Date</dt>
						<dd>{{ \Carbon\Carbon::parse($referenceMaterial->created_at)->format('Y.m.d') }}</dd>
					</dl>
				</div>
				@php
					$attachments = [];
					if ($referenceMaterial->attachments) {
						$attachments = json_decode($referenceMaterial->attachments, true);
						if (!is_array($attachments)) {
							$attachments = [];
						}
					}
				@endphp
				@if(!empty($attachments))
				<div class="files">
					@foreach($attachments as $index => $attachment)
					<a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank">별첨파일 {{ $index + 1 }}</a>
					@endforeach
				</div>
				@endif
				<div class="con">
					{!! $referenceMaterial->content !!}
				</div>
			</div>
			@else
			<div class="board_view">
				<div class="con">
					<p>확인할 참고자료가 없습니다.</p>
				</div>
			</div>
			@endif
			
			<div class="board_bottom flex_center">
				<a href="javascript:void(0);" onclick="history.back();" class="btn_back">Before</a>
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')
@endsection
