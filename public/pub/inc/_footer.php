<div class="mobile_foot mo_vw">
<?if($type=="kofih"){?>
	<a href="/kofih/dashboard.php" class="i1 <?if($gNum=="main"){?>on<?}?>">대시보드</a>
	<a href="/kofih/member.php" class="ik2 <?if($gNum=="01"){?>on<?}?>">회원</a>
	<a href="/kofih/schedule.php" class="i2 <?if($gNum=="02"){?>on<?}?>">일정</a>
	<a href="/kofih/notices.php" class="i4 <?if($gNum=="03"){?>on<?}?>">공지사항</a>
<?}else{?>
	<a href="/" class="i1 <?if($gNum=="main"){?>on<?}?>">Dashboard</a>
	<a href="/page/schedule.php" class="i2 <?if($gNum=="02"){?>on<?}?>">Schedule</a>
	<a href="/page/accommodation.php" class="i3 <?if($gNum=="03"){?>on<?}?>">Map</a>
	<a href="/page/mypage.php" class="i4 <?if($gNum=="08"&&$sNum=="01"){?>on<?}?>">My Page</a>
<?}?>
</div>
<div class="footer">
	<div class="links">
		<ul class="inner">
			<li><a href="/page/privacy_policy.php"><strong>Privacy Policy</strong></a></li>
			<li><a href="/page/terms.php">Terms Of Use</a></li>
		</ul>
	</div>
	<div class="info_wrap">
		<div class="inner">
			<div class="left">
				<div class="logo"></div>
				<ul class="info">
					<li>CEO: Myungjin Jung</li>
					<li>Address: Cosmojin B/D, 26-17 World Cupbuk-ro 1-gil, Mapo-gu, Seoul, 04031, Republic of Korea</li>
					<li>Business Registration Number: 105-86-31215</li>
					<li>Mail Order License: Seoul Jung-gu No. 0266</li>
					<li>Customer Service: +82-2-318-0345</li>
					<li>Fax: +82-2-318-0426</li>
					<li>Email: booking@cosmojin.com</li>
					<li>Customer Service: +82-2-318-0345</li>
				</ul>
				<p class="copy">Copyrightⓒ 2022 COSMOJIN. All right reserved</p>
			</div>
			<div class="right">
				<div class="gray_box">
				<?if($type=="kofih"){?>
					<div class="box">
						<div class="tit">연수기관 담당자</div>
						<div class="name"><strong>노연</strong> 연구교수</div>
						<ul>
							<li class="email">rohyoun@inje.ac.kr</li>
							<li class="tel">010-4660-9460</li>
						</ul>
					</div>
					<div class="box">
						<div class="tit">코스모진 담당자</div>
						<div class="name"><strong>김영희</strong> 전문가</div>
						<ul>
							<li class="email">staff2@email.com</li>
							<li class="tel">010-1111-2222</li>
						</ul>
					</div>
				<?}else{?>
					<div class="box">
						<div class="tit">Training Institute Contact Person</div>
						<div class="name"><strong>Noh Yeon</strong> Research Professor</div>
						<ul>
							<li class="email">rohyoun@inje.ac.kr</li>
							<li class="tel">010-4660-9460</li>
						</ul>
					</div>
					<div class="box">
						<div class="tit">Cosmogene Contact Person</div>
						<div class="name"><strong>Kim Young-hee</strong> Professional</div>
						<ul>
							<li class="email">staff2@email.com</li>
							<li class="tel">010-1111-2222</li>
						</ul>
					</div>
				<?}?>
				</div>
				<?if($type=="kofih"){?>
				<a href="#this" class="green_box"><i>바로가기</i></a>
				<?}else{?>
				<a href="#this" class="green_box"><i>Shortcut</i></a>
				<?}?>
			</div>
		</div>
	</div>
</div>

</body>
</html>