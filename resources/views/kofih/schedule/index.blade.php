@extends('layouts.kofih')

@section('title', '일정')

@php
$gNum = '02';
$gName = '일정';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="dashboard_kofih_wrap">
			<div class="wbox inabso">
				<div class="btit">일정 검색</div>
				<form method="GET" action="{{ route('kofih.schedule.index') }}" id="scheduleFilterForm">
					<div class="selects">
						<dl>
							<dt class="tt">기수</dt>
							<dd>
								<select name="project_term_id" id="project_term_id">
									<option value="">전체</option>
									@foreach($projectTerms ?? [] as $term)
										<option value="{{ $term->id }}" @selected($projectTermId == $term->id)>{{ $term->name }}</option>
									@endforeach
								</select>
							</dd>
						</dl>
						<dl>
							<dt>과정</dt>
							<dd>
								<select name="course_id" id="course_id">
									<option value="">전체</option>
									@if($projectTermId && isset($courses) && $courses->count() > 0)
										@foreach($courses as $course)
											<option value="{{ $course->id }}" @selected($courseId == $course->id)>{{ $course->name_ko ?? $course->name_en }}</option>
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
									@if($courseId && isset($operatingInstitutions) && $operatingInstitutions->count() > 0)
										@foreach($operatingInstitutions as $institution)
											<option value="{{ $institution->id }}" @selected($operatingInstitutionId == $institution->id)>{{ $institution->name_ko ?? $institution->name_en }}</option>
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
									@if($operatingInstitutionId && isset($projectPeriods) && $projectPeriods->count() > 0)
										@foreach($projectPeriods as $period)
											<option value="{{ $period->id }}" @selected($projectPeriodId == $period->id)>{{ $period->name_ko ?? $period->name_en }}</option>
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
									@if($projectPeriodId && isset($countries) && $countries->count() > 0)
										@foreach($countries as $country)
											<option value="{{ $country->id }}" @selected($countryId == $country->id)>{{ $country->name_ko ?? $country->name }}</option>
										@endforeach
									@endif
								</select> 
							</dd> 
						</dl> 
					</div> 
					<div class="btns_search_area flex_center btns_abso"> 
						<button type="submit" class="btn btn_search">검색</button> 
					</div> 
				</form>
			</div>
			
			<div class="wbox">
				<div class="month_area schedule_area nbd">
					<div class="date_pick flex_center">
						<strong id="currentMonth">{{ $year }}.{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</strong>
						<button class="arrow prev" type="button">이전</button>
						<button class="arrow next" type="button">다음</button>
					</div>
					<table>
						<thead>
							<tr>
								<th>일</th>
								<th>월</th>
								<th>화</th>
								<th>수</th>
								<th>목</th>
								<th>금</th>
								<th>토</th>
							</tr>
						</thead>
						<tbody id="calendarBody">
							<!-- 달력이 JavaScript로 동적 생성됨 -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
	</div>
</div>

@include('components.popup-schedule')

@push('scripts')
<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
	// 스케줄 데이터 (전역 변수로 선언하여 필터링 후 업데이트 가능하도록)
	let schedules = @json($schedules ?? []);
	
	// 현재 월 설정
	let currentYear = {{ $year }};
	let currentMonth = {{ $month }} - 1; // 0-11
	
	// 초기 선택된 값 저장 (전역 변수)
	let initialProjectTermId = null;
	let initialCourseId = null;
	let initialInstitutionId = null;
	let initialPeriodId = null;
	let initialCountryId = null;
	
	// 연쇄 필터링: 기수 선택 시 과정 업데이트
	$('#project_term_id').on('change', function() {
		const projectTermId = $(this).val();
		const $courseSelect = $('#course_id');
		const $institutionSelect = $('#operating_institution_id');
		const $periodSelect = $('#project_period_id');
		const $countrySelect = $('#country_id');
		
		// 현재 선택된 값 저장
		const currentCourseId = $courseSelect.val();
		const currentInstitutionId = $institutionSelect.val();
		const currentPeriodId = $periodSelect.val();
		const currentCountryId = $countrySelect.val();
		
		// 하위 셀렉트박스 초기화
		$institutionSelect.html('<option value="">전체</option>');
		$periodSelect.html('<option value="">전체</option>');
		$countrySelect.html('<option value="">전체</option>');
		
		if (!projectTermId) {
			// 기수 선택 해제 시 모든 과정 표시
			loadAllCourses($courseSelect, currentCourseId);
			return;
		}
		
		// 해당 기수의 과정만 로드
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
			// 이전에 선택된 값이 있으면 복원 (초기 로드 시에는 저장된 값 사용)
			const valueToRestore = currentCourseId || (typeof initialCourseId !== 'undefined' && initialCourseId ? initialCourseId : null);
			if (valueToRestore && $courseSelect.find('option[value="' + valueToRestore + '"]').length > 0) {
				$courseSelect.val(valueToRestore);
				// 초기 로드가 아닌 경우에만 change 이벤트 트리거
				if (currentCourseId !== valueToRestore || !initialCourseId) {
					$courseSelect.trigger('change');
				}
			}
		})
		.fail(function(xhr, status, error) {
			$courseSelect.html('<option value="">전체</option>');
		});
	});
	
	// 연쇄 필터링: 과정 선택 시 운영기관 업데이트
	$('#course_id').on('change', function() {
		const courseId = $(this).val();
		const $institutionSelect = $('#operating_institution_id');
		const $periodSelect = $('#project_period_id');
		const $countrySelect = $('#country_id');
		
		// 현재 선택된 값 저장
		const currentInstitutionId = $institutionSelect.val();
		const currentPeriodId = $periodSelect.val();
		const currentCountryId = $countrySelect.val();
		
		// 하위 셀렉트박스 초기화
		$periodSelect.html('<option value="">전체</option>');
		$countrySelect.html('<option value="">전체</option>');
		
		if (!courseId) {
			// 과정 선택 해제 시 모든 운영기관 표시
			loadAllInstitutions($institutionSelect, currentInstitutionId);
			return;
		}
		
		// 해당 과정의 운영기관만 로드
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
			// 이전에 선택된 값이 있으면 복원 (초기 로드 시에는 저장된 값 사용)
			const valueToRestore = currentInstitutionId || (typeof initialInstitutionId !== 'undefined' ? initialInstitutionId : null);
			if (valueToRestore && $institutionSelect.find('option[value="' + valueToRestore + '"]').length > 0) {
				$institutionSelect.val(valueToRestore);
				$institutionSelect.trigger('change');
			}
		})
		.fail(function(xhr, status, error) {
			$institutionSelect.html('<option value="">전체</option>');
		});
	});
	
	// 연쇄 필터링: 운영기관 선택 시 프로젝트기간 업데이트
	$('#operating_institution_id').on('change', function() {
		const operatingInstitutionId = $(this).val();
		const $periodSelect = $('#project_period_id');
		const $countrySelect = $('#country_id');
		
		// 현재 선택된 값 저장
		const currentPeriodId = $periodSelect.val();
		const currentCountryId = $countrySelect.val();
		
		// 하위 셀렉트박스 초기화
		$countrySelect.html('<option value="">전체</option>');
		
		if (!operatingInstitutionId) {
			// 운영기관 선택 해제 시 모든 프로젝트기간 표시
			loadAllProjectPeriods($periodSelect, currentPeriodId);
			return;
		}
		
		// 해당 운영기관의 프로젝트기간만 로드
		$.get('{{ route("kofih.schedule.get-project-periods-by-institution") }}', {
			operating_institution_id: operatingInstitutionId
		})
		.done(function(periods) {
			$periodSelect.html('<option value="">전체</option>');
			if (periods && periods.length > 0) {
				periods.forEach(function(period) {
					$periodSelect.append('<option value="' + period.id + '">' + (period.name_ko || period.name_en) + '</option>');
				});
			}
			// 이전에 선택된 값이 있으면 복원 (초기 로드 시에는 저장된 값 사용)
			const valueToRestore = currentPeriodId || (typeof initialPeriodId !== 'undefined' ? initialPeriodId : null);
			if (valueToRestore && $periodSelect.find('option[value="' + valueToRestore + '"]').length > 0) {
				$periodSelect.val(valueToRestore);
				$periodSelect.trigger('change');
			}
		})
		.fail(function(xhr, status, error) {
			$periodSelect.html('<option value="">전체</option>');
		});
	});
	
	// 연쇄 필터링: 프로젝트기간 선택 시 국가 업데이트
	$('#project_period_id').on('change', function() {
		const projectPeriodId = $(this).val();
		const $countrySelect = $('#country_id');
		
		// 현재 선택된 값 저장
		const currentCountryId = $countrySelect.val();
		
		if (!projectPeriodId) {
			// 프로젝트기간 선택 해제 시 모든 국가 표시
			loadAllCountries($countrySelect, currentCountryId);
			return;
		}
		
		// 해당 프로젝트기간의 국가만 로드
		$.get('{{ route("kofih.schedule.get-countries-by-project-period") }}', {
			project_period_id: projectPeriodId
		})
		.done(function(countries) {
			$countrySelect.html('<option value="">전체</option>');
			if (countries && countries.length > 0) {
				countries.forEach(function(country) {
					$countrySelect.append('<option value="' + country.id + '">' + (country.name_ko || country.name) + '</option>');
				});
			}
			// 이전에 선택된 값이 있으면 복원 (초기 로드 시에는 저장된 값 사용)
			const valueToRestore = currentCountryId || (typeof initialCountryId !== 'undefined' ? initialCountryId : null);
			if (valueToRestore && $countrySelect.find('option[value="' + valueToRestore + '"]').length > 0) {
				$countrySelect.val(valueToRestore);
			}
		})
		.fail(function(xhr, status, error) {
			$countrySelect.html('<option value="">전체</option>');
		});
	});
	
	// 전체 옵션 로드 함수들 (초기화용 - 기수 선택 해제 시 빈 상태로)
	function loadAllCourses($select, currentValue) {
		$select.html('<option value="">전체</option>');
		if (currentValue) {
			$select.val(currentValue);
		}
	}
	
	function loadAllInstitutions($select, currentValue) {
		$select.html('<option value="">전체</option>');
		if (currentValue) {
			$select.val(currentValue);
		}
	}
	
	function loadAllProjectPeriods($select, currentValue) {
		$select.html('<option value="">전체</option>');
		if (currentValue) {
			$select.val(currentValue);
		}
	}
	
	function loadAllCountries($select, currentValue) {
		$select.html('<option value="">전체</option>');
		if (currentValue) {
			$select.val(currentValue);
		}
	}
	
	// 검색 버튼 클릭 시 필터링된 결과로 캘린더 업데이트
	$('#scheduleFilterForm').on('submit', function(e) {
		e.preventDefault();
		
		const formData = $(this).serialize();
		const url = $(this).attr('action') + '?' + formData + '&year=' + currentYear + '&month=' + (currentMonth + 1);
		
		// 페이지 리로드하여 필터링된 결과 표시
		window.location.href = url;
	});
	
	// 달력 초기화
	function initCalendar() {
		renderCalendar(currentYear, currentMonth);
	}
	
	// 달력 렌더링
	function renderCalendar(year, month) {
		const $tbody = $('#calendarBody');
		$tbody.empty();
		
		// 현재 월 표시
		$('#currentMonth').text(year + '.' + String(month + 1).padStart(2, '0'));
		
		// 해당 월의 첫 날과 마지막 날
		const firstDay = new Date(year, month, 1);
		const lastDay = new Date(year, month + 1, 0);
		const daysInMonth = lastDay.getDate();
		const startDayOfWeek = firstDay.getDay(); // 0(일) ~ 6(토)
		
		// 이전 달의 마지막 날들
		const prevMonthLastDay = new Date(year, month, 0).getDate();
		
		let date = 1;
		let $tr = $('<tr></tr>');
		
		// 첫 주: 이전 달 날짜들
		for (let i = 0; i < startDayOfWeek; i++) {
			const prevDate = prevMonthLastDay - startDayOfWeek + i + 1;
			const $td = $('<td class="other"><span>' + prevDate + '</span></td>');
			$tr.append($td);
		}
		
		// 현재 달 날짜들
		while (date <= daysInMonth) {
			if ($tr.children().length === 7) {
				$tbody.append($tr);
				$tr = $('<tr></tr>');
			}
			
			const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(date).padStart(2, '0');
			const isToday = year === new Date().getFullYear() && 
			               month === new Date().getMonth() && 
			               date === new Date().getDate();
			
			const $td = $('<td' + (isToday ? ' class="today"' : '') + '><span>' + date + '</span></td>');
			const $list = $('<ul class="list"></ul>');
			
			// 해당 날짜의 일정 찾기
			schedules.forEach(function(schedule) {
				if (!schedule.start_date || !schedule.end_date) {
					return;
				}
				
				// 날짜 문자열을 Date 객체로 변환
				const startDateStr = schedule.start_date.split(' ')[0];
				const endDateStr = schedule.end_date.split(' ')[0];
				const currentDateStr = dateStr;
				
				const startDate = new Date(startDateStr + 'T00:00:00');
				const endDate = new Date(endDateStr + 'T00:00:00');
				const currentDate = new Date(currentDateStr + 'T00:00:00');
				
				startDate.setHours(0, 0, 0, 0);
				endDate.setHours(0, 0, 0, 0);
				currentDate.setHours(0, 0, 0, 0);
				
				// 날짜 범위 내에 있는지 확인
				if (currentDate >= startDate && currentDate <= endDate) {
					// 시작일인 경우에만 버튼 추가
					if (currentDate.getTime() === startDate.getTime()) {
						const $li = $('<li class="day' + schedule.day_span + '"><button type="button" title="' + schedule.title + '" onclick="showScheduleModal(' + schedule.id + ')"><strong>' + schedule.title + '</strong>' + (schedule.project_info ? '<p>' + schedule.project_info + '</p>' : '') + '</button></li>');
						$list.append($li);
					}
				}
			});
			
			if ($list.children().length > 0) {
				$td.append($list);
			}
			
			$tr.append($td);
			date++;
		}
		
		// 마지막 주: 다음 달 날짜들
		while ($tr.children().length < 7) {
			const $td = $('<td class="other"><span>' + String(date - daysInMonth).padStart(2, '0') + '</span></td>');
			$tr.append($td);
			date++;
		}
		
		if ($tr.children().length > 0) {
			$tbody.append($tr);
		}
		
		// 달력 처리 로직 적용
		applyCalendarLogic();
	}
	
	// 달력 처리 로직
	function applyCalendarLogic() {
		const tdPadding = (function () {
			const $td = $('.month_area tbody td').first();
			const pl = parseInt($td.css('padding-left')) || 0;
			const pr = parseInt($td.css('padding-right')) || 0;
			return pl + pr;
		})();

		const blankCountMap = new Map();

		function addBlankCount($td) {
			const key = $td[0];
			const cur = blankCountMap.get(key) || 0;
			blankCountMap.set(key, cur + 1);
		}

		function applyBlankElements() {
			blankCountMap.forEach((count, key) => {
				const $td = $(key);
				if (count <= 0) return;

				let $list = $td.find('ul.list');
				if (!$list.length) {
					$td.append('<ul class="list"></ul>');
					$list = $td.find('ul.list');
				}

				for (let i = 0; i < count; i++) {
					$list.prepend('<li class="blank"></li>');
				}
			});
		}

		const originalEvents = $('.month_area .list li[class*="day"]').toArray();

		originalEvents.forEach(function (li) {
			const $li = $(li);
			if (!$li.closest('table').length) return;

			const classStr = $li.attr('class') || '';
			const m = classStr.match(/day(\d+)/);
			if (!m) return;

			let span = parseInt(m[1], 10);
			if (!span || span <= 0) return;

			const $startTd = $li.closest('td');
			const $startTr = $li.closest('tr');
			const $startRowTds = $startTr.children('td');
			const startColIndex = $startRowTds.index($startTd);

			if (startColIndex < 0) return;

			let remaining = span;
			let $row = $startTr;
			let col = startColIndex;
			let firstSegment = true;
			let offsetFromStart = 0;

			while (remaining > 0 && $row.length) {
				const $rowTds = $row.children('td');
				const slotCountInRow = 7 - col;
				const spanHere = Math.min(remaining, slotCountInRow);

				for (let d = 0; d < spanHere; d++) {
					const globalOffset = offsetFromStart + d;
					if (globalOffset === 0) continue;
					const $targetTd = $rowTds.eq(col + d);
					if ($targetTd.length) {
						addBlankCount($targetTd);
					}
				}

				if (firstSegment) {
					if (spanHere !== span) {
						const newClass = classStr.replace(/day\d+/, 'day' + spanHere);
						$li.attr('class', newClass);
					}
					remaining -= spanHere;
					firstSegment = false;
				} else {
					const $targetTd = $rowTds.eq(col);
					if (!$targetTd.length) break;

					const $newLi = $li.clone(false);
					const newClassStr = ($newLi.attr('class') || '').replace(/day\d+/, 'day' + spanHere);
					$newLi.attr('class', newClassStr);
					$newLi.find('button').removeAttr('style');

					let $list = $targetTd.find('ul.list');
					if (!$list.length) {
						$targetTd.append('<ul class="list"></ul>');
						$list = $targetTd.find('ul.list');
					}
					$list.append($newLi);

					remaining -= spanHere;
				}

				offsetFromStart += spanHere;

				if (remaining > 0) {
					$row = $row.next('tr');
					col = 0;
				}
			}
		});

		$('.month_area .list li[class*="day"]').each(function () {
			const $li = $(this);
			const classStr = $li.attr('class') || '';
			const m = classStr.match(/day(\d+)/);
			if (!m) return;

			const n = parseInt(m[1], 10);
			if (!n || n <= 0) return;

			const widthPercent = 100 * n;
			const extraPad = tdPadding * (n - 1);
			const calcWidth = `calc(${widthPercent}% + ${extraPad}px)`;

			const $btn = $li.find('button');
			$btn.css({
				width: calcWidth,
				display: 'block'
			});
		});

		applyBlankElements();
		
		function adjustListItemHeights() {
			$('.month_area tbody td').each(function () {
				const $td = $(this);
				const $lis = $td.find('ul.list > li');

				if ($lis.length === 0) return;

				let maxHeight = 0;
				$lis.each(function () {
					const $btn = $(this).find('button');
					if ($btn.length) {
						const h = $btn.outerHeight();
						if (h > maxHeight) maxHeight = h;
					}
				});

				if (maxHeight === 0) return;
				$lis.css('height', maxHeight + 'px');
			});
		}
		adjustListItemHeights();
	}
	
	// 이전/다음 월 버튼
	$('.date_pick .prev').on('click', function() {
		currentMonth--;
		if (currentMonth < 0) {
			currentMonth = 11;
			currentYear--;
		}
		// URL 업데이트하여 페이지 리로드
		const url = new URL(window.location.href);
		url.searchParams.set('year', currentYear);
		url.searchParams.set('month', currentMonth + 1);
		window.location.href = url.toString();
	});
	
	$('.date_pick .next').on('click', function() {
		currentMonth++;
		if (currentMonth > 11) {
			currentMonth = 0;
			currentYear++;
		}
		// URL 업데이트하여 페이지 리로드
		const url = new URL(window.location.href);
		url.searchParams.set('year', currentYear);
		url.searchParams.set('month', currentMonth + 1);
		window.location.href = url.toString();
	});
	
	// 초기화: 페이지 로드 시 선택된 값 저장
	initialProjectTermId = $('#project_term_id').val();
	initialCourseId = $('#course_id').val();
	initialInstitutionId = $('#operating_institution_id').val();
	initialPeriodId = $('#project_period_id').val();
	initialCountryId = $('#country_id').val();
	
	// 초기화
	initCalendar();
	
	// 초기 로드 시 이미 선택된 값이 있으면 AJAX 호출 없이 유지
	// 서버에서 이미 올바른 옵션들을 렌더링했으므로 추가 AJAX 호출 불필요
	// 사용자가 직접 변경할 때만 AJAX 호출
	
	// 전역 함수: 스케줄 모달 표시
	window.showScheduleModal = function(scheduleId) {
		const schedule = schedules.find(s => s.id == scheduleId);
		if (!schedule) return;
		
		$('#pop_schedule .tit').text(schedule.title);
		$('#pop_schedule .con').html(schedule.content || '');
		layerShow('pop_schedule');
	};
	
	function formatDate(date) {
		return date.getFullYear() + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + String(date.getDate()).padStart(2, '0');
	}
});

// 레이어 팝업 함수
function layerShow(id) {
	$('#' + id).fadeIn();
}

function layerHide(id) {
	$('#' + id).fadeOut();
}
//]]>
</script>
@endpush
@endsection