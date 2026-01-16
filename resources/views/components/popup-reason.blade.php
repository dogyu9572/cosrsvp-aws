<div class="popup" id="pop_reason">
	<div class="dm" onclick="layerHide('pop_reason');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_reason');">Close</button>
		<div class="tit">Submit documents</div>
		<div class="scroll">
			<div class="con file_wrap">
				<div class="tt">Self-introduction</div>
				<label class="input_file"><input type="file"><span>File attachment</span></label>
				<div class="file_list"></div>
			</div>
			<button type="button" class="btn_clo" onclick="layerHide('pop_reason');">Check</button>
		</div>
	</div>
</div>

<script>
$(document).ready(function () {
    $('.file_wrap input[type="file"]').on('change', function () {
        const file = this.files[0];
        const $wrap = $(this).closest('.file_wrap');
        const $list = $wrap.find('.file_list');
        if (!file) return;
        const fileName = file.name;
        $list.append(`<button type="button" class="file_item">${fileName}</button>`);
        $wrap.addClass('on');
    });
    $(document).on('click', '.file_list .file_item', function () {
        const $wrap = $(this).closest('.file_wrap');
        $(this).remove();
        if ($wrap.find('.file_item').length === 0) {
            $wrap.removeClass('on');
            $wrap.find('input[type="file"]').val('');
        }
    });
});
</script>
