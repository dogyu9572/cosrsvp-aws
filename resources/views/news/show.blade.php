@extends('layouts.user')

@section('content')
<div id="mainContent" class="container news_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="board_view">
				<div class="tit">{{ $newsItem['title'] }}
					<dl class="date">
						<dt>Registration Date</dt>
						<dd>{{ $newsItem['date'] }}</dd>
					</dl>
				</div>
				<div class="con">
					@if(!empty($newsItem['description']))
						<div style="color: #333; line-height: 1.8; font-size: 15px; word-wrap: break-word;">
							{{ $newsItem['description'] }}
							@if(!empty($newsItem['link']) || !empty($newsItem['originallink']))
								<a href="{{ $newsItem['originallink'] ?? $newsItem['link'] }}" target="_blank" rel="noopener noreferrer" style="color: #0066cc; text-decoration: underline; margin-left: 5px;">
									원문 보기 →
								</a>
							@endif
						</div>
					@elseif(!empty($newsItem['link']) || !empty($newsItem['originallink']))
						<div style="color: #333; line-height: 1.8; font-size: 15px;">
							<a href="{{ $newsItem['originallink'] ?? $newsItem['link'] }}" target="_blank" rel="noopener noreferrer" style="color: #0066cc; text-decoration: underline;">
								원문 보기 →
							</a>
						</div>
					@endif
				</div>
			</div>
			<div class="board_bottom flex_center">
				<a href="{{ route('news', ['category' => $category]) }}" class="btn_back">List</a>
				@if($prev)
					<a href="{{ route('news.show', ['id' => $prev['id'], 'category' => $category]) }}" class="arrow prev"><strong>Prev</strong><p>{{ $prev['title'] }}</p></a>
				@else
					<span class="arrow prev"><strong>Prev</strong><p>No previous post</p></span>
				@endif
				@if($next)
					<a href="{{ route('news.show', ['id' => $next['id'], 'category' => $category]) }}" class="arrow next"><strong>Next</strong><p>{{ $next['title'] }}</p></a>
				@else
					<span class="arrow next"><strong>Next</strong><p>No next post</p></span>
				@endif
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
