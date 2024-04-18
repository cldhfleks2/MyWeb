<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/head.php');
$bId = @$_GET['id'];
$type = '';
if(@$_GET['type']){
	$type = $_GET['type'];
}

// 추가된 부분
if($bId == 5) {
    header("Location: /board/match.php");
    exit();
}

$page = 0;
if(@$_GET['page']){
		$page = $_GET['page'] - 1; //인젝션 방어도 됨
}
	$LIMIT = 10; //10개씩 보여줄거임
	$pages = $LIMIT * $page; //그럼 생기는 페이지갯수
	$sql_all = "SELECT *,c.idx as cIdx,(SELECT COUNT(*) FROM comment WHERE contents_id = c.idx) as ctCnt 
	FROM contents as c 
	INNER JOIN user as u ON (c.user_id = u.idx) 
	WHERE c.is_view = 1 ";
	$sql_limit = " LIMIT $pages,$LIMIT";
	$sql_CNT = "SELECT COUNT(*) as cnt FROM contents as c 
	INNER JOIN user as u ON (c.user_id = u.idx) 
	WHERE c.is_view = 1 ";
	

	$useBoardName = false;
	if($type == 'best'){
		$boardInfo['name'] = '<i class="fas fa-trophy"></i> 베스트 게시물';
		$sql = $sql_all." ORDER BY c.view DESC ".$sql_limit;
		$contentsList = $mysqli->query($sql);

		$sql = $sql_CNT;
		$bCount = $mysqli->query($sql);
		$bCount = $bCount->fetch_array();
		$useBoardName = true;


	}else if($type == 'recent'){
		$boardInfo['name'] = '<i class="fas fa-clock"></i> 최근 게시물';
		$sql = $sql_all." ORDER BY c.idx DESC ".$sql_limit;
		$contentsList = $mysqli->query($sql);

		$sql = $sql_CNT;
		$bCount = $mysqli->query($sql);
		$bCount = $bCount->fetch_array();
		$useBoardName = true;

	}else if($type == 'my'){
		$boardInfo['name'] = '<i class="fas fa-user"></i> '.$_SESSION['user_info']['name'].'님의 게시물';
		$sql = $sql_all." && c.user_id = ".$_SESSION['user_info']['idx']."
		ORDER BY c.idx DESC ".$sql_limit;
		$contentsList = $mysqli->query($sql);

		$sql = $sql_CNT." && c.user_id = ".$_SESSION['user_info']['idx'];
		$bCount = $mysqli->query($sql);
		$bCount = $bCount->fetch_array();
		$useBoardName = true;

	}else {

		$sql = "SELECT * FROM board WHERE idx = $bId";
		$boardInfo = $mysqli->query($sql);
		$boardInfo = $boardInfo->fetch_array();
		
		$sql = $sql_all." && board_id = $bId
		ORDER BY c.idx DESC ".$sql_limit;
		$contentsList = $mysqli->query($sql);

		$sql = $sql_CNT." && board_id = $bId ";
		$bCount = $mysqli->query($sql);
		$bCount = $bCount->fetch_array();
	}

	$boardName = ''; //어떤게시판에 올렸는지 출력


	$bCount = $bCount['cnt']; //bCount변수를 재정의함.
	$paging = ceil($bCount / $LIMIT);
	?>
	<link rel="stylesheet" type="text/css" href="/css/board.css">
	<div class="contents">
		<div class="bTitle">
			<a href="javascript:document.location.reload()"><?=$boardInfo['name']?></a> <span style="font-size:12px;">게시물 수 : (<?=$bCount?>)</span>
			<?php if(!$type) { ?>
				<div class="writeBtn cp" onclick="document.location.href='write.php?id=<?=$bId?>'">
					<i class="fa-solid fa-pencil"></i> 게시글작성
				</div>
			<?php } ?>
		</div>
		<div class="list">
			<?php if($contentsList->num_rows > 0){ ?>
				<?php 
				while($row = $contentsList->fetch_array()){
					if(@$_GET['id'] == 4){ //익명게시판이면 프로필이미지와 이름을 똑같이 지정해줌
						$row['name'] = 'Anonnymous';
						$row['pic'] = 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg';
					}
					if($useBoardName){
						$sql = "SELECT * FROM board WHERE idx = ".$row['board_id'];
						$boardInfo = $mysqli->query($sql);
						$boardInfo = $boardInfo->fetch_array();
						$boardName = $boardInfo['name'];
					}


					?>
					<div class="article">
						<div class="titleLayer">
							<a href="/board/read.php?id=<?=$row['cIdx']?>">
								<div class="num">
									#<?=$row['cIdx']?>
									<?=$boardName?> 
									<?php
            							// 게시물 내에 이미지가 있는지 확인
									$contentHTML = htmlspecialchars_decode($row['contents']); 
									$dom = new DOMDocument();
									@$dom->loadHTML($contentHTML); 
									$images = $dom->getElementsByTagName('img');
            							if ($images->length > 0) { //본문에 이미지가 있을 경우 아이콘 표시
            								echo '<i class="far fa-images" aria-hidden="true"></i>';
            							}
            							?>
            							<?php
            							if (!empty($row['file'])) { //첨부파일이 존재하면 아이콘 표시
            								echo '<i class="fa-regular fa-file" aria-hidden="true"></i>';
            							}
            							?>
            						</div> 
            						<div class="title"><?=$row['title']?></div>
            					</a>
            				</div>
            				<div class="articleRight user">
            					<div class="pic" style="background-image:url(<?=pfChk($row['pic'])?>)"></div>
            					<div class="name"><a><?=$row['name']?></a></div> 
            				</div>
            				<div class="articleRight contents_info">
            					<div class="comment"><i class="fas fa-comment"></i> <?=$row['ctCnt']?></div>
            					<div class="like"><i class="fas fa-thumbs-up"></i> <?=$row['like_cnt']?></div>
            					<div class="dislike"><i class="fas fa-eye"></i> <?=$row['view']?></div>
            					<div class="date"><?=display_datetime($row['reg_date'])?></div>
            				</div>
            			</div>
            		<?php } ?>
            		<div class="paging">
            			<?php for($i = 0 ; $i < $paging; $i++) { 
            				$j = $i+1;
            				if($i != $page) {?>
            					<a href="<?=$_SERVER['REQUEST_URI']?>&page=<?=$j?>"><?=$j?></a>
            				<?php } else { ?>
            					<?=$j?>
            				<?php } ?>
            			<?php } ?>
            		</div>
            	<?php } else { ?>
            		<div class="noContents">게시물이 없습니다.</div>
            	<?php } ?>
            </div>


            <?php
            include_once($_SERVER['DOCUMENT_ROOT'].'/bottom.php');
            ?>

