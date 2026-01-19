@extends('layouts.user')

@section('content')
<div id="mainContent" class="container dashboard_wrap">
	@include('components.user-header')
	<div class="contents">
	
		@if(isset($topNotice) && $topNotice)
		<div class="dashboard_notice">
			<div class="tit">Notice</div>
			<div class="con">
				<a href="#this">{{ $topNotice->content }}</a>
			</div>
		</div>
		@endif
		
		<div class="wbox">
			<div class="itit ico_sch">Schedule</div>
			<ol class="step_area">
				<li class="on chk"><i>1</i><span></span>STEP 1<strong>Preparing for Entry</strong></li>
				<li class="on"><i>2</i><span>STEP 2</span><strong>Document Verification after Entry</strong></li>
				<li><i>3</i><span>STEP 3</span><strong>Health Checkup</strong></li>
				<li><i>4</i><span>STEP 4</span><strong>Opening an Account</strong></li>
				<li><i>5</i><span>STEP 5</span><strong>Cultural Experience</strong></li>
			</ol>
		</div>
		
		<div class="dash_cont mc01">
			<div class="left">
				<div class="wbox left_half">
					<div class="itit s ico_date">Entry/Departure Dates
						@if($memberModel && $memberModel->ticket_file)
							<a href="{{ route('ticket-file.download') }}" class="btn" target="_blank">Check Flight Ticket</a>
						@else
							<a href="#this" class="btn" onclick="alert('항공권 파일이 등록되지 않았습니다.'); return false;">Check Flight Ticket</a>
						@endif
					</div>
					<div class="date_beaf">
						<div class="gbox"><div class="tt">Entry Date</div><strong>{{ $memberModel && $memberModel->entry_date ? $memberModel->entry_date->format('Y-m-d') : '-' }}</strong></div>
						<div class="gbox"><div class="tt">Departure Dates</div><strong>{{ $memberModel && $memberModel->exit_date ? $memberModel->exit_date->format('Y-m-d') : '-' }}</strong></div>
					</div>
					<p class="c_red">*Please check your flight ticket before entering the country.</p>
				</div>
				<div class="wbox left_half">
					<div class="itit s ico_alert">Enter Personal Information<a href="{{ route('mypage') }}" class="btn">Enter</a></div>
					<div class="alert_area">
						<p>For smooth project progress, please enter your personal information on My Page.</p>
					</div>
				</div>
			</div>
			<div class="wbox right">
				<div class="itit ico_document">Document Submission</div>
				<div class="tbl state_tbl">
					<table>
						<colgroup>
							<col class="w90">
							<col>
							<col class="w100">
							<col class="w100">
							<col class="w100">
							<col class="w100">
						</colgroup>
						<thead>
							<tr>
								<th>Status</th>
								<th>Documents</th>
								<th>Submission Deadline</th>
								<th>Notes</th>
								<th>Reason for Document Supplement</th>
								<th>Submission</th>
							</tr>
						</thead>
						<tbody>
							@php
								// MemberDocument가 있으면 MemberDocument 사용, 없으면 Country 정보 사용
								$memberDocument = $memberDocuments && $memberDocuments->count() > 0 ? $memberDocuments->first() : null;
								$displayDocument = $memberDocument ?: $countryDocument;
								$documentName = $memberDocument ? $memberDocument->document_name : ($countryDocument ? $countryDocument->document_name : null);
								$submissionDeadline = $memberDocument ? $memberDocument->submission_deadline : ($countryDocument ? $countryDocument->submission_deadline : null);
							@endphp
							<tr>
								<td>
									@if($memberDocument)
										@if($memberDocument->status == 'submitted' || $memberDocument->status == 'resubmitted')
											<strong class="state submitted">Submission</strong>
										@elseif($memberDocument->status == 'supplement_requested')
											<strong class="state back">Rejection</strong>
										@elseif($memberDocument->status == 'not_submitted')
											<strong class="state not_submitted">Not submitted</strong>
										@else
											<strong class="state not_submitted">Not submitted</strong>
										@endif
									@else
										<strong class="state not_submitted">Not submitted</strong>
									@endif
								</td>
								<td>
									@if($documentName)
										{{ $documentName }}
									@else
										-
									@endif
								</td>
								<td>
									@if($submissionDeadline)
										@if($submissionDeadline instanceof \Carbon\Carbon)
											{{ $submissionDeadline->format('Y-m-d') }}
										@else
											{{ \Carbon\Carbon::parse($submissionDeadline)->format('Y-m-d') }}
										@endif
									@else
										-
									@endif
								</td>
								<td><a href="{{ route('note.show') }}" class="btn" onclick="return checkNotesLink({{ $memberId ?? 0 }});">Confirm</a></td>
								<td>
									@if($memberDocument && $memberDocument->id)
										<button type="button" onclick="showSupplementReason({{ $memberDocument->id }}, '{{ $memberDocument->supplement_request_content ? addslashes($memberDocument->supplement_request_content) : '' }}');" class="btn">Confirm</button>
									@else
										<button type="button" onclick="showSupplementReason(0, '');" class="btn">Confirm</button>
									@endif
								</td>
								<td>
									@if($memberDocument && $memberDocument->id)
										<button type="button" onclick="showDocumentUpload({{ $memberDocument->id }}, '{{ $documentName ? addslashes($documentName) : '' }}');" class="btn">Supplement</button>
									@else
										<button type="button" onclick="showDocumentUpload(0, '{{ $documentName ? addslashes($documentName) : '' }}');" class="btn">Supplement</button>
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="dash_cont mc02">
			<div class="wbox left month_wrap">
				<div class="itit ico_month" id="currentMonth">2025.08
					<div class="btns">
						<button type="button" class="btn prev">Prev</button>
						<button type="button" class="btn next">Next</button>
					</div>
				</div>
				<div class="month_area">
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
				<div class="day_list mo_vw" id="dayList">
					<!-- 모바일용 일정 리스트가 JavaScript로 동적 생성됨 -->
				</div>
			</div>
			<div class="right">
			<div class="wbox w100p">
				<div class="itit ico_notice">Notice <a href="{{ route('home') }}" class="more">View More</a></div>
				@if($noticePosts && $noticePosts->count() > 0)
				<ul class="dash_board">
					@foreach($noticePosts as $post)
					<li>
						<a href="#this">
							<strong>{{ $post->title }}</strong>
							@if($post->content)
							<p class="con mo_vw">{{ Str::limit(strip_tags($post->content), 50) }}</p>
							@endif
							@php
								$date = \Carbon\Carbon::parse($post->created_at);
								$year = $date->format('Y');
								$month = $date->format('m');
								$day = $date->format('d');
							@endphp
							<p class="date">{{ $year }}.{{ $month }}<i class="pc_vw">.</i><b>{{ $day }}</b></p>
						</a>
					</li>
					@endforeach
				</ul>
				@else
				<ul class="dash_board">
					<li>
						<a href="#this">
							<strong>등록된 공지사항이 없습니다.</strong>
						</a>
					</li>
				</ul>
				@endif
			</div>
				<div class="wbox w100p">
					<div class="itit ico_notice">Latest News <a href="{{ route('home') }}" class="more">View More</a></div>
					<div class="jq_tabonoff dash_board_wrap">
						<ul class="jq_tab tabs">
							<li><button type="button">All</button></li>
							<li><button type="button">Main News</button></li>
							<li><button type="button">Lifestyle</button></li>
							<li><button type="button">Fashion</button></li>
							<li><button type="button">Entertainment</button></li>
						</ul>
						<div class="jq_cont">
							<div class="cont">
								<ul class="dash_board">
									<li>
										<a href="#this">
											<span class="type">Main News</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Lifestyle</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Fashion</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
								</ul>
							</div>
							<div class="cont">
								<ul class="dash_board">
									<li>
										<a href="#this">
											<span class="type">Main News</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Main News</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Main News</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
								</ul>
							</div>
							<div class="cont">
								<ul class="dash_board">
									<li>
										<a href="#this">
											<span class="type">Lifestyle</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Lifestyle</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Lifestyle</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
								</ul>
							</div>
							<div class="cont">
								<ul class="dash_board">
									<li>
										<a href="#this">
											<span class="type">Fashion</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Fashion</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Fashion</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
								</ul>
							</div>
							<div class="cont">
								<ul class="dash_board">
									<li>
										<a href="#this">
											<span class="type">Entertainment</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Entertainment</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
									<li>
										<a href="#this">
											<span class="type">Entertainment</span>
											<strong>제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.제목입니다.</strong>
											<p class="con mo_vw">내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.내용입니다.</p>
											<p class="date">2025.01<i class="pc_vw">.</i><b>01</b></p>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="wbox w100p inquiries_area">
					<div class="tit">Contact Us</div>
					<p>Do you have any questions about your training, including entry, accommodation, or documentation?<br/>Please check our Frequently Asked Questions (FAQ) and feel free to contact us anytime.</p>
					<a href="{{ route('inquiries') }}" class="btn flex_center">Contact Us</a>
				</div>
			</div>
		</div>
		
	</div>
</div>

@include('components.popup-reason')
@include('components.popup-supplement')

@push('styles')
<link rel="stylesheet" href="/pub/css/slick.css" media="all">
@endpush

@push('scripts')
<script src="/pub/js/slick.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready (function () {
//달력
$(function () {
	// 스케줄 데이터
	const schedules = @json($schedules ?? []);
	
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
						const $li = $('<li class="day' + schedule.day_span + '"><button type="button" title="' + schedule.title + '"><strong>' + schedule.title + '</strong></button></li>');
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
				
				const $item = $('<a href="#this"><strong>' + dateRange + '</strong><p>' + schedule.title + '</p></a>');
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
	$('.month_wrap .prev').on('click', function() {
		currentMonth--;
		if (currentMonth < 0) {
			currentMonth = 11;
			currentYear--;
		}
		renderCalendar(currentYear, currentMonth);
	});
	
	$('.month_wrap .next').on('click', function() {
		currentMonth++;
		if (currentMonth > 11) {
			currentMonth = 0;
			currentYear++;
		}
		renderCalendar(currentYear, currentMonth);
	});
	
	// 초기화
	initCalendar();
});

//최신뉴스 탭
	$('.jq_tabonoff>.jq_cont').children().css('display', 'none');
	$('.jq_tabonoff>.jq_cont .cont:first-child').css('display', 'block');
	$('.jq_tabonoff>.jq_tab > li:first-child').addClass('on');

	$('.jq_tabonoff').delegate('.jq_tab>li', 'click', function() {
		var index = $(this).parent().children().index(this);
		$(this).siblings().removeClass('on');
		$(this).addClass('on');
		$(this).parent().next('.jq_cont').children().hide().eq(index).show();
	});
	
	// Notes 링크 체크
	window.checkNotesLink = function(memberId) {
		if (!memberId || memberId === 0) {
			alert('회원 정보가 없습니다.');
			return false;
		}
		return true;
	};
});
//]]>
</script>
@endpush

@include('components.user-footer')
@endsection
