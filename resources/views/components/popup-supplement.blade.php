<div class="popup" id="pop_supplement">
	<div class="dm" onclick="layerHide('pop_supplement');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_supplement');">Close</button>
		<div class="tit">Reason for supplementing documents</div>
		<div class="scroll">
			<div class="con gbox" id="supplement_content">
				보완사유가 없습니다.
			</div>
			<button type="button" class="btn_clo" onclick="layerHide('pop_supplement');">Check</button>
		</div>
	</div>
</div>

<script>
// 전역 함수: 보완요청 사유 표시
window.showSupplementReason = function(documentId, supplementContent) {
    if (!documentId || documentId === 0) {
        alert('등록된 서류가 없습니다.');
        return;
    }
    
    const $content = $('#supplement_content');
    
    if (supplementContent && supplementContent.trim() !== '') {
        // 줄바꿈 처리
        const formattedContent = supplementContent.replace(/\n/g, '<br/>');
        $content.html(formattedContent);
    } else {
        $content.html('보완사유가 없습니다.');
    }
    
    layerShow('pop_supplement');
};
</script>
