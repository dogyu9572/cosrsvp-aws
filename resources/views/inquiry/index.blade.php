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
		
		<div class="wbox">
			@include('components.board-top', [
				'total' => $inquiries->total(),
				'routeName' => 'inquiries',
				'perPage' => request('per_page', 10),
				'keyword' => request('keyword', ''),
				'showCategorySelect' => false
			])
			
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
							<th>Title</th>
							<th>Response Status</th>
							<th>Registration Date</th>
						</tr>
					</thead>
					<tbody>
						@forelse($inquiries as $inquiry)
							<tr>
								<td class="num">{{ $inquiries->total() - (($inquiries->currentPage() - 1) * $inquiries->perPage()) - $loop->index }}</td>
								<td class="tal tit">
									<a href="{{ route('inquiries.show', $inquiry->id) }}">{{ $inquiry->title }}</a>
								</td>
								<td class="reply">
									@if($inquiry->reply_status === 'completed')
										<i class="end">답변완료</i>
									@else
										<i class="ing">답변대기</i>
									@endif
								</td>
								<td class="date">{{ \Carbon\Carbon::parse($inquiry->created_at)->format('Y-m-d') }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="no_data">등록된 문의사항이 없습니다.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<div class="board_bottom">
				<a href="{{ route('inquiries.create') }}" class="btn_abso">Contact Us</a>
				<div class="paging">
					@php
						$currentPage = $inquiries->currentPage();
						$lastPage = $inquiries->lastPage();
						$startPage = max(1, $currentPage - 2);
						$endPage = min($lastPage, $currentPage + 2);
					@endphp
					
					@if($currentPage == 1)
						<span class="arrow two first">First</span>
						<span class="arrow one prev">Prev</span>
					@else
						<a href="{{ $inquiries->url(1) }}" class="arrow two first">First</a>
						<a href="{{ $inquiries->previousPageUrl() }}" class="arrow one prev">Prev</a>
					@endif
					
					@for($page = $startPage; $page <= $endPage; $page++)
						@if($page == $currentPage)
							<a href="{{ $inquiries->url($page) }}" class="on">{{ $page }}</a>
						@else
							<a href="{{ $inquiries->url($page) }}">{{ $page }}</a>
						@endif
					@endfor
					
					@if($currentPage >= $lastPage)
						<span class="arrow one next">Next</span>
						<span class="arrow two last">Last</span>
					@else
						<a href="{{ $inquiries->nextPageUrl() }}" class="arrow one next">Next</a>
						<a href="{{ $inquiries->url($lastPage) }}" class="arrow two last">Last</a>
					@endif
				</div>
			</div>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
