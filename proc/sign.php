<!-- <?php
/*	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');
	if(!$_POST['email']){
		exit('<script>alert("이메일을 체크 해주세요."); history.back()</script>');
	}
	if(!$_POST['name']){
		exit('<script>alert("이름을 체크 해주세요."); history.back()</script>');
	}
	if(!$_POST['password']){
		exit('<script>alert("비밀번호를 체크 해주세요."); history.back()</script>');
	}
	if(!$_POST['passwordChk']){
		exit('<script>alert("비밀번호확인을 체크 해주세요."); history.back()</script>');
	}

	if($_POST['password'] != $_POST['passwordChk']){
		exit('<script>alert("비밀번호가 다릅니다. 다시 확인 해주세요."); history.back()</script>');
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

	$sql = "INSERT INTO user (id, name, password, reg_date) VALUES ('$email', '$name', PASSWORD('$password'), '$date')";
	$query = $mysqli->query($sql);

	if(!$query){
		exit('<script>alert("오류. 다시 시도 하세요."); history.back()</script>');
	}

	$sql = "SELECT * FROM user WHERE id = '$email'";
	$query = $mysqli->query($sql);
	$_SESSION['user_info'] = $query->fetch_array(MYSQLI_ASSOC);

	$prevPage = $_SERVER['HTTP_REFERER'];
	header('location:'.$prevPage);*/

?>
-->

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');


$check_email=preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i"
	, $_POST['email']);

if(!$check_email){
	exit('<script>alert("이메일 형식을 확인해 주세요."); history.back()</script>');
}


	// Prepared Statements 사용하여 SQL Injection 방지
$stmt = $mysqli->prepare("INSERT INTO user (id, name, password, reg_date) VALUES (?, ?, PASSWORD(?), ?)");
$stmt->bind_param("ssss", $email, $name, $password, $date);

// XSS 방지는 이미 global.php에서 처리됨

// 사용자 입력값 설정
$email = $_POST['email'];
$name = $_POST['name'];
$password = $_POST['password'];
$date = date("Y-m-d H:i:s");

if ($stmt->execute()) {
    // 회원가입 성공 시 로그인 처리 등 추가 작업 수행
	$sql = "SELECT * FROM user WHERE id = '$email'";
	$query = $mysqli->query($sql);
	$_SESSION['user_info'] = $query->fetch_array(MYSQLI_ASSOC);

	$prevPage = $_SERVER['HTTP_REFERER'];
	header('location:' . $prevPage);
} else {
    // 오류 처리
	exit('<script>alert("오류. 다시 시도 하세요."); history.back()</script>');
}

$stmt->close();

?>
