<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

	$idx = $_POST['idx'];
	$uId = $_SESSION['user_info']['idx'];
	$comment = $_POST['comment'];

	if(!$comment){
		exit('<script>alert("댓글을 입력 해주세요."); history.back()</script>');
	}

	$sql = "UPDATE comment SET comment = '$comment' WHERE idx = $idx";
	$query = $mysqli->query($sql);
	
	if(!$query){
		echo 2;
	}else{
		echo 1;
	}

?> 
