@props(['paginator'])

<div class="board_bottom">
	<div class="paging">
		@php
			$currentPage = $paginator->currentPage();
			$lastPage = $paginator->lastPage();
			$startPage = max(1, $currentPage - 2);
			$endPage = min($lastPage, $currentPage + 2);
		@endphp
		
		@if($currentPage == 1)
			<span class="arrow two first">First</span>
			<span class="arrow one prev">Prev</span>
		@else
			<a href="{{ $paginator->url(1) }}" class="arrow two first">First</a>
			<a href="{{ $paginator->previousPageUrl() }}" class="arrow one prev">Prev</a>
		@endif
		
		@for($page = $startPage; $page <= $endPage; $page++)
			@if($page == $currentPage)
				<a href="{{ $paginator->url($page) }}" class="on">{{ $page }}</a>
			@else
				<a href="{{ $paginator->url($page) }}">{{ $page }}</a>
			@endif
		@endfor
		
		@if($currentPage >= $lastPage)
			<span class="arrow one next">Next</span>
			<span class="arrow two last">Last</span>
		@else
			<a href="{{ $paginator->nextPageUrl() }}" class="arrow one next">Next</a>
			<a href="{{ $paginator->url($lastPage) }}" class="arrow two last">Last</a>
		@endif
	</div>
</div>
