@extends('layouts.user')

@section('content')
<div id="mainContent" class="container">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span><span><strong>{{ $sName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<div class="faq_wrap">
				@forelse($faqs as $faq)
					<dl>
						<dt><button type="button">{{ $faq->title }}<i></i></button></dt>
						<dd>{!! $faq->content !!}</dd>
					</dl>
				@empty
					<div class="no_data">등록된 FAQ가 없습니다.</div>
				@endforelse
			</div>

			@include('components.board-pagination', ['paginator' => $faqs])
		</div>
		
	</div>
</div>

@include('components.user-footer')

@push('scripts')
<script>
$(document).ready(function() {
	$(".faq_wrap dt").click(function(event){
		$(this).next("dd").stop(false,true).slideToggle("fast").parent().stop(false,true).toggleClass("on").siblings().removeClass("on").children("dd").slideUp("fast");
		event.stopPropagation();
	});
});
</script>
@endpush

@endsection
