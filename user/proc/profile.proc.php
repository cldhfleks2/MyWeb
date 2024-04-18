<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');
	$name = $_POST['name'];
	$password = $_POST['password'];
	$profile = $_POST['profile'];

	$sql_p = '';
	if($password){
		$sql_p = ", password = PASSWORD('$password') ";
	}

	$sql = "UPDATE user SET name = '$name', pic = '$profile' $sql_p WHERE idx = ".$_SESSION['user_info']['idx'];
	$mysqli->query($sql);
?>
<script>
	document.location.replace('/user/myInfo.php');
</script>