<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

	$idx = $_POST['idx'];
	$uId = $_SESSION['user_info']['idx'];


	$sql = "DELETE FROM comment  WHERE idx = $idx"; //댓글 지움
	$query = $mysqli->query($sql);
	
	if(!$query){
		echo 2;
	}else{
		echo 1;
	}

?>
