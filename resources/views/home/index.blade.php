@extends('layouts.user')

@section('content')
<div id="mainContent" class="container dashboard_wrap">
	@include('components.user-header')
	<div class="contents">
	
		@if(isset($topNotice) && $topNotice)
		<div class="dashboard_notice">
			<div class="tit">Notice</div>
			<div class="con">
				<a href="#this">{{ $topNotice->title }}</a>
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
					<div class="itit s ico_date">Entry/Departure Dates<a href="#this" class="btn">Check Flight Ticket</a></div>
					<div class="date_beaf">
						<div class="gbox"><div class="tt">Entry Date</div><strong>2025-01-01</strong></div>
						<div class="gbox"><div class="tt">Departure Dates</div><strong>2025-01-01</strong></div>
					</div>
					<p class="c_red">*Please check your flight ticket before entering the country.</p>
				</div>
				<div class="wbox left_half">
					<div class="itit s ico_alert">Enter Personal Information<a href="{{ route('home') }}" class="btn">Enter</a></div>
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
							<tr>
								<td><strong class="state back">Rejection</strong></td>
								<td>Preparing for Entry</td>
								<td>2025-01-01</td>
								<td><a href="{{ route('home') }}" class="btn">Confirm</a></td>
								<td><button type="button" onclick="layerShow('pop_supplement');" class="btn">Confirm</button></td>
								<td><button type="button" onclick="layerShow('pop_reason');" class="btn">Supplement</button></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="dash_cont mc02">
			<div class="wbox left month_wrap">
				<div class="itit ico_month">2025.08
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
						<tbody>
							<tr>
								<td class="other"><span>27</span></td>
								<td class="other"><span>28</span></td>
								<td class="other"><span>29</span></td>
								<td class="other"><span>30</span></td>
								<td class="other"><span>31</span></td>
								<td><span>01</span>
									<ul class="list">
										<li class="day1"><button type="button" title="자체건강검진">자체건강검진</button></li>
									</ul>
								</td>
								<td><span>02</span></td>
							</tr>
							<tr>
								<td><span>03</span></td>
								<td><span>04</span></td>
								<td><span>05</span>
									<ul class="list">
										<li class="day1"><button type="button" title="계좌개설">계좌개설</button></li>
									</ul>
								</td>
								<td><span>06</span></td>
								<td><span>07</span></td>
								<td><span>08</span></td>
								<td><span>09</span></td>
							</tr>
							<tr>
								<td><span>10</span></td>
								<td><span>11</span></td>
								<td><span>12</span>
									<ul class="list">
										<li class="day4"><button type="button" title="경주 문화체험">경주 문화체험</button></li>
									</ul>
								</td>
								<td><span>13</span>
									<ul class="list">
										<li class="day5"><button type="button" title="경주 문화체험">경주 문화체험</button></li>
									</ul>
								</td>
								<td><span>14</span></td>
								<td><span>15</span></td>
								<td><span>16</span></td>
							</tr>
							<tr>
								<td><span>17</span></td>
								<td><span>18</span></td>
								<td><span>19</span></td>
								<td><span>20</span></td>
								<td class="today"><span>21</span></td>
								<td><span>22</span></td>
								<td><span>23</span></td>
							</tr>
							<tr>
								<td><span>24</span></td>
								<td><span>25</span></td>
								<td><span>26</span></td>
								<td><span>27</span></td>
								<td><span>28</span></td>
								<td><span>29</span></td>
								<td><span>30</span></td>
							</tr>
							<tr>
								<td><span>31</span></td>
								<td class="other"><span>01</span></td>
								<td class="other"><span>02</span></td>
								<td class="other"><span>03</span></td>
								<td class="other"><span>04</span></td>
								<td class="other"><span>05</span></td>
								<td class="other"><span>06</span></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="day_list mo_vw">
					<a href="#this"><strong>2025.08.01</strong><p>자체건강검진</p></a>
					<a href="#this"><strong>2025.08.05</strong><p>계좌계설</p></a>
					<a href="#this"><strong>2025.08.12 ~ 2025.08.15</strong><p>경주 문화체험</p></a>
					<a href="#this"><strong>2025.08.13 ~ 2025.08.17</strong><p>경주 문화체험</p></a>
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
					<a href="{{ route('home') }}" class="btn flex_center">Contact Us</a>
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

    // 1) td 좌우 padding 합 구하기
    const tdPadding = (function () {
        const $td = $('.month_area tbody td').first();
        const pl = parseInt($td.css('padding-left')) || 0;
        const pr = parseInt($td.css('padding-right')) || 0;
        return pl + pr;
    })();

    // td별 blank 누적 카운트 저장용
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

            // 필요 개수만큼 blank 누적 prepend
            for (let i = 0; i < count; i++) {
                $list.prepend('<li class="blank"></li>');
            }
        });
    }

    // ---------------------------------------------------
    // 2) "원래 있던 dayN 일정" 기반으로
    //    - 주 넘김 분할
    //    - 전체 스팬에 대한 blankCount 계산을 동시에 처리
    // ---------------------------------------------------
    const originalEvents = $('.month_area .list li[class*="day"]').toArray();

    originalEvents.forEach(function (li) {
        const $li = $(li);

        // 이미 다른 곳으로 옮겨졌으면 무시
        if (!$li.closest('table').length) return;

        const classStr = $li.attr('class') || '';
        const m = classStr.match(/day(\d+)/);
        if (!m) return;

        let span = parseInt(m[1], 10); // day8 → 8
        if (!span || span <= 0) return;

        const $startTd = $li.closest('td');
        const $startTr = $li.closest('tr');
        const $startRowTds = $startTr.children('td');
        const startColIndex = $startRowTds.index($startTd);

        if (startColIndex < 0) return;

        // 분할 및 blank 계산용 변수
        let remaining = span;
        let $row = $startTr;
        let col = startColIndex;
        let firstSegment = true;
        let offsetFromStart = 0; // 이벤트 시작일부터의 총 이동 일수

        while (remaining > 0 && $row.length) {
            const $rowTds = $row.children('td');
            const slotCountInRow = 7 - col; // 이 주에서 남은 칸 수
            const spanHere = Math.min(remaining, slotCountInRow);

            // ---- [A] 이 구간에서 blankCount 계산 ----
            // 전체 스팬 기준: 시작일 다음날부터 N-1일까지 blank +1
            for (let d = 0; d < spanHere; d++) {
                const globalOffset = offsetFromStart + d;
                if (globalOffset === 0) {
                    // 시작일은 실제 dayN 버튼이 들어가는 칸이므로 blank 생성 X
                    continue;
                }
                const $targetTd = $rowTds.eq(col + d);
                if ($targetTd.length) {
                    addBlankCount($targetTd);
                }
            }

            // ---- [B] dayN li 분할 처리 ----
            if (firstSegment) {
                // 첫 번째 조각: 기존 li 의 dayN 을 spanHere 으로 변경
                if (spanHere !== span) {
                    const newClass = classStr.replace(/day\d+/, 'day' + spanHere);
                    $li.attr('class', newClass);
                }
                remaining -= spanHere;
                firstSegment = false;
            } else {
                // 이후 조각: li 복제해서 다른 주에 배치
                const $targetTd = $rowTds.eq(col);
                if (!$targetTd.length) break;

                const $newLi = $li.clone(false);
                const newClassStr = ($newLi.attr('class') || '').replace(/day\d+/, 'day' + spanHere);
                $newLi.attr('class', newClassStr);
                // width 스타일은 뒤에서 다시 계산하므로 제거
                $newLi.find('button').removeAttr('style');

                let $list = $targetTd.find('ul.list');
                if (!$list.length) {
                    $targetTd.append('<ul class="list"></ul>');
                    $list = $targetTd.find('ul.list');
                }
                $list.append($newLi);

                remaining -= spanHere;
            }

            // 다음 구간을 위해 offset/row/col 갱신
            offsetFromStart += spanHere;

            if (remaining > 0) {
                $row = $row.next('tr');
                col = 0; // 다음 주는 항상 첫번째 칸부터
            }
        }
    });

    // ---------------------------------------------------
    // 3) 모든 dayN 들에 대해 버튼 width 계산 (요구사항 1)
    // ---------------------------------------------------
    $('.month_area .list li[class*="day"]').each(function () {
        const $li = $(this);
        const classStr = $li.attr('class') || '';
        const m = classStr.match(/day(\d+)/);
        if (!m) return;

        const n = parseInt(m[1], 10); // dayN 의 N
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

    // ---------------------------------------------------
    // 4) 계산된 blankCountMap 을 실제 DOM 에 반영
    // ---------------------------------------------------
    applyBlankElements();
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
});
//]]>
</script>
@endpush

@include('components.user-footer')
@endsection
