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
		
		<div class="wbox">
			<div class="board_top">
				<div class="left">
					<div class="total">Total <strong class="c_red">{{ $galleries->total() ?? 0 }}</strong></div>
					<form method="GET" action="{{ route('kofih.gallery.index') }}" class="per-page-form-pc" style="display: inline;">
						@foreach(request()->except('per_page') as $key => $value)
							<input type="hidden" name="{{ $key }}" value="{{ $value }}">
						@endforeach
						<select name="per_page" id="per_page_pc" class="pc_vw" onchange="this.form.submit()">
							<option value="10" @selected(request('per_page', 10) == 10)>10개씩 보기</option>
							<option value="20" @selected(request('per_page', 10) == 20)>20개씩 보기</option>
							<option value="30" @selected(request('per_page', 10) == 30)>30개씩 보기</option>
							<option value="50" @selected(request('per_page', 10) == 50)>50개씩 보기</option>
						</select>
					</form>
				</div>
				<div class="right select_area">
					<form method="GET" action="{{ route('kofih.gallery.index') }}" class="per-page-form-mo" style="display: inline;">
						@foreach(request()->except('per_page') as $key => $value)
							<input type="hidden" name="{{ $key }}" value="{{ $value }}">
						@endforeach
						<select name="per_page" id="per_page_mo" class="mo_vw" onchange="this.form.submit()">
							<option value="10" @selected(request('per_page', 10) == 10)>10개씩 보기</option>
							<option value="20" @selected(request('per_page', 10) == 20)>20개씩 보기</option>
							<option value="30" @selected(request('per_page', 10) == 30)>30개씩 보기</option>
							<option value="50" @selected(request('per_page', 10) == 50)>50개씩 보기</option>
						</select>
					</form>
					<form method="GET" action="{{ route('kofih.gallery.index') }}" id="searchForm">
						@foreach(request()->except(['keyword', 'per_page']) as $key => $value)
							<input type="hidden" name="{{ $key }}" value="{{ $value }}">
						@endforeach
						<select name="" id="">
							<option value="">전체</option>
						</select>
						<input type="text" name="keyword" class="text" placeholder="검색어를 입력해주세요." value="{{ request('keyword') }}">
						<button type="submit" class="btn">조회</button>
					</form>
				</div>
			</div>
			
			<ul class="gall_list">
				@forelse($galleries ?? [] as $gallery)
					<li>
						<a href="{{ route('kofih.gallery.show', $gallery->id) }}">
							<span class="imgfit">
								@if($gallery->thumbnail)
									<img src="{{ asset('storage/' . $gallery->thumbnail) }}" alt="{{ $gallery->title }}">
								@else
									<img src="/pub/images/img_gallery_sample.jpg" alt="{{ $gallery->title }}">
								@endif
							</span>
							<span class="txt">
								<span class="tit">{{ $gallery->title }}</span>
								<span class="date">{{ \Carbon\Carbon::parse($gallery->created_at)->format('Y.m.d') }}</span>
							</span>
						</a>
					</li>
				@empty
					<li class="no_data">등록된 갤러리가 없습니다.</li>
				@endforelse
			</ul>

			<div class="board_bottom">
				<div class="paging">
					@php
						$currentPage = isset($galleries) ? $galleries->currentPage() : 1;
						$lastPage = isset($galleries) ? $galleries->lastPage() : 1;
						$startPage = max(1, $currentPage - 2);
						$endPage = min($lastPage, $currentPage + 2);
					@endphp
					
					@if($currentPage == 1)
						<span class="arrow two first" style="pointer-events: none; opacity: 0.5;">처음</span>
						<span class="arrow one prev" style="pointer-events: none; opacity: 0.5;">이전</span>
					@else
						@php
							$queryParams = request()->except('page');
							$firstUrl = $galleries->url(1);
							$firstUrl .= !empty($queryParams) ? (str_contains($firstUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							$prevUrl = $galleries->previousPageUrl();
							$prevUrl .= !empty($queryParams) ? (str_contains($prevUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
						@endphp
						<a href="{{ $firstUrl }}" class="arrow two first">처음</a>
						<a href="{{ $prevUrl }}" class="arrow one prev">이전</a>
					@endif
					
					@for($page = $startPage; $page <= $endPage; $page++)
						@php
							$queryParams = request()->except('page');
							$pageUrl = isset($galleries) ? $galleries->url($page) : '#';
							$pageUrl .= !empty($queryParams) ? (str_contains($pageUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
						@endphp
						@if($page == $currentPage)
							<a href="{{ $pageUrl }}" class="on">{{ $page }}</a>
						@else
							<a href="{{ $pageUrl }}">{{ $page }}</a>
						@endif
					@endfor
					
					@if($currentPage >= $lastPage)
						<span class="arrow one next" style="pointer-events: none; opacity: 0.5;">다음</span>
						<span class="arrow two last" style="pointer-events: none; opacity: 0.5;">맨끝</span>
					@else
						@php
							$queryParams = request()->except('page');
							$nextUrl = $galleries->nextPageUrl();
							$nextUrl .= !empty($queryParams) ? (str_contains($nextUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							$lastUrl = $galleries->url($lastPage);
							$lastUrl .= !empty($queryParams) ? (str_contains($lastUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
						@endphp
						<a href="{{ $nextUrl }}" class="arrow one next">다음</a>
						<a href="{{ $lastUrl }}" class="arrow two last">맨끝</a>
					@endif
				</div>
			</div>
		</div>
		
	</div>
</div>
@endsection
