<?php
include_once('head.php');

$sql = "SELECT *,c.idx as cIdx ,u.name as name,
		(SELECT COUNT(*) FROM comment WHERE contents_id = c.idx) as ctCnt 
		FROM contents as c
		INNER JOIN user as u ON(c.user_id = u.idx)
		WHERE c.is_view = 1 ORDER BY c.view DESC LIMIT 5";
$bestContents = $mysqli->query($sql);

$sql = "SELECT *,c.idx as cIdx ,u.name as name,
		(SELECT COUNT(*) FROM comment WHERE contents_id = c.idx) as ctCnt 
		FROM contents as c
		INNER JOIN user as u ON(c.user_id = u.idx)
		WHERE c.is_view = 1 ORDER BY c.reg_date DESC LIMIT 5"; 
$recentContents = $mysqli->query($sql);

$sql = "SELECT *,c.idx as cIdx ,u.name as name, 
		(SELECT COUNT(*) FROM comment WHERE contents_id = c.idx) as ctCnt 
		FROM contents as c
		INNER JOIN user as u ON(c.user_id = u.idx)
		WHERE c.is_view = 1 && c.board_id = 1 ORDER BY c.idx DESC LIMIT 5";
$noticeContents = $mysqli->query($sql);

?>
<link rel="stylesheet" type="text/css" href="/css/index.css">
<div class="mainSlide">
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<?=$site_setting['site_banner']?>
		</div>
		<div class="swiper-button-next"></div>
		<div class="swiper-button-prev"></div>
	</div>
</div>
<div class="contents">
	<div class="bestArticle">
		<div class="bALayer bestofbest">
			<div class="title"><a href="/board/list.php?type=best"><i class="fas fa-trophy"></i> 베스트 게시물</a></div>
			<div class="list">
				<ul>
					<?php while($row = $bestContents->fetch_array()){ ?>
						<li class="cp" onclick="document.location.href='/board/read.php?id=<?=$row['cIdx']?>'">
							<div class="title">
								<?=$row['title']?>
							</div>
							<div class="info">
								<?=$row['name']?> <?=date('Y-m-d', strtotime($row['update_date']))?> 
								<i class="fas fa-eye"></i> 
								<?=$row['view']?> 
								<i class="fas fa-comment"></i> 
								<?=$row['ctCnt']?> 
								<i class="fas fa-thumbs-up"></i> 
								<?=$row['like_cnt']?> 
								<i class="fas fa-thumbs-down"></i> 
								<?=$row['dislike_cnt']?> 
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="bALayer recent">
			<div class="title"><a href="/board/list.php?type=recent"><i class="fas fa-clock"></i> 최근 게시물</a></div>
			<div class="list">
				<ul>
					<?php while($row = $recentContents->fetch_array()){ ?>
						<li class="cp" onclick="document.location.href='/board/read.php?id=<?=$row['cIdx']?>'">
							<div class="title">
								<?=$row['title']?>
							</div>
							<div class="info">
								<?=$row['name']?> <?=date('Y-m-d', strtotime($row['update_date']))?> 
								<i class="fas fa-eye"></i> 
								<?=$row['view']?> 
								<i class="fas fa-comment"></i> 
								<?=$row['ctCnt']?> 
								<i class="fas fa-thumbs-up"></i> 
								<?=$row['like_cnt']?> 
								<i class="fas fa-thumbs-down"></i> 
								<?=$row['dislike_cnt']?> 
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="bALayer notice">
			<div class="title"><a href="/board/list.php?id=1"><i class="fa-solid fa-bullhorn"></i> 공지사항</a></div>
			<div class="list">
				<ul>
					<?php while($row = $noticeContents->fetch_array()){ ?>
						<li class="cp" onclick="document.location.href='/board/read.php?id=<?=$row['cIdx']?>'">
							<div class="title">
								<?=$row['title']?>
							</div>
							<div class="info">
								<?=$row['name']?> <?=date('Y-m-d', strtotime($row['update_date']))?> 
								<i class="fas fa-eye"></i> 
								<?=$row['view']?> 
								<i class="fas fa-comment"></i> 
								<?=$row['ctCnt']?> 
								<i class="fas fa-thumbs-up"></i> 
								<?=$row['like_cnt']?> 
								<i class="fas fa-thumbs-down"></i> 
								<?=$row['dislike_cnt']?> 
							</div>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<hr/>
	<div class="hello">
		<?=$site_setting['site_hello']?></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script> 
<script>
	var swiper = new Swiper(".swiper-container", {
		slidesPerView: "auto",  //초기값 설정 모바일값이 먼저!!
		centeredSlides:true,
		autoplay:{
			delay:3500,
			disableOnInteraction:false,
		},
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		}
	});


</script>
<?php
	include_once('bottom.php');
?>

