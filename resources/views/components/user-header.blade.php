@php
$member = session('member', null);
$memberName = $member['name'] ?? 'Hong Gil-dong';
$memberAffiliation = $member['affiliation'] ?? 'Basic Medicine_Korea University';
@endphp

<div class="header">
	<a href="{{ route('home') }}" class="logo">
		<img src="/pub/images/logo.png" alt="logo"><h1>COSMOJIN</h1>
	</a>
	<a href="javascript:void(0);" class="btn_menu">
		<p class="t"></p>
		<p class="m"></p>
		<p class="b"></p>
	</a>
	
	<div class="mo_wrap">
		<div class="info">
			<div class="mo_info_in">
				<div class="img"><img src="/pub/images/img_profile.svg" alt=""></div>
				<div class="name">{{ $memberName }}</div>
				<div class="affiliation">{{ $memberAffiliation }}</div>
			</div>
			<a href="{{ route('mypage') }}" class="btn_mypage mo_vw">MY PAGE</a>
		</div>
		<div class="scroll">
			<div class="gnb">
				<div class="menu gnb0 {{ ($gNum ?? '') == 'main' ? 'on' : '' }}"><a href="{{ route('home') }}">Dashboard</a></div>
				<div class="menu gnb2 {{ ($gNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ route('schedule') }}">Schedule</a></div>
				<div class="menu gnb3 {{ ($gNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ route('home') }}">Map</a></div>
				<div class="menu gnb4 {{ ($gNum ?? '') == '04' ? 'on' : '' }}"><a href="{{ route('notices') }}">Notice</a></div>
				<div class="menu gnb5 {{ ($gNum ?? '') == '05' ? 'on' : '' }}"><a href="{{ route('gallery') }}">Gallery</a></div>
				<div class="menu gnb6 {{ ($gNum ?? '') == '06' ? 'on' : '' }}"><a href="{{ route('home') }}">Latest News</a></div>
				<div class="menu gnb7 {{ ($gNum ?? '') == '07' ? 'on' : '' }}"><a href="{{ route('inquiries') }}">Contact Us<i></i></a>
					<div class="snb">
						<a href="{{ route('inquiries') }}" class="{{ (($gNum ?? '') == '07' && ($sNum ?? '') == '01') ? 'on' : '' }}">Contact Us</a>
						<a href="{{ route('faq') }}" class="{{ (($gNum ?? '') == '07' && ($sNum ?? '') == '02') ? 'on' : '' }}">FAQ</a>
					</div>
				</div>
				<div class="menu gnb8 {{ ($gNum ?? '') == '08' ? 'on' : '' }}"><a href="{{ route('mypage') }}">MY PAGE<i></i></a>
					<button type="button" class="alert"><span class="flex"><i></i><p>There is a notification that requires your confirmation.<br/>Please confirm.</p></span></button>
					<div class="snb">
						<a href="{{ route('mypage') }}" class="{{ (($gNum ?? '') == '08' && ($sNum ?? '') == '01') ? 'on' : '' }}">MY PAGE</a>
						<a href="{{ route('alarms') }}" class="{{ (($gNum ?? '') == '08' && ($sNum ?? '') == '02') ? 'on' : '' }}">Alarm</a>
					</div>
				</div>
			</div>
			<div class="weather">
				<dl class="gap1">
					<dt>Weather</dt>
					<dd>
						<p>
							<i><img src="/pub/images/icon_sun.svg" alt="맑음"></i>
							<strong>10°C</strong>
						</p>
					</dd>
				</dl>
				<dl class="gap2">
					<dt>Exchange rate</dt>
					<dd>
						<p>USD<strong>1,463.60</strong></p>
						<p>EUR<strong>1,690.97</strong></p>
						<p>GBP<strong>1,926.61</strong></p>
					</dd>
				</dl>
			</div>
		</div>
	</div>
	
</div>

@push('scripts')
<script>
$(document).ready(function(){
	// 서브메뉴 토글 (2차 메뉴)
	$(".header .gnb .menu > a").click(function(e){
		var $menu = $(this).parent(".menu");
		var $snb = $menu.find(".snb");
		
		// 서브메뉴가 있는 경우에만 토글
		if ($snb.length > 0) {
			e.preventDefault();
			$snb.stop(false,true).slideToggle("fast");
			$menu.stop(false,true).toggleClass("open").siblings().removeClass("open").removeClass("on").children(".snb").slideUp("fast");
		}
	});
});
</script>
@endpush
