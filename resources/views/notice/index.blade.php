@extends('layouts.user')

@section('content')
<div id="mainContent" class="container notice_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
			@include('components.board-top', [
				'total' => $notices->total(),
				'routeName' => 'notices',
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
							<th>Attached File</th>
							<th>Registration Date</th>
						</tr>
					</thead>
					<tbody>
						@forelse($notices as $notice)
							<tr class="{{ $notice->is_notice ? 'notice' : '' }}">
								<td class="num">{{ $notices->total() - (($notices->currentPage() - 1) * $notices->perPage()) - $loop->index }}</td>
								<td class="tal tit">
									<a href="{{ route('notices.show', $notice->id) }}">{{ $notice->title }}</a>
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

			@include('components.board-pagination', ['paginator' => $notices])
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
