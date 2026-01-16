@extends('layouts.user')

@section('content')
<div id="mainContent" class="container schedule_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
			<div class="month_area schedule_area nbd">
				<div class="date_pick flex_center">
					<strong id="currentMonth">2025.08</strong>
					<button class="arrow prev" type="button">Prev</button>
					<button class="arrow next" type="button">Next</button>
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
				<div class="day_list mo_vw" id="dayList">
					<!-- 모바일용 일정 리스트가 JavaScript로 동적 생성됨 -->
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
	// 스케줄 데이터
	const schedules = @json($schedules);
	
	// 현재 월 설정 (오늘 날짜 기준)
	let currentDate = new Date();
	let currentYear = currentDate.getFullYear();
	let currentMonth = currentDate.getMonth(); // 0-11
	
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
				
				// 날짜 문자열을 Date 객체로 변환 (YYYY-MM-DD 형식)
				const startDateStr = schedule.start_date.split(' ')[0]; // 시간 부분 제거
				const endDateStr = schedule.end_date.split(' ')[0];
				const currentDateStr = dateStr;
				
				const startDate = new Date(startDateStr + 'T00:00:00');
				const endDate = new Date(endDateStr + 'T00:00:00');
				const currentDate = new Date(currentDateStr + 'T00:00:00');
				
				// 날짜 비교를 위해 시간 부분 제거
				startDate.setHours(0, 0, 0, 0);
				endDate.setHours(0, 0, 0, 0);
				currentDate.setHours(0, 0, 0, 0);
				
				// 날짜 범위 내에 있는지 확인
				if (currentDate >= startDate && currentDate <= endDate) {
					// 시작일인 경우에만 버튼 추가 (중복 방지)
					if (currentDate.getTime() === startDate.getTime()) {
						const $li = $('<li class="day' + schedule.day_span + '"><button type="button" title="' + schedule.title + '" onclick="showScheduleModal(' + schedule.id + ')"><strong>' + schedule.title + '</strong></button></li>');
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
		
		// 모바일용 일정 리스트 생성
		updateDayList(year, month);
	}
	
	// 모바일용 일정 리스트 업데이트
	function updateDayList(year, month) {
		const $dayList = $('#dayList');
		$dayList.empty();
		
		schedules.forEach(function(schedule) {
			if (!schedule.start_date || !schedule.end_date) return;
			
			const startDate = new Date(schedule.start_date);
			const endDate = new Date(schedule.end_date);
			
			// 해당 월에 포함되는 일정만 표시
			if (startDate.getFullYear() === year && startDate.getMonth() === month) {
				const startStr = formatDate(startDate);
				const endStr = formatDate(endDate);
				const dateRange = startStr === endStr ? startStr : startStr + ' ~ ' + endStr;
				
				const $item = $('<a href="#this" onclick="showScheduleModal(' + schedule.id + '); return false;"><strong>' + dateRange + '</strong><p>' + schedule.title + '</p></a>');
				$dayList.append($item);
			}
		});
	}
	
	// 날짜 포맷팅
	function formatDate(date) {
		return date.getFullYear() + '.' + String(date.getMonth() + 1).padStart(2, '0') + '.' + String(date.getDate()).padStart(2, '0');
	}
	
	// 달력 처리 로직 (메인 페이지와 동일)
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
		
		// 높이 조정
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
		renderCalendar(currentYear, currentMonth);
	});
	
	$('.date_pick .next').on('click', function() {
		currentMonth++;
		if (currentMonth > 11) {
			currentMonth = 0;
			currentYear++;
		}
		renderCalendar(currentYear, currentMonth);
	});
	
	// 초기화
	initCalendar();
	
	// 전역 함수: 스케줄 모달 표시
	window.showScheduleModal = function(scheduleId) {
		const schedule = schedules.find(s => s.id == scheduleId);
		if (!schedule) return;
		
		$('#pop_schedule .tit').text(schedule.title);
		$('#pop_schedule .con').html(schedule.content || '');
		layerShow('pop_schedule');
	};
});
//]]>
</script>
@endpush

@include('components.user-footer')
@endsection
