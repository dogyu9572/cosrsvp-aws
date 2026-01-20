<div class="header">
	<a href="{{ route('kofih.dashboard') }}" class="logo">
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
				<div class="name">ADMIN</div>
			</div>
			<a href="#" class="btn_mypage mo_vw">MY PAGE</a>
		</div>
		<div class="scroll">
			<div class="gnb">
				<div class="menu gnb0 {{ ($gNum ?? '') == 'main' ? 'on' : '' }}"><a href="{{ route('kofih.dashboard') }}">대시보드</a></div>
				<div class="menu gnb_k1 {{ ($gNum ?? '') == '01' ? 'on' : '' }}"><a href="{{ route('kofih.member.index') }}">회원</a></div>
				<div class="menu gnb2 {{ ($gNum ?? '') == '02' ? 'on' : '' }}"><a href="{{ route('kofih.schedule.index') }}">일정</a></div>
				<div class="menu gnb3 {{ ($gNum ?? '') == '03' ? 'on' : '' }}"><a href="{{ route('kofih.notices.index') }}">공지사항</a></div>
				<div class="menu gnb4 {{ ($gNum ?? '') == '04' ? 'on' : '' }}"><a href="#">최신뉴스</a></div>
				<div class="menu gnb5 {{ ($gNum ?? '') == '05' ? 'on' : '' }}"><a href="{{ route('kofih.gallery.index') }}">갤러리</a></div>
			</div>
		</div>
	</div>
	
</div>