<div class="header">
	<?if($type=="kofih"){?>
	<a href="/kofih/dashboard.php" class="logo">
	<?}else{?>
	<a href="/" class="logo">
	<?}?>
	<img src="/pub/images/logo.png" alt="logo"><h1>COSMOJIN</h1></a>
	<a href="javascript:void(0);" class="btn_menu">
		<p class="t"></p>
		<p class="m"></p>
		<p class="b"></p>
	</a>
	
	<div class="mo_wrap">
		<?if($type=="kofih"){?>
		<div class="info">
			<div class="mo_info_in">
				<div class="img"><img src="/pub/images/img_profile.svg" alt=""></div>
				<div class="name">ADMIN</div>
			</div>
			<a href="/kofih/mypage.php" class="btn_mypage mo_vw">MY PAGE</a>
		</div>
		<div class="scroll">
			<div class="gnb">
				<div class="menu gnb0 <?if($gNum=="main"){?>on<?}?>"><a href="/kofih/dashboard.php">대시보드</a></div>
				<div class="menu gnb_k1 <?if($gNum=="01"){?>on<?}?>"><a href="/kofih/member.php">회원</a></div>
				<div class="menu gnb2 <?if($gNum=="02"){?>on<?}?>"><a href="/kofih/schedule.php">일정</a></div>
				<div class="menu gnb3 <?if($gNum=="03"){?>on<?}?>"><a href="/kofih/notices.php">공지사항</a></div>
				<div class="menu gnb4 <?if($gNum=="04"){?>on<?}?>"><a href="/kofih/news.php">최신뉴스</a></div>
				<div class="menu gnb5 <?if($gNum=="05"){?>on<?}?>"><a href="/kofih/gallery.php">갤러리</a></div>
			</div>
		</div>
		<?}else{?>
		<div class="info">
			<div class="mo_info_in">
				<div class="img"><img src="/pub/images/img_profile.svg" alt=""></div>
				<div class="name">Hong Gil-dong</div>
				<div class="affiliation">Basic Medicine_Korea University</div>
			</div>
			<a href="/page/mypage.php" class="btn_mypage mo_vw">MY PAGE</a>
		</div>
		<div class="scroll">
			<div class="gnb">
				<div class="menu gnb0 <?if($gNum=="main"){?>on<?}?>"><a href="/">Dashboard</a></div>
				<!-- <div class="menu gnb1 <?if($gNum=="01"){?>on<?}?>"><a href="/page/document.php">Document submission</a></div> -->
				<div class="menu gnb2 <?if($gNum=="02"){?>on<?}?>"><a href="/page/schedule.php">Schedule</a></div>
				<div class="menu gnb3 <?if($gNum=="03"){?>on<?}?>"><a href="/page/accommodation.php">Map</a></div>
				<div class="menu gnb4 <?if($gNum=="04"){?>on<?}?>"><a href="/page/notices.php">Notice</a></div>
				<div class="menu gnb5 <?if($gNum=="05"){?>on<?}?>"><a href="/page/gallery.php">Gallery</a></div>
				<div class="menu gnb6 <?if($gNum=="06"){?>on<?}?>"><a href="/page/news.php">Latest News</a></div>
				<div class="menu gnb7 <?if($gNum=="07"){?>on<?}?>"><a href="/page/inquiries.php">Contact Us<i></i></a>
					<div class="snb">
						<a href="/page/inquiries.php" class="<?if($gNum=="07"&&$sNum=="01"){?>on<?}?>">Contact Us</a>
						<a href="/page/faq.php" class="<?if($gNum=="07"&&$sNum=="02"){?>on<?}?>">FAQ</a>
					</div>
				</div>
				<div class="menu gnb8 <?if($gNum=="08"){?>on<?}?>"><a href="/page/mypage.php">MY PAGE<i></i></a>
					<button type="button" class="alert"><span class="flex"><i></i><p>There is a notification that requires your confirmation.<br/>Please confirm.</p></span></button>
					<div class="snb">
						<a href="/page/mypage.php" class="<?if($gNum=="08"&&$sNum=="01"){?>on<?}?>">MY PAGE</a>
						<a href="/page/alarm.php" class="<?if($gNum=="08"&&$sNum=="02"){?>on<?}?>">Alarm</a>
					</div>
				
				</div>
			</div>
			<div class="weather">
				<dl class="gap1">
					<dt>Weather</dt>
					<dd>
						<p>
							<i><img src="/pub/images/icon_sun.svg" alt="맑음"></i>
							<strong>10°C</strong>
						</p>
					</dd>
				</dl>
				<dl class="gap2">
					<dt>Exchange rate</dt>
					<dd>
						<p>USD<strong>1,463.60</strong></p>
						<p>EUR<strong>1,690.97</strong></p>
						<p>GBP<strong>1,926.61</strong></p>
					</dd>
				</dl>
			</div>
		</div>
		<?}?>
	</div>
	
</div>