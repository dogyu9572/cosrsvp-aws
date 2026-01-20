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
		
		<div class="dashboard_kofih_wrap">
			<div class="wbox">
				<div class="btit">검색</div>
				<form method="GET" action="{{ route('kofih.member.index') }}" id="memberFilterForm">
					<div class="selects">
						<div class="flex">
							<dl>
								<dt class="tt">프로젝트 기수</dt>
								<dd>
									<select name="filter_project_term_id" id="filter_project_term_id" class="project-term-filter" data-level="term">
										<option value="">기수</option>
										@foreach($projectTerms ?? [] as $term)
											<option value="{{ $term->id }}" @selected(request('filter_project_term_id') == $term->id)>{{ $term->name }}</option>
										@endforeach
									</select>
									<select name="filter_course_id" id="filter_course_id" class="project-term-filter" data-level="course" data-selected="{{ request('filter_course_id') }}">
										<option value="">과정</option>
									</select>
									<select name="filter_operating_institution_id" id="filter_operating_institution_id" class="project-term-filter" data-level="institution" data-selected="{{ request('filter_operating_institution_id') }}">
										<option value="">운영기관</option>
									</select>
									<select name="filter_project_period_id" id="filter_project_period_id" class="project-term-filter" data-level="period" data-selected="{{ request('filter_project_period_id') }}">
										<option value="">프로젝트기간</option>
									</select>
									<select name="filter_country_id" id="filter_country_id" class="project-term-filter" data-level="country" data-selected="{{ request('filter_country_id') }}">
										<option value="">국가</option>
									</select>
								</dd>
							</dl>
							<dl>
								<dt>비고</dt>
								<dd>
									<select name="note_status" id="note_status">
										<option value="">전체</option>
										<option value="urgent" @selected(request('note_status') == 'urgent')>긴급</option>
										<option value="caution" @selected(request('note_status') == 'caution')>주의</option>
										<option value="normal" @selected(request('note_status') == 'normal')>정상</option>
									</select> 
								</dd> 
							</dl> 
						</div> 
						<div class="flex"> 
							<dl> 
								<dt class="tt">검색</dt> 
								<dd class="select_area"> 
									<select name="search_type" id="search_type"> 
										<option value="">전체</option>
										<option value="name" @selected(request('search_type') == 'name')>회원명</option>
										<option value="email" @selected(request('search_type') == 'email')>이메일</option>
										<option value="phone" @selected(request('search_type') == 'phone')>휴대폰</option>
										<option value="passport" @selected(request('search_type') == 'passport')>여권번호</option>
										<option value="alien_registration" @selected(request('search_type') == 'alien_registration')>외국인등록번호</option>
									</select> 
									<input type="text" name="keyword" class="text" placeholder="검색어를 입력해주세요." value="{{ request('keyword') }}"> 
									<button type="submit" class="btn">조회</button> 
								</dd> 
							</dl> 
						</div> 
					</div> 
					<div class="btns_search_area flex_center"> 
						<button type="button" class="btn btn_re" onclick="location.href='{{ route('kofih.member.index') }}'">초기화</button> 
						<button type="submit" class="btn btn_search">검색</button> 
					</div>
				</form>
			</div>
			
			<div class="wbox">
				<div class="tbl break_notebook">
					<table>
						<colgroup>
							<col class="w110">
							<col>
							<col class="w200">
							<col class="w220">
							<col class="w170">
							<col class="w110">
							<col class="w110">
							<col class="w100">
							<col class="w100">
							<col class="w100">
						</colgroup>
						<thead>
							<tr>
								<th>비고</th>
								<th>프로젝트 기수</th>
								<th>회원명</th>
								<th>이메일</th>
								<th>휴대폰</th>
								<th>가입일</th>
								<th>입국</th>
								<th>회원비고</th>
								<th>알림</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody>
							@forelse($members ?? [] as $member)
								@php
									// 프로젝트 기수 표시 형식: {연도} / {과정명} / {운영기관} / {기간} / {국가}
									$projectInfo = [];
									if ($member->projectTerm) {
										$projectInfo[] = $member->projectTerm->name;
									}
									if ($member->course) {
										$projectInfo[] = $member->course->name_ko ?? $member->course->name;
									}
									if ($member->operatingInstitution) {
										$projectInfo[] = $member->operatingInstitution->name_ko ?? $member->operatingInstitution->name;
									}
									if ($member->projectPeriod) {
										$projectInfo[] = $member->projectPeriod->name_ko ?? '';
									}
									if ($member->country) {
										$projectInfo[] = $member->country->name_ko ?? '';
									}
									$projectInfoStr = !empty($projectInfo) ? implode(' / ', $projectInfo) : '-';
									
									// 입국일 D-XX 계산
									$entryDateStr = '-';
									if ($member->entry_date) {
										$today = now()->startOfDay();
										$entryDate = \Carbon\Carbon::parse($member->entry_date)->startOfDay();
										$diffDays = (int) $today->diffInDays($entryDate, false);
										if ($diffDays > 0) {
											$entryDateStr = 'D-' . $diffDays;
										} elseif ($diffDays < 0) {
											$entryDateStr = 'D+' . abs($diffDays);
										} else {
											$entryDateStr = 'D-Day';
										}
									}
									
									// 비고 상태 (초기에는 정상으로 처리)
									$noteStatus = 'normal'; // 추후 Alert 데이터 연동
								@endphp
								<tr>
									<td class="state">
										@if($noteStatus == 'urgent')
											<i class="urgent2">긴급</i>
										@elseif($noteStatus == 'caution')
											<i class="urgent1">주의</i>
										@else
											<i class="urgent0">정상</i>
										@endif
									</td>
									<td class="tb_mem01">{{ $projectInfoStr }}</td>
									<td class="tb_mem02">{{ $member->name }}</td>
									<td class="tb_mem03">{{ $member->email ?: '-' }}</td>
									<td class="tb_mem04">{{ $member->phone_kr ?: ($member->phone_local ?: '-') }}</td>
									<td class="tb_mem05">{{ $member->created_at->format('Y.m.d') }}</td>
									<td class="tb_mem06">{{ $entryDateStr }}</td>
									<td class="tb_mem07"><a href="{{ route('kofih.member-notes.index', ['member_id' => $member->id]) }}" class="btn">보기</a></td>
									<td class="tb_mem08"><a href="{{ route('kofih.alerts.index', ['member_id' => $member->id]) }}" class="btn">보기</a></td>
									<td class="tb_mem09"><a href="{{ route('kofih.mypage.index', ['member_id' => $member->id]) }}" class="btn">상세</a></td>
								</tr>
							@empty
								<tr>
									<td colspan="10" class="text-center">등록된 회원이 없습니다.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
				
				@if(isset($members) && $members->hasPages())
					<div class="pagination-wrap" style="margin-top: 20px; text-align: center;">
						{{ $members->links() }}
					</div>
				@endif
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
	// 프로젝트 기수 계층적 필터링 (백오피스와 동일한 로직)
	$('.project-term-filter').on('change', function() {
		var level = $(this).data('level');
		var selectedId = $(this).val();
		var $form = $('#memberFilterForm');
		
		// 하위 필터 초기화
		if (level === 'term') {
			$('#filter_course_id, #filter_operating_institution_id, #filter_project_period_id, #filter_country_id').html('<option value="">로딩중...</option>');
		} else if (level === 'course') {
			$('#filter_operating_institution_id, #filter_project_period_id, #filter_country_id').html('<option value="">로딩중...</option>');
		} else if (level === 'institution') {
			$('#filter_project_period_id, #filter_country_id').html('<option value="">로딩중...</option>');
		} else if (level === 'period') {
			$('#filter_country_id').html('<option value="">로딩중...</option>');
		}
		
		if (!selectedId) {
			// 선택 해제 시 기본 옵션으로 복원
			if (level === 'term') {
				$('#filter_course_id').html('<option value="">과정</option>');
				$('#filter_operating_institution_id').html('<option value="">운영기관</option>');
				$('#filter_project_period_id').html('<option value="">프로젝트기간</option>');
				$('#filter_country_id').html('<option value="">국가</option>');
			} else if (level === 'course') {
				$('#filter_operating_institution_id').html('<option value="">운영기관</option>');
				$('#filter_project_period_id').html('<option value="">프로젝트기간</option>');
				$('#filter_country_id').html('<option value="">국가</option>');
			} else if (level === 'institution') {
				$('#filter_project_period_id').html('<option value="">프로젝트기간</option>');
				$('#filter_country_id').html('<option value="">국가</option>');
			} else if (level === 'period') {
				$('#filter_country_id').html('<option value="">국가</option>');
			}
			return;
		}
		
		// AJAX로 하위 옵션 로드
		var url = '';
		var data = {};
		
		if (level === 'term') {
			url = '/backoffice/courses/get-by-term/' + selectedId;
		} else if (level === 'course') {
			url = '/backoffice/operating-institutions/get-by-course/' + selectedId;
			data = { filter_project_term_id: $('#filter_project_term_id').val() };
		} else if (level === 'institution') {
			url = '/backoffice/project-periods/get-by-institution/' + selectedId;
			data = { 
				filter_project_term_id: $('#filter_project_term_id').val(),
				filter_course_id: $('#filter_course_id').val()
			};
		} else if (level === 'period') {
			url = '/backoffice/countries/get-by-period/' + selectedId;
			data = { 
				filter_project_term_id: $('#filter_project_term_id').val(),
				filter_course_id: $('#filter_course_id').val(),
				filter_operating_institution_id: $('#filter_operating_institution_id').val()
			};
		}
		
		if (url) {
			$.ajax({
				url: url,
				method: 'GET',
				data: data,
				success: function(response) {
					if (level === 'term') {
						var html = '<option value="">과정</option>';
						response.data.forEach(function(item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						$('#filter_course_id').html(html);
					} else if (level === 'course') {
						var html = '<option value="">운영기관</option>';
						response.data.forEach(function(item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						$('#filter_operating_institution_id').html(html);
					} else if (level === 'period') {
						var html = '<option value="">국가</option>';
						response.data.forEach(function(item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						$('#filter_country_id').html(html);
					} else if (level === 'institution') {
						var html = '<option value="">프로젝트기간</option>';
						response.data.forEach(function(item) {
							html += '<option value="' + item.id + '">' + item.name + '</option>';
						});
						$('#filter_project_period_id').html(html);
					}
					
					// 저장된 선택값 복원
					var selectedValue = $('#' + (level === 'term' ? 'filter_course_id' : level === 'course' ? 'filter_operating_institution_id' : level === 'institution' ? 'filter_project_period_id' : 'filter_country_id')).data('selected');
					if (selectedValue) {
						$('#' + (level === 'term' ? 'filter_course_id' : level === 'course' ? 'filter_operating_institution_id' : level === 'institution' ? 'filter_project_period_id' : 'filter_country_id')).val(selectedValue).trigger('change');
					}
				},
				error: function() {
					alert('데이터를 불러오는데 실패했습니다.');
				}
			});
		}
	});
	
	// 페이지 로드 시 저장된 필터 값 복원
	@if(request('filter_project_term_id'))
		$('#filter_project_term_id').trigger('change');
	@endif
});
</script>
@endpush