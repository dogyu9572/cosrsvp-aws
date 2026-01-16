$(document).ready(function(){
//헤더
	$(window).scroll(function() {
		if ($(window).scrollTop() > 100) {
			$(".header").addClass("fixed");
		} else {
			$(".header").removeClass("fixed");
		}
	});
	$(".btn_menu").click(function(){
		$("html,body").stop(false,true).toggleClass("over_h");
		$(".header").stop(false,true).toggleClass("on");
	});
	$(".header .gnb .menu .mo_vw").click(function(){
		$(this).next(".snb").stop(false,true).slideToggle("fast").parent().stop(false,true).toggleClass("open").siblings().removeClass("open").removeClass("on").children(".snb").slideUp("fast");
	});
	
//마이페이지 알림
	const $alert = $(".header .gnb .menu .alert");

    // 쿠키가 있으면 처음부터 숨김
    if ($.cookie("alertHide") === "1") {
        $alert.hide();
    }

    // 클릭 시 fadeOut + 쿠키 저장
    $alert.on("click", function () {
        $(this).fadeOut(300);
        $.cookie("alertHide", "1", { expires: 7, path: "/" });  // 7일 유지
    });
//aside
	/*$(".aside dt").click(function(event){
		$(this).next("dd").stop(false,true).slideToggle("fast").parent().stop(false,true).toggleClass("on").siblings().removeClass("on").children("dd").slideUp("fast");
		event.stopPropagation(); // 이벤트 전파를 막음
	});
	$(document).click(function(event){
		if(!$(event.target).closest('.aside dl').length) {
			$(".aside dl").removeClass("on").children("dd").slideUp("fast");
		}
	});*/
//브라우저 사이즈
	let vh = window.innerHeight * 0.01; 
	document.documentElement.style.setProperty('--vh', `${vh}px`);
//화면 리사이즈시 변경 
	window.addEventListener('resize', () => {
		let vh = window.innerHeight * 0.01; 
		document.documentElement.style.setProperty('--vh', `${vh}px`);
	});
	window.addEventListener('touchend', () => {
		let vh = window.innerHeight * 0.01;
		document.documentElement.style.setProperty('--vh', `${vh}px`);
	});
});

//팝업
function layerShow(id) {
	$("#" + id).fadeIn(300);
}
function layerHide(id) {
	$("#" + id).fadeOut(300);
}