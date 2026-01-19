<div class="popup" id="pop_supplement">
	<div class="dm" onclick="layerHide('pop_supplement');"></div>
	<div class="inbox">
		<button type="button" class="btn_close" onclick="layerHide('pop_supplement');">Close</button>
		<div class="tit">Reason for supplementing documents</div>
		<div class="scroll">
			<div class="con gbox" id="supplement_content">
				No supplement reason.
			</div>
			<button type="button" class="btn_clo" onclick="layerHide('pop_supplement');">Check</button>
		</div>
	</div>
</div>

<script>
// Global function: Display supplement request reason
window.showSupplementReason = function(documentId, supplementContent) {
    if (!documentId || documentId === 0) {
        alert('No document registered.');
        return;
    }
    
    const $content = $('#supplement_content');
    
    if (supplementContent && supplementContent.trim() !== '') {
        // 줄바꿈 처리
        const formattedContent = supplementContent.replace(/\n/g, '<br/>');
        $content.html(formattedContent);
    } else {
        $content.html('No supplement reason.');
    }
    
    layerShow('pop_supplement');
};
</script>
