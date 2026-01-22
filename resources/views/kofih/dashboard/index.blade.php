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
				<form method="GET" action="{{ route('kofih.dashboard') }}" id="dashboardFilterForm">
					<div class="selects">
						<dl>
							<dt>기수</dt>
							<dd>
								<select name="project_term_id" id="project_term_id">
									<option value="">전체</option>
									@foreach($projectTerms ?? [] as $term)
										<option value="{{ $term->id }}" @selected(($filters['project_term_id'] ?? null) == $term->id)>{{ $term->name }}</option>
									@endforeach
								</select>
							</dd>
						</dl>
						<dl>
							<dt>과정</dt>
							<dd>
								<select name="course_id" id="course_id">
									<option value="">전체</option>
									@if(($filters['project_term_id'] ?? null) && isset($courses) && $courses->count() > 0)
										@foreach($courses as $course)
											<option value="{{ $course->id }}" @selected(($filters['course_id'] ?? null) == $course->id)>{{ $course->name_ko ?? $course->name_en }}</option>
										@endforeach
									@endif
								</select>
							</dd>
						</dl>
						<dl>
							<dt>운영기관</dt>
							<dd>
								<select name="operating_institution_id" id="operating_institution_id">
									<option value="">전체</option>
									@if(($filters['course_id'] ?? null) && isset($operatingInstitutions) && $operatingInstitutions->count() > 0)
										@foreach($operatingInstitutions as $institution)
											<option value="{{ $institution->id }}" @selected(($filters['operating_institution_id'] ?? null) == $institution->id)>{{ $institution->name_ko ?? $institution->name_en }}</option>
										@endforeach
									@endif
								</select>
							</dd>
						</dl>
						<dl>
							<dt>프로젝트 기간</dt>
							<dd>
								<select name="project_period_id" id="project_period_id">
									<option value="">전체</option>
									@if(($filters['operating_institution_id'] ?? null) && isset($projectPeriods) && $projectPeriods->count() > 0)
										@foreach($projectPeriods as $period)
											<option value="{{ $period->id }}" @selected(($filters['project_period_id'] ?? null) == $period->id)>{{ $period->name_ko ?? $period->name_en }}</option>
										@endforeach
									@endif
								</select>
							</dd>
						</dl>
						<dl>
							<dt>국가</dt>
							<dd>
								<select name="country_id" id="country_id">
									<option value="">전체</option>
									@if(($filters['project_period_id'] ?? null) && isset($countries) && $countries->count() > 0)
										@foreach($countries as $country)
											<option value="{{ $country->id }}" @selected(($filters['country_id'] ?? null) == $country->id)>{{ $country->name_ko ?? $country->name }}</option>
										@endforeach
									@endif
								</select>
							</dd>
						</dl>
					</div>
				</form>
				<div class="info">
					<dl class="i1">
						<dt>전체 학생</dt>
						<dd><strong>{{ $statistics['total_students'] ?? 0 }}</strong>명</dd>
					</dl>
					<dl class="i2">
						<dt>전체 그룹</dt>
						<dd><strong>{{ $statistics['total_groups'] ?? 0 }}</strong>건</dd>
					</dl>
					<dl class="i3">
						<dt>긴급 특이사항</dt>
						<dd><strong>{{ $statistics['urgent_notes'] ?? 0 }}</strong>건</dd>
					</dl>
					<dl class="i4 black">
						<dt>주의 특이사항</dt>
						<dd><strong>{{ $statistics['caution_notes'] ?? 0 }}</strong>건</dd>
					</dl>
				</div>
			</div>
			
			<div class="wbox sch_list">
				@forelse($scheduleGroups ?? [] as $group)
					@php
						$queryParams = http_build_query([
							'filter_project_term_id' => $group->project_info['project_term_id'],
							'filter_course_id' => $group->project_info['course_id'],
							'filter_operating_institution_id' => $group->project_info['operating_institution_id'],
							'filter_project_period_id' => $group->project_info['project_period_id'],
							'filter_country_id' => $group->project_info['country_id'],
						]);
					@endphp
					<a href="{{ route('kofih.member.index') }}?{{ $queryParams }}" class="box">
						<div class="pos_r">
							<ul class="state">
								<li class="count">{{ $group->member_count }}명</li>
								@if($group->urgent_count > 0)
									<li class="urgent">긴급 {{ $group->urgent_count }}</li>
								@endif
								@if($group->caution_count > 0)
									<li class="caution">주의 {{ $group->caution_count }}</li>
								@endif
							</ul>
							<div class="tit">{{ $group->title }}</div>
							<div class="pct">
								<div class="bg"><i></i></div>
								<strong class="label">{{ $group->progress_rate }}%</strong><p>전체진행률</p>
							</div>
						</div>
						<ul class="step_area">
							@php
								$today = \Carbon\Carbon::today();
								$maxDisplay = 5; // 최대 5개 일정 표시
								$totalSteps = $group->step_schedules->count();
								$displayedSteps = [];
								
								// 최대 5개까지 일정 표시
								$displayCount = min($maxDisplay, $totalSteps);
								for ($i = 0; $i < $displayCount; $i++) {
									$displayedSteps[] = $i;
								}
							@endphp
							
							@foreach($displayedSteps as $displayIndex => $scheduleIndex)
								@php
									$step = $group->step_schedules[$scheduleIndex];
									$isCurrent = false;
									$isCompleted = false;
									
									if ($step->start_date && $step->end_date) {
										$startDate = \Carbon\Carbon::parse($step->start_date);
										$endDate = \Carbon\Carbon::parse($step->end_date);
										$isCurrent = $today >= $startDate && $today <= $endDate;
										$isCompleted = $today > $endDate;
									}
									
									$dateRange = '';
									if ($step->start_date && $step->end_date) {
										$start = \Carbon\Carbon::parse($step->start_date)->format('Y-m-d');
										$end = \Carbon\Carbon::parse($step->end_date)->format('Y-m-d');
										$dateRange = $start . ' ~ ' . $end;
									}
									
									// 단계 번호는 display_order가 있으면 사용, 없으면 순서대로
									$stepNumber = $step->display_order ?? ($scheduleIndex + 1);
								@endphp
								<li class="{{ $isCurrent ? 'on' : '' }} {{ $isCompleted ? 'chk' : '' }}">
									<i>{{ $stepNumber }}</i>
									<strong>{{ $step->title }}</strong>
									@if($dateRange)
										<p>{{ $dateRange }}</p>
									@endif
								</li>
							@endforeach
							
							@if($totalSteps > $maxDisplay)
								<li class="blank"></li>
							@endif
						</ul>
					</a>
				@empty
					<div class="no_data">등록된 일정이 없습니다.</div>
				@endforelse
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="/pub/css/slick.css" media="all">
<script src="/pub/js/slick.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
	// 진행률 원형 프로그레스 바
	$('.dashboard_kofih_wrap .sch_list .pct').each(function () {
		var pctText = $(this).find('.label').text().replace('%','');
		var pct = parseFloat(pctText);
		if (isNaN(pct)) pct = 0;
		var angle = -180 + (pct * 1.8);
		$(this).find('.bg i').css({
			'transform': 'rotate(' + angle + 'deg)'
		});
	});
	
	// Cascading dropdown 구현 및 자동 검색
	$('#project_term_id').on('change', function() {
		const projectTermId = $(this).val();
		const $courseSelect = $('#course_id');
		
		$('#operating_institution_id, #project_period_id, #country_id').html('<option value="">전체</option>');
		
		if (!projectTermId) {
			$courseSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
			return;
		}
		
		$.get('{{ route("kofih.schedule.get-courses-by-project-term") }}', {
			project_term_id: projectTermId
		})
		.done(function(courses) {
			$courseSelect.html('<option value="">전체</option>');
			if (courses && courses.length > 0) {
				courses.forEach(function(course) {
					$courseSelect.append('<option value="' + course.id + '">' + (course.name_ko || course.name_en) + '</option>');
				});
			}
			$('#dashboardFilterForm').submit();
		})
		.fail(function() {
			$courseSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
		});
	});
	
	$('#course_id').on('change', function() {
		const courseId = $(this).val();
		const $institutionSelect = $('#operating_institution_id');
		
		$('#project_period_id, #country_id').html('<option value="">전체</option>');
		
		if (!courseId) {
			$institutionSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
			return;
		}
		
		$.get('{{ route("kofih.schedule.get-institutions-by-course") }}', {
			course_id: courseId
		})
		.done(function(institutions) {
			$institutionSelect.html('<option value="">전체</option>');
			if (institutions && institutions.length > 0) {
				institutions.forEach(function(institution) {
					$institutionSelect.append('<option value="' + institution.id + '">' + (institution.name_ko || institution.name_en) + '</option>');
				});
			}
			$('#dashboardFilterForm').submit();
		})
		.fail(function() {
			$institutionSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
		});
	});
	
	$('#operating_institution_id').on('change', function() {
		const institutionId = $(this).val();
		const $periodSelect = $('#project_period_id');
		
		$('#country_id').html('<option value="">전체</option>');
		
		if (!institutionId) {
			$periodSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
			return;
		}
		
		$.get('{{ route("kofih.schedule.get-project-periods-by-institution") }}', {
			operating_institution_id: institutionId
		})
		.done(function(periods) {
			$periodSelect.html('<option value="">전체</option>');
			if (periods && periods.length > 0) {
				periods.forEach(function(period) {
					$periodSelect.append('<option value="' + period.id + '">' + (period.name_ko || period.name_en) + '</option>');
				});
			}
			$('#dashboardFilterForm').submit();
		})
		.fail(function() {
			$periodSelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
		});
	});
	
	$('#project_period_id').on('change', function() {
		const periodId = $(this).val();
		const $countrySelect = $('#country_id');
		
		if (!periodId) {
			$countrySelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
			return;
		}
		
		$.get('{{ route("kofih.schedule.get-countries-by-project-period") }}', {
			project_period_id: periodId
		})
		.done(function(countries) {
			$countrySelect.html('<option value="">전체</option>');
			if (countries && countries.length > 0) {
				countries.forEach(function(country) {
					$countrySelect.append('<option value="' + country.id + '">' + (country.name_ko || country.name) + '</option>');
				});
			}
			$('#dashboardFilterForm').submit();
		})
		.fail(function() {
			$countrySelect.html('<option value="">전체</option>');
			$('#dashboardFilterForm').submit();
		});
	});
	
	// 국가 선택 시에도 자동 검색
	$('#country_id').on('change', function() {
		$('#dashboardFilterForm').submit();
	});
});
</script>
@endpush
