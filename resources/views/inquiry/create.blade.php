@php
$gNum = $gNum ?? "07";
$sNum = $sNum ?? "01";
$gName = $gName ?? "Contact Us";
$sName = $sName ?? "Contact Us";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container inquiry_wrap">
	@include('components.user-header')
	<div class="contents">
	
		<div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span><span><strong>{{ $sName }}</strong></span></div></div>
		
		<div class="wbox sh2">
			<form action="{{ route('inquiries.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="board_write tbl tbl_colm">
					<table>
						<tr>
							<th>Title</th>
							<td><input type="text" name="title" class="text w100p" value="{{ old('title') }}" required></td>
						</tr>
						<tr>
							<th>Contents</th>
							<td>
								<textarea name="content" class="text w100p" rows="10" required>{{ old('content') }}</textarea>
							</td>
						</tr>
						<tr>
							<th>Attachment</th>
							<td>
								<div class="file_area">
									<label class="input_file"><input type="file" name="attachments[]" multiple><span>Attach file</span></label>
									<div class="file_list"></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="board_bottom flex_center">
					<button type="submit" class="btn_submit">Contact Us</button>
				</div>
			</form>
		</div>
		
	</div>
</div>

@include('components.user-footer')

@push('scripts')
<script>
$(document).ready(function () {
	// 최대 용량(10MB)
	const MAX_SIZE = 10 * 1024 * 1024;

	// 파일 상태 저장용
	const fileStore = new Map(); // key: input element, value: File[]

	$('.file_area input[type="file"]').on('change', function (e) {
		const input = this;
		const $wrap = $(this).closest('.file_area');
		const $list = $wrap.find('.file_list');

		let files = Array.from(this.files);
		let storedFiles = fileStore.get(input) || [];

		// 기존 저장 + 새 파일 합치기
		let combined = storedFiles.concat(files);

		// 총 용량 계산
		let totalSize = combined.reduce((sum, f) => sum + f.size, 0);

		if (totalSize > MAX_SIZE) {
			alert("총 첨부 용량은 10MB를 초과할 수 없습니다.");
			input.value = ""; // 선택했던 파일 초기화
			return;
		}

		// 저장소 업데이트
		fileStore.set(input, combined);

		// UI 업데이트
		$list.empty();
		combined.forEach((f, idx) => {
			$list.append(`
				<button type="button" class="file_item" data-idx="${idx}">
					${f.name}
				</button>
			`);
		});

		$wrap.addClass('on');
	});

	// 파일 삭제 처리
	$(document).on('click', '.file_list .file_item', function () {
		const idx = $(this).data("idx");
		const $wrap = $(this).closest('.file_area');
		const input = $wrap.find('input[type="file"]')[0];
		const $list = $wrap.find('.file_list');

		let storedFiles = fileStore.get(input) || [];

		// 해당 파일 제거
		storedFiles.splice(idx, 1);

		// 저장소 갱신
		if (storedFiles.length > 0) {
			fileStore.set(input, storedFiles);
		} else {
			fileStore.delete(input);
		}

		// UI 업데이트
		$list.empty();
		storedFiles.forEach((f, idx) => {
			$list.append(`
				<button type="button" class="file_item" data-idx="${idx}">
					${f.name}
				</button>
			`);
		});

		// 파일 없으면 리셋
		if (storedFiles.length === 0) {
			$wrap.removeClass('on');
			input.value = "";
		}
	});

	// 폼 제출 시 파일 데이터 전송을 위한 처리
	$('form').on('submit', function(e) {
		const input = $('.file_area input[type="file"]')[0];
		const storedFiles = fileStore.get(input) || [];
		
		if (storedFiles.length > 0) {
			// DataTransfer를 사용하여 파일 목록 재구성
			const dataTransfer = new DataTransfer();
			storedFiles.forEach(file => {
				dataTransfer.items.add(file);
			});
			input.files = dataTransfer.files;
		}
	});
});
</script>
@endpush

@endsection
