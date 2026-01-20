@extends('layouts.kofih')

@section('title', '회원')

@php
$gNum = '01';
$gName = '회원';
@endphp

@section('content')
<div id="mainContent" class="container kofih_wrap">
	@include('components.kofih-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="/" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
		
		<div class="wbox">
		
			<div class="ntit"><span>1</span>내 정보</div>
			<div class="board_write tbl border break_write">
				<table>
					<colgroup>
						<col class="w170">
						<col>
						<col class="w170">
						<col>
					</colgroup>
					<tr>
						<th>이름</th>
						<td>{{ $member->name ?? '-' }}</td>
						<th>프로젝트 기수</th>
						<td>
							@if($member->projectTerm && $member->operatingInstitution && $member->course && $member->projectPeriod && $member->country)
								{{ $member->projectTerm->name_ko ?? $member->projectTerm->name }} / {{ $member->operatingInstitution->name_ko ?? $member->operatingInstitution->name }} / {{ $member->course->name_ko ?? $member->course->name }} / {{ $member->country->name_ko ?? $member->country->name }}
							@else
								-
							@endif
						</td>
					</tr>
					<tr>
						<th>성별</th>
						<td>
							@if($member->gender == 'male')
								남자
							@elseif($member->gender == 'female')
								여자
							@else
								-
							@endif
						</td>
						<th>직업</th>
						<td>{{ $member->occupation ?? '-' }}</td>
					</tr>
					<tr>
						<th>생년월일<span class="c_red">*</span></th>
						<td>{{ $member->birth_date ? $member->birth_date->format('Y.m.d') : '-' }}</td>
						<th>전공</th>
						<td>{{ $member->major ?? '-' }}</td>
					</tr>
					<tr>
						<th>현지 전화번호</th>
						<td>{{ $member->phone_local ?? '-' }}</td>
						<th>소속</th>
						<td>{{ $member->affiliation ?? '-' }}</td>
					</tr>
					<tr>
						<th>한국 전화번호</th>
						<td>{{ $member->phone_kr ?? '-' }}</td>
						<th>부서</th>
						<td>{{ $member->department ?? '-' }}</td>
					</tr>
					<tr>
						<th>이메일</th>
						<td>{{ $member->email ?? '-' }}</td>
						<th>직위</th>
						<td>{{ $member->position ?? '-' }}</td>
					</tr>
					<tr>
						<th>여권번호<span class="c_red">*</span></th>
						<td>{{ $member->passport_number ?? '-' }}</td>
						<th>외국인등록번호</th>
						<td>{{ $member->alien_registration_number ?? '-' }}</td>
					</tr>
					<tr>
						<th>여권유효기간<span class="c_red">*</span></th>
						<td>{{ $member->passport_expiry ? $member->passport_expiry->format('Y.m.d') : '-' }}</td>
						<th>외국인등록증 유효기간</th>
						<td>{{ $member->alien_registration_expiry ? $member->alien_registration_expiry->format('Y.m.d') : '-' }}</td>
					</tr>
				</table>
			</div>
		
			<div class="ntit"><span>2</span>연수 관련정보</div>
			<div class="board_write tbl border break_write">
				<table>
					<colgroup>
						<col class="w170">
						<col>
						<col class="w170">
						<col>
					</colgroup>
					<tr>
						<th>호텔</th>
						<td>{{ $member->hotel_name ?? '-' }}</td>
						<th>연수기간</th>
						<td>{{ $member->training_period ?? '-' }}</td>
					</tr>
					<tr>
						<th>객실번호</th>
						<td>{{ $member->hotel_address_detail ?? '-' }}</td>
						<th>비자종류</th>
						<td>{{ $member->visa_type ?? '-' }}</td>
					</tr>
					<tr>
						<th>문화체험</th>
						<td>{{ $member->cultural_experience ?? '-' }}</td>
						<th>옷 사이즈<span class="c_red">*</span></th>
						<td>{{ $member->clothing_size ?? '-' }}</td>
					</tr>
					<tr>
						<th>계좌번호</th>
						<td>{{ $member->account_info ?? '-' }}</td>
						<th>특이식성</th>
						<td>{{ $member->dietary_restrictions ?? '-' }}</td>
					</tr>
					<tr>
						<th>보험가입여부</th>
						<td>{{ $member->insurance_status ?? '-' }}</td>
						<th>특이사항 및 요청사항</th>
						<td>{{ $member->special_requests ?? '-' }}</td>
					</tr>
				</table>
			</div>
		
			<div class="ntit"><span>3</span>입출국 정보</div>
			<div class="board_write tbl border break_write">
				<table>
					<colgroup>
						<col class="w170">
						<col>
						<col class="w170">
						<col>
					</colgroup>
					<tr>
						<th>항공권</th>
						<td colspan="3">
							<div class="flex gap32">
								@if($member->ticket_file)
									<span>{{ basename($member->ticket_file) }}</span>
								@else
									<span>-</span>
								@endif
								<p class="c_red">*입국 전 반드시 항공권을 확인하여 주세요.</p>
							</div>
						</td>
					</tr>
					<tr>
						<th>출발지</th>
						<td>{{ $member->departure_location ?? '-' }}</td>
						<th>도착지</th>
						<td>{{ $member->arrival_location ?? '-' }}</td>
					</tr>
					<tr>
						<th>입국일자</th>
						<td>{{ $member->entry_date ? $member->entry_date->format('Y/m/d') : '-' }}</td>
						<th>출국일자</th>
						<td>{{ $member->exit_date ? $member->exit_date->format('Y/m/d') : '-' }}</td>
					</tr>
					<tr>
						<th>계좌번호</th>
						<td>{{ $member->account_info ?? '-' }}</td>
						<th>출국 항공편</th>
						<td>{{ $member->exit_flight ?? '-' }}</td>
					</tr>
				</table>
			</div>
			
			<div class="board_bottom flex_center">
				<button type="button" class="btn_submit">저장하기</button>
			</div>
		</div>
		
	</div>
</div>

@push('scripts')
<script src="//code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
//달력
	$(".datepicker").datepicker({
		dateFormat: 'yy.mm.dd',
		showMonthAfterYear: true,
		showOn: "focus",  // 텍스트 필드를 클릭하면 달력이 열림
		changeYear: true,
		changeMonth: true,
		yearRange: 'c-100:c+10',
		yearSuffix: "년 ",
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin: ['일','월','화','수','목','금','토']
	});
});
//]]>
</script>
@endpush
@endsection