<?php
	//페이징을 위해 댓글 리스트를 불러옴
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');
	
	$error = '<div style="margin:10px; text-align:center">오류. 댓글을 불러올 수 없습니다.</div>';
	if(!is_numeric($_POST['cPage'])){
		exit($error);
	}
	if(!is_numeric($_POST['cLIMIT'])){
		exit($error);
	}
	if(!is_numeric($_POST['cId'])){
		exit($error);
	}
	$cPage = $_POST['cPage'];
	$cLIMIT = $_POST['cLIMIT'];
	$cPages = $cPage * $cLIMIT;
	$cId = $_POST['cId'];

	$sql = "SELECT * FROM comment WHERE contents_id = $cId ORDER BY idx DESC LIMIT $cPages ,$cLIMIT";
	$commentList = $mysqli->query($sql);

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