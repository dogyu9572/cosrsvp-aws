@extends('layouts.kofih')

@section('title', '회원')

@php
$gNum = '01';
$gName = '회원';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
			<div class="board_top">
				<div class="left">
					<div class="total">Total <strong class="c_red">{{ $memberNotes->total() ?? 0 }}</strong></div>
					<form method="GET" action="{{ route('kofih.member-notes.index') }}" class="per-page-form-pc" style="display: inline;">
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
					<form method="GET" action="{{ route('kofih.member-notes.index') }}" class="per-page-form-mo" style="display: inline;">
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
					<form method="GET" action="{{ route('kofih.member-notes.index') }}" id="searchForm">
						@foreach(request()->except(['search_type', 'keyword', 'per_page']) as $key => $value)
							<input type="hidden" name="{{ $key }}" value="{{ $value }}">
						@endforeach
						<select name="search_type" id="search_type">
							<option value="">전체</option>
							<option value="title" @selected(request('search_type') == 'title')>제목</option>
							<option value="content" @selected(request('search_type') == 'content')>내용</option>
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
						@forelse($memberNotes ?? [] as $index => $note)
							<tr>
								<td class="num">{{ $memberNotes->total() - ($memberNotes->currentPage() - 1) * $memberNotes->perPage() - $loop->index }}</td>
								<td class="tal tit">
									<a href="{{ route('kofih.member-notes.show', $note->id) }}">{{ $note->korean_title ?? $note->english_title ?? '-' }}</a>
								</td>
								<td class="file">
									@if($note->files && $note->files->count() > 0)
										<i></i>
									@endif
								</td>
								<td class="date">{{ $note->created_at->format('Y-m-d') }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="text-center">등록된 회원비고가 없습니다.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if(isset($memberNotes) && $memberNotes->hasPages())
				<div class="board_bottom">
					<div class="paging">
						@if($memberNotes->onFirstPage())
							<span class="arrow two first" style="pointer-events: none; opacity: 0.5;">처음</span>
							<span class="arrow one prev" style="pointer-events: none; opacity: 0.5;">이전</span>
						@else
							@php
								$firstUrl = $memberNotes->url(1);
								$queryParams = request()->except('page');
								$firstUrl .= !empty($queryParams) ? '&' . http_build_query($queryParams) : '';
								$prevUrl = $memberNotes->previousPageUrl();
								$prevUrl .= !empty($queryParams) ? (str_contains($prevUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							@endphp
							<a href="{{ $firstUrl }}" class="arrow two first">처음</a>
							<a href="{{ $prevUrl }}" class="arrow one prev">이전</a>
						@endif
						
						@foreach($memberNotes->getUrlRange(max(1, $memberNotes->currentPage() - 2), min($memberNotes->lastPage(), $memberNotes->currentPage() + 2)) as $page => $url)
							@php
								$queryParams = request()->except('page');
								$pageUrl = $url;
								$pageUrl .= !empty($queryParams) ? (str_contains($pageUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							@endphp
							@if($page == $memberNotes->currentPage())
								<span class="on">{{ $page }}</span>
							@else
								<a href="{{ $pageUrl }}">{{ $page }}</a>
							@endif
						@endforeach
						
						@if($memberNotes->hasMorePages())
							@php
								$queryParams = request()->except('page');
								$nextUrl = $memberNotes->nextPageUrl();
								$nextUrl .= !empty($queryParams) ? (str_contains($nextUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
								$lastUrl = $memberNotes->url($memberNotes->lastPage());
								$lastUrl .= !empty($queryParams) ? (str_contains($lastUrl, '?') ? '&' : '?') . http_build_query($queryParams) : '';
							@endphp
							<a href="{{ $nextUrl }}" class="arrow one next">다음</a>
							<a href="{{ $lastUrl }}" class="arrow two last">맨끝</a>
						@else
							<span class="arrow one next" style="pointer-events: none; opacity: 0.5;">다음</span>
							<span class="arrow two last" style="pointer-events: none; opacity: 0.5;">맨끝</span>
						@endif
					</div>
				</div>
			@endif
		</div>
		
	</div>
</div>
@endsection