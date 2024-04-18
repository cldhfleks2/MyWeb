<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

$cId = $_POST['cId'];
$uId = $_SESSION['user_info']['idx'];
$comment = $_POST['comment'];

if(!$comment){
	exit('<script>alert("댓글을 입력 해주세요."); history.back()</script>');
}

//댓글 추가
$sql = "INSERT INTO comment (contents_id, user_id, comment) VALUES ('$cId', '$uId', '$comment')";
$query = $mysqli->query($sql);

//댓글 추가 후 댓글 목록을 다시 불러옴
$sql = "SELECT * FROM comment WHERE contents_id = $cId && user_id = $uId ORDER BY idx DESC LIMIT 1";
$commentList = $mysqli->query($sql);

//댓글이 달린 게시물의 정보들
$sql ="SELECT * FROM contents WHERE idx = ".$cId;
$query = $mysqli->query($sql);
$contentsInfo = $query->fetch_array();
$r_user_id = $contentsInfo['user_id']; //게시물 작성자
$contents_id = $cId; //게시물 번호
$notiContents = $comment; //알림에 표시할 내용 현재는 댓글내용만. 

include_once($_SERVER['DOCUMENT_ROOT'].'/proc/noti.proc.php');

?> 
<?php while($row = $commentList->fetch_array()){ 
	$sql = "SELECT * FROM user WHERE idx = ".$row['user_id'];
	$userInfoC = $mysqli->query($sql);
	$userInfoC = $userInfoC->fetch_array();

	if(@$_SESSION['user_info']){
		$sql = "SELECT * FROM comment_like_list WHERE comment_id = ".$row['idx']." && user_id = ".$_SESSION['user_info']['idx'];
		$cLike = $mysqli->query($sql);
		$cLike = $cLike->fetch_array();
	}

	
	?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/board/proc/comment_form.php') ?>
<?php } ?>

