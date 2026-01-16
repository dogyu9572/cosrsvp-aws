@props([
    'total' => 0,
    'routeName' => '',
    'perPage' => 10,
    'keyword' => '',
    'showCategorySelect' => false
])

<div class="board_top">
	<div class="left">
		<div class="total">Total <strong class="c_red">{{ $total }}</strong></div>
		<form method="GET" action="{{ route($routeName) }}" class="per-page-form pc_vw">
			@if($keyword)
				<input type="hidden" name="keyword" value="{{ $keyword }}">
			@endif
			<select name="per_page" onchange="this.form.submit()">
				<option value="10" {{ $perPage == 10 ? 'selected' : '' }}>View 10 at a time</option>
				<option value="20" {{ $perPage == 20 ? 'selected' : '' }}>View 20 at a time</option>
				<option value="30" {{ $perPage == 30 ? 'selected' : '' }}>View 30 at a time</option>
				<option value="50" {{ $perPage == 50 ? 'selected' : '' }}>View 50 at a time</option>
			</select>
		</form>
	</div>
	<div class="right select_area">
		<form method="GET" action="{{ route($routeName) }}" class="per-page-form mo_vw">
			@if($keyword)
				<input type="hidden" name="keyword" value="{{ $keyword }}">
			@endif
			<select name="per_page" onchange="this.form.submit()">
				<option value="10" {{ $perPage == 10 ? 'selected' : '' }}>View 10 at a time</option>
				<option value="20" {{ $perPage == 20 ? 'selected' : '' }}>View 20 at a time</option>
				<option value="30" {{ $perPage == 30 ? 'selected' : '' }}>View 30 at a time</option>
				<option value="50" {{ $perPage == 50 ? 'selected' : '' }}>View 50 at a time</option>
			</select>
		</form>
		@if($showCategorySelect)
			<select name="" id="">
				<option value="">All</option>
			</select>
		@endif
		<form method="GET" action="{{ route($routeName) }}" class="search-form">
			@if($perPage)
				<input type="hidden" name="per_page" value="{{ $perPage }}">
			@endif
			<input type="text" name="keyword" value="{{ $keyword }}" class="text" placeholder="Please enter a search term.">
			<button type="submit" class="btn">Search</button>
		</form>
	</div>
</div>
