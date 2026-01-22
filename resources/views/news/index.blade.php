@extends('layouts.user')

@section('content')
<div id="mainContent" class="container news_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
			<ul class="tabs big">
				<li class="{{ $category === 'all' ? 'on' : '' }}"><a href="{{ route('news', ['category' => 'all']) }}">All</a></li>
				<li class="{{ $category === 'main_news' ? 'on' : '' }}"><a href="{{ route('news', ['category' => 'main_news']) }}">Main News</a></li>
				<li class="{{ $category === 'lifestyle' ? 'on' : '' }}"><a href="{{ route('news', ['category' => 'lifestyle']) }}">Lifestyle</a></li>
				<li class="{{ $category === 'fashion' ? 'on' : '' }}"><a href="{{ route('news', ['category' => 'fashion']) }}">Fashion</a></li>
				<li class="{{ $category === 'entertainment' ? 'on' : '' }}"><a href="{{ route('news', ['category' => 'entertainment']) }}">Entertainment</a></li>
			</ul>
			<div class="board_top">
				<div class="left">
					<div class="total">Total <strong class="c_red">{{ $news->total() }}</strong></div>
					<select name="per_page" id="per_page_select" class="pc_vw" onchange="changePerPage(this.value)">
						<option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>View 10 at a time</option>
						<option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>View 20 at a time</option>
						<option value="30" {{ request('per_page', 10) == 30 ? 'selected' : '' }}>View 30 at a time</option>
						<option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>View 50 at a time</option>
					</select>
				</div>
				<div class="right select_area">
					<select name="per_page" id="per_page_select_mo" class="mo_vw" onchange="changePerPage(this.value)">
						<option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>View 10 at a time</option>
						<option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>View 20 at a time</option>
						<option value="30" {{ request('per_page', 10) == 30 ? 'selected' : '' }}>View 30 at a time</option>
						<option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>View 50 at a time</option>
					</select>
					<select name="category" id="category_select" onchange="changeCategory(this.value)">
						<option value="all" {{ $category === 'all' ? 'selected' : '' }}>All</option>
						<option value="main_news" {{ $category === 'main_news' ? 'selected' : '' }}>Main News</option>
						<option value="lifestyle" {{ $category === 'lifestyle' ? 'selected' : '' }}>Lifestyle</option>
						<option value="fashion" {{ $category === 'fashion' ? 'selected' : '' }}>Fashion</option>
						<option value="entertainment" {{ $category === 'entertainment' ? 'selected' : '' }}>Entertainment</option>
					</select>
					<form method="GET" action="{{ route('news') }}" id="search_form">
						<input type="hidden" name="category" value="{{ $category }}">
						<input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
						<input type="text" name="keyword" class="text" placeholder="Please enter a search term." value="{{ request('keyword', '') }}">
						<button type="submit" class="btn">Search</button>
					</form>
				</div>
			</div>
			
			<div class="tbl board_list">
				<table>
					<colgroup>
						<col class="w90">
						<col>
						<col class="w120">
					</colgroup>
					<thead>
						<tr>
							<th>No.</th>
							<th>Title</th>
							<th>Registration Date</th>
						</tr>
					</thead>
					<tbody>
						@forelse($news as $item)
							<tr>
								<td class="num">{{ $news->total() - (($news->currentPage() - 1) * $news->perPage()) - $loop->index }}</td>
								<td class="tal tit">
									<a href="{{ $item['originallink'] ?? $item['link'] ?? '#' }}" target="_blank" rel="noopener noreferrer">{{ $item['title'] }}</a>
								</td>
								<td class="date">{{ $item['date'] }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="3" class="no_data">등록된 뉴스가 없습니다.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@include('components.board-pagination', ['paginator' => $news])
		</div>
		
	</div>
</div>

@include('components.user-footer')

<script>
function changePerPage(value) {
	const url = new URL(window.location.href);
	url.searchParams.set('per_page', value);
	url.searchParams.set('page', '1');
	window.location.href = url.toString();
}

function changeCategory(value) {
	const url = new URL(window.location.href);
	url.searchParams.set('category', value);
	url.searchParams.set('page', '1');
	window.location.href = url.toString();
}
</script>

@endsection
