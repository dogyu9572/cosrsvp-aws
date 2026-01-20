@extends('layouts.kofih')

@section('title', '회원')

@php
$gNum = 'main';
$gName = '회원';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="dashboard_kofih_wrap">
			<div class="wbox">
				<div class="btit">일정 검색</div>
				<div class="selects">
					<dl>
						<dt>기수</dt>
						<dd>
							<select name="" id="">
								<option value="">전체</option>
							</select>
						</dd>
					</dl>
					<dl>
						<dt>과정</dt>
						<dd>
							<select name="" id="">
								<option value="">전체</option>
							</select>
						</dd>
					</dl>
					<dl>
						<dt>운영기관</dt>
						<dd>
							<select name="" id="">
								<option value="">전체</option>
							</select>
						</dd>
					</dl>
					<dl>
						<dt>프로젝트 기간</dt>
						<dd>
							<select name="" id="">
								<option value="">전체</option>
							</select>
						</dd>
					</dl> 
					<dl> 
						<dt>국가</dt> 
						<dd> 
							<select name="" id=""> 
								<option value="">전체</option> 
							</select> 
						</dd> 
					</dl> 
				</div>
				<div class="info">
					<dl class="i1">
						<dt>전체 학생</dt>
						<dd><strong>200</strong>명</dd>
					</dl>
					<dl class="i2">
						<dt>전체 그룹</dt>
						<dd><strong>3</strong>건</dd>
					</dl>
					<dl class="i3">
						<dt>긴급 특이사항</dt>
						<dd><strong>20</strong>건</dd>
					</dl>
					<dl class="i4 black">
						<dt>주의 특이사항</dt>
						<dd><strong>7</strong>건</dd>
					</dl>
				</div>
			</div>
			
			<div class="wbox sch_list">
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
							<li class="urgent">긴급 1</li>
							<li class="caution">주의 1</li>
						</ul>
						<div class="tit">2025년 / 보건정책학 / 고려대 / 6개월 / 몽골</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">60%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li><i>1</i><strong>입국준비</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li><i>2</i><strong>입국후 서류확인</strong><p>2025-01-01 ~ 2025-01-02</p></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
							<li class="urgent">긴급 2</li>
						</ul>
						<div class="tit">2025년 / 기초의학 / 순천향대 / 6개월 / 라오스</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">20%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="on chk"><i>1</i><strong>입국준비</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="on"><i>2</i><strong>입국후 서류확인</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li><i>3</i><strong>건강검진</strong><p>2025-01-01 ~ 2025-01-02</p></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
							<li class="caution">주의 1</li>
						</ul>
						<div class="tit">2025년 / 임상과정 / 연세대 / 1년 / 캄보디아</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">30%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="on chk"><i>2</i><strong>입국후 서류확인</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="on"><i>3</i><strong>건강검진</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li><i>4</i><strong>계좌개설</strong><p>2025-01-01 ~ 2025-01-02</p></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 보건인력교육 전문가과정 / 서울대 / 3개월 / 태국</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 가나</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 라오스</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 베트남</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 스리랑카</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 우간다</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 이집트</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 캄보디아</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 피지</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 말라위</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
			
				<a href="#" class="box">
					<div class="pos_r">
						<ul class="state">
							<li class="count">3명</li>
						</ul>
						<div class="tit">2025년 / 정책담당과정 / 인제대학교 / 2 주 / 탄자니아</div>
						<div class="pct">
							<div class="bg"><i></i></div>
							<strong class="label">100%</strong><p>전체진행률</p>
						</div>
					</div>
					<ul class="step_area">
						<li class="blank"></li>
						<li class="on chk"><i>5</i><strong>출국</strong><p>2025-01-01 ~ 2025-01-02</p></li>
						<li class="blank"></li>
					</ul>
				</a>
				
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="/pub/css/slick.css" media="all">
<script src="/pub/js/slick.js"></script>
<script>
$('.dashboard_kofih_wrap .sch_list .pct').each(function () {
    var pctText = $(this).find('.label').text().replace('%','');
    var pct = parseFloat(pctText); // 숫자만 추출
    var angle = -180 + (pct * 1.8); 
    $(this).find('.bg i').css({
        'transform': 'rotate(' + angle + 'deg)'
    });
});
</script>
@endpush