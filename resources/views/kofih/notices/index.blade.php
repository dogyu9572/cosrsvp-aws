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
		
		<div class="wbox">
			<div class="board_top">
				<div class="left">
					<div class="total">Total <strong class="c_red">{{ $notices->total() ?? 0 }}</strong></div>
					<form method="GET" action="{{ route('kofih.notices.index') }}" class="per-page-form-pc" style="display: inline;">
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
					<form method="GET" action="{{ route('kofih.notices.index') }}" class="per-page-form-mo" style="display: inline;">
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
					<form method="GET" action="{{ route('kofih.notices.index') }}" id="searchForm">
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
			
			<div class="tbl board_list">
				<table>
					<colgroup>
						<col class="w90">
						<col>
						<col class="w120">
						<col class="w120">
					</colgroup>
					<thead>
						<tr>
							<th>No.</th>
							<th>제목</th>
							<th>첨부파일</th>
							<th>등록일</th>
						</tr>
					</thead>
					<tbody>
						@php
							$totalNormal = isset($notices) && isset($notices->totalNormal) ? $notices->totalNormal : (isset($notices) ? ($notices->total() - $notices->where('is_notice', true)->count()) : 0);
							$normalIndex = 0;
						@endphp
						@forelse($notices ?? [] as $index => $notice)
							@if($notice->is_notice)
								@php
									$displayNumber = '공지';
								@endphp
							@else
								@php
									$normalIndex++;
									$previousPageNormal = 0;
									if (isset($notices) && $notices->currentPage() > 1) {
										for ($i = 1; $i < $notices->currentPage(); $i++) {
											$previousPageNormal += $notices->getCollection()->slice(($i - 1) * $notices->perPage(), $notices->perPage())->where('is_notice', false)->count();
										}
									}
									$displayNumber = $totalNormal - $previousPageNormal - $normalIndex + 1;
								@endphp
							@endif
							<tr @if($notice->is_notice) class="notice" @endif>
								<td class="num">{{ $displayNumber }}</td>
								<td class="tal tit">
									<a href="{{ route('kofih.notices.show', $notice->id) }}">{{ $notice->title }}</a>
								</td>
								<td class="file">
									@if($notice->attachments && is_array($notice->attachments) && count($notice->attachments) > 0)
										<i></i>
									@endif
								</td>
								<td class="date">{{ \Carbon\Carbon::parse($notice->created_at)->format('Y-m-d') }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="no_data">등록된 공지사항이 없습니다.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<div class="board_bottom">
				<div class="paging">
					@php
						$currentPage = isset($notices) ? $notices->currentPage() : 1;
						$lastPage = isset($notices) ? $notices->lastPage() : 1;
						$startPage = max(1, $currentPage - 2);
						$endPage = min($lastPage, $currentPage + 2);
					@endphp
					
					@if($currentPage == 1)
						<span class="arrow two first" style="pointer-events: none; opacity: 0.5;">처음</span>
						<span class="arrow one prev" style="pointer-events: none; opacity: 0.5;">이전</span>
					@else
						@php
							$queryParams = request()->except('page');
							$firstUrl = $notices->url(1);
							$firstUrl .= !empty($queryParams) ? (str_contains($firstUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							$prevUrl = $notices->previousPageUrl();
							$prevUrl .= !empty($queryParams) ? (str_contains($prevUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
						@endphp
						<a href="{{ $firstUrl }}" class="arrow two first">처음</a>
						<a href="{{ $prevUrl }}" class="arrow one prev">이전</a>
					@endif
					
					@for($page = $startPage; $page <= $endPage; $page++)
						@php
							$queryParams = request()->except('page');
							$pageUrl = isset($notices) ? $notices->url($page) : '#';
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
							$nextUrl = $notices->nextPageUrl();
							$nextUrl .= !empty($queryParams) ? (str_contains($nextUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							$lastUrl = $notices->url($lastPage);
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
