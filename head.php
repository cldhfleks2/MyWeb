<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');
$sql = "SELECT * FROM board WHERE view = 1 ORDER BY num ASC";
$menu= $mysqli->query($sql);

if(@$_SESSION['user_info']){
	$sql = "SELECT COUNT(*) FROM noti WHERE r_user_id = ".$_SESSION['user_info']['idx']."  
	&& r_user_id != s_user_id
	&& is_read = 0";
	$nCount = $mysqli->query($sql);
	$nCount = $nCount->fetch_array();
	$nCount = $nCount[0];
}

$thumb_img = '/img/thumb.png';
$page_title = $site_setting['site_name'];
$page_desc = "롤케익 커뮤니티";
$request_uri = $_SERVER['REQUEST_URI'];
if(strpos($request_uri, 'read.php') > -1){
	$id = $_GET['id']; 
	$sql = "SELECT * FROM contents WHERE idx = $id && is_view = 1";
	$contents = $mysqli->query($sql);
	$contents = $contents->fetch_array();
	if(!$contents){
		exit('<script>alert("게시물이 없습니다."); history.back()</script>');
	}


	$allowed_ext = array('jpg','jpeg','png','gif');
	$fileListExp = explode('|',$contents['file']);
	$fileList = array_values(array_filter($fileListExp));
	$imageList = array(); 
	for($i = 0; $i < count($fileList); $i++){
		$ext = @array_pop(explode('.', $fileList[$i]));
		$ext = strtolower($ext);
		if(in_array($ext, $allowed_ext) ) {
			$imageList[] = $fileList[$i];
		}
	}

	if(@$imageList[0]){
		$thumb_img = $imageList[0];
	}
	$page_title = $contents['title'].' | '.$site_setting['site_name'];
	$page_desc = iconv_substr($contents['contents'],0,50,"utf-8").' | 롤케익커뮤니티'; //주소창에뜨는 이름
}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?=$page_desc?>">
		<meta name="author" content="cldhfleks2@naver.com">
		<meta name="image" content="<?=$thumb_img?>">
		<meta property="og:image" content="<?=$thumb_img?>" />
		<meta property="og:description" content="<?=$page_desc?>" />
		<meta property="og:title" content="<?=$page_title?>" />
		<link rel="shortcut icon" href="/img/favicon.ico">
		<title><?=$page_title?></title>
		<script src="https://kit.fontawesome.com/d8dbfbd854.js" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="/css/global.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
		<!--<script data-ad-client="ca-pub-565655555~~" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
		<script async src="https://cse.google.com/cse.js?cx=b2efff50eac8345ba"></script> 
	</head>
	<body> 
		<div class="bg cls" onclick="close_layer()"></div>
		<div class="top">
			<div class="topInner">
				<i class="fas fa-bars cp" onclick="menuOn()"></i>
				<?php if(@$_SESSION['user_info']) {?>
				<div class="profile_Picture cp" onclick="openProfile()" style="background-image:url(<?=pfChk($_SESSION['user_info']['pic'])?>)"></div>
				<div class="noti" onclick="loadNoti()">
					<?php if($nCount > 0) { ?>
						<div class="notiNum">
							<?php if($nCount > 99) { ?> 
								+99
							<?php } else { ?>
								<?php if(strlen($nCount) < 3) { ?>&nbsp;<?php if(strlen($nCount) < 2) { ?>&nbsp;<?php } ?><?php } ?><?=$nCount?><?php if(strlen($nCount) < 3) { ?>&nbsp;<?php if(strlen($nCount) < 2) { ?>&nbsp;<?php } ?>
							<?php } ?>
						<?php }?>
						</div>
					<?php } ?>
					<i class="fas fa-bell"></i>
				</div>
				<div class="userInfo">
					<div><?=$_SESSION['user_info']['name']?></div>
					<hr/>
					<div class="cp" onclick="document.location.href='/user/myInfo.php'">내 정보 수정</div>
					<div class="cp" onclick="document.location.href='/board/list.php?type=my'">내가 쓴글</div>
					<div class="cp" onclick="document.location.href='/user/mynoti.php'">내 알림</div>
					<hr/>
					<a href="/proc/logout.php">로그아웃</a>
				</div>
				<?php } else { ?>
				<div class="login_btn" onclick="signInOn()">로그인</div>
				<?php } ?>
				<a href="/">
					<img src="<?=$site_setting['site_logo']?>">
				</a>
				<div class="menu"> 
					<div class="menuOuter"></div>
					<div class="menuInner"> 
						<!--구글검색-->
						<div class="search"> 
							<div class="gcse-search"></div>
						</div>
						<div class="realMenu"> 
							<ul>
								<?php 
								$i = 0;
								$j = $menu->num_rows - 2; //2개는 밑줄을 긋고 표시해줄것
								while($row = $menu->fetch_array()){ ?>
									<li>
										<?php if($i == $j) { ?>
											<hr/>
										<?php } ?>
										<a href="/board/list.php?id=<?=$row['idx']?>"><?=$row['name']?>
										</a>
									</li>
								<?php $i++; } ?>
 							</ul>
						</div>
					</div>
					<div class="mBottom">
						<div class="bottom_sns">
							<a href="https://facebook.com/profile.php?id=100017112346944" target="_blank"><i class="fab fa-facebook-f"></i></a>
							<a href="https://www.instagram.com/cldhfleks2/" target="_blank"><i class="fab fa-instagram"></i></a>
						</div>
						<a href="/privacy/privacy.html" target="_blank">개인정보취급방침</a> | <a href="/privacy/yakgwan.html" target="_blank">이용약관</a><br><?=$site_setting['site_info']?>
					</div>
				</div>
			</div>
		</div>