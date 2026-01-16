@extends('layouts.user')

@section('content')
<div id="mainContent" class="container gallery_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
			@include('components.board-top', [
				'total' => $galleries->total(),
				'routeName' => 'gallery',
				'perPage' => request('per_page', 10),
				'keyword' => request('keyword', ''),
				'showCategorySelect' => false
			])
			
			<ul class="gall_list">
				@forelse($galleries as $gallery)
					<li>
						<a href="{{ route('gallery.show', $gallery->id) }}">
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

			@include('components.board-pagination', ['paginator' => $galleries])
		</div>
		
	</div>
</div>

@include('components.user-footer')

@endsection
