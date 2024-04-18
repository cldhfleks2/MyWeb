<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

	$idx = $_POST['idx'];
	$uId = $_SESSION['user_info']['idx'];
	$v = $_POST['v'];

	$sql = "SELECT * FROM comment_like_list WHERE comment_id = $idx && user_id = $uId";
	$query = $mysqli->query($sql);
	$clike = $query->fetch_array();

	$isLike = 0;
	if($v == 'like'){
		$isLike = 1;
	}

	if($clike['is_like'] == $isLike && $clike['is_like'] != ''){
		exit();
	}else{
		if($isLike && $clike['is_like'] != ''){
			$sql = "UPDATE comment SET dislike_cnt = dislike_cnt - 1 WHERE idx = $idx"; //취약함
			$query = $mysqli->query($sql);
		} else if(!$isLike && $clike['is_like'] != ''){
			$sql = "UPDATE comment SET like_cnt = like_cnt - 1 WHERE idx = $idx"; //취약함
			$query = $mysqli->query($sql);
		}

		$sql = "DELETE FROM comment_like_list WHERE comment_id = $idx && user_id = $uId";
		$query = $mysqli->query($sql);
	}

	$sql = "INSERT INTO comment_like_list (comment_id, user_id, is_like) VALUES ('$idx', '$uId', '$isLike')";
	$query = $mysqli->query($sql);

	$q = $v."_cnt";
	$sql = "UPDATE comment SET $q = $q + 1 WHERE idx = $idx"; //취약함
	$query = $mysqli->query($sql);

?>