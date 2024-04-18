<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php'); 
	if(!$_POST['email']){
		exit('<script>alert("이메일을 체크 해주세요."); history.back()</script>');
	}

	if(!$_POST['password']){
		exit('<script>alert("비밀번호를 체크 해주세요."); history.back()</script>');
	}

	$check_email=preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i"
		, $_POST['email']);

	if(!$check_email){
	   exit('<script>alert("이메일 형식을 확인해 주세요."); history.back()</script>');
	}

	//XSS과 SQL인젝션 방지
	foreach($_POST as $key => $value){
		$_POST[$key] = $mysqli->escape_string(htmlspecialchars($value));
	}
	foreach($_GET as $key => $value){
		$_GET[$key] = $mysqli->escape_string(htmlspecialchars($value));
	}

	$sql = "SELECT * FROM user WHERE id = '$email' && password = PASSWORD('$password')";
	$query = $mysqli->query($sql);
	if(!$query){
		exit('<script>alert("오류. 다시 시도 하세요."); history.back()</script>');
	}

	$userInfo = $query->fetch_array(MYSQLI_ASSOC);
	if(!$userInfo){
		exit('<script>alert("이메일 혹은 비밀번호가 잘못되었습니다.\n다시 입력해주세요."); history.back()</script>');
	}

	$_SESSION['user_info'] = $userInfo;

	//$sql = "INSERT INTO user (last_date) VALUES ('$date')"; //수정 하기전 원본

	$sql = "UPDATE user SET last_date = '$date' WHERE idx = ".$_SESSION['user_info']['idx']; //마지막접속날짜 기록하도록
	$query = $mysqli->query($sql);

	//이전 페이지로 리다이렉트
	$prevPage = $_SERVER['HTTP_REFERER'];
	header('location:'.$prevPage);
?>