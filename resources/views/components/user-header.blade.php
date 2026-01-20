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
				<div class="menu gnb3 {{ ($gNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ route('map') }}">Map</a></div>
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
					@if($hasNewAlert)
						<button type="button" class="alert"><span class="flex"><i></i><p>There is a notification that requires your confirmation.<br/>Please confirm.</p></span></button>
					@endif
					<div class="snb">
						<a href="{{ route('mypage') }}" class="{{ (($gNum ?? '') == '08' && ($sNum ?? '') == '01') ? 'on' : '' }}">MY PAGE</a>
						<a href="{{ route('alarms') }}" class="{{ (($gNum ?? '') == '08' && ($sNum ?? '') == '02') ? 'on' : '' }}">Alarm</a>
					</div>
				</div>
			</div>
			<div class="weather">
				<dl class="gap1">
					<dt>Weather</dt>
					<dd id="weather-info">
						<p>
							<i><img src="/pub/images/Icon feather-sun.png" alt="" id="weather-icon" style="display:none;"></i>
							<strong id="weather-temperature">-</strong>
						</p>
					</dd>
				</dl>
				<dl class="gap2">
					<dt>Exchange rate</dt>
					<dd id="exchange-rates">
						<p>USD<strong>-</strong></p>
						<p>EUR<strong>-</strong></p>
						<p>GBP<strong>-</strong></p>
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

	// 환율 정보 로드
	function loadExchangeRates() {
		fetch('{{ route("api.exchange-rates") }}')
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data) {
					const rates = data.data;
					const $exchangeRates = $('#exchange-rates');
					
					// 값이 있을 때만 표시
					const usd = (rates.usd && rates.usd !== '-') ? rates.usd : '-';
					const eur = (rates.eur && rates.eur !== '-') ? rates.eur : '-';
					const gbp = (rates.gbp && rates.gbp !== '-') ? rates.gbp : '-';
					
					$exchangeRates.html(
						'<p>USD<strong>' + usd + '</strong></p>' +
						'<p>EUR<strong>' + eur + '</strong></p>' +
						'<p>GBP<strong>' + gbp + '</strong></p>'
					);
				}
			})
			.catch(error => {
				console.error('환율 정보를 불러오는데 실패했습니다:', error);
				// 에러 시에도 기본값 표시하지 않음
			});
	}

	// 페이지 로드 시 환율 정보 가져오기
	loadExchangeRates();

	// 날씨 정보 로드
	function loadWeather() {
		// 사용자 위치 가져오기
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(position) {
					const lat = position.coords.latitude;
					const lng = position.coords.longitude;
					
					fetchWeather(lat, lng);
				},
				function(error) {
					console.error("위치 정보를 가져올 수 없습니다:", error);
					// 위치 정보 없이 날씨 정보 가져오기 (기본 위치 사용)
					fetchWeather();
				}
			);
		} else {
			// Geolocation을 지원하지 않는 경우 기본 위치 사용
			fetchWeather();
		}
	}

	function fetchWeather(lat, lng) {
		let url = '{{ route("api.weather") }}';
		if (lat && lng) {
			url += '?lat=' + lat + '&lng=' + lng;
		}
		
		fetch(url)
			.then(response => response.json())
			.then(data => {
				if (data.success && data.data) {
					const weather = data.data;
					
					// 아이콘 업데이트
					const iconElement = document.getElementById('weather-icon');
					if (iconElement && weather.icon) {
						iconElement.src = weather.icon;
						iconElement.style.display = 'flex';
						
						// SKY 코드에 따른 alt 텍스트
						let altText = '맑음';
						if (weather.sky === 3) altText = '구름많음';
						else if (weather.sky === 4) altText = '흐림';
						
						// PTY 코드에 따른 alt 텍스트
						if (weather.pty === 1) altText = '비';
						else if (weather.pty === 2) altText = '비/눈';
						else if (weather.pty === 3) altText = '눈';
						else if (weather.pty === 4) altText = '소나기';
						
						iconElement.alt = altText;
					}
					
					// 기온 업데이트
					const tempElement = document.getElementById('weather-temperature');
					if (tempElement && weather.temperature !== null && weather.temperature !== undefined) {
						tempElement.textContent = weather.temperature + '°C';
					} else {
						tempElement.textContent = '-';
					}
				}
			})
			.catch(error => {
				console.error('날씨 정보를 불러오는데 실패했습니다:', error);
			});
	}

	// 페이지 로드 시 날씨 정보 가져오기
	loadWeather();
});
</script>
@endpush
