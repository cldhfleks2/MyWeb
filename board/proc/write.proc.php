<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

	$bId = $_POST['bId'];
	$uId = $_SESSION['user_info']['idx'];
	$title = $_POST['title'];
	$contents = $_POST['contents'];
	$fileList = $_POST['fileList'];
	$original_reg_date = $_POST['original_reg_date'];

	if(!$title){
		exit('<script>alert("제목을 입력 해주세요."); history.back()</script>');
	}
	if(!$contents){
		exit('<script>alert("내용을 입력 해주세요."); history.back()</script>');
	}
	
	if(!$_POST['update']){ //작성할때
		$sql = "SELECT * FROM user WHERE idx = ".$uId;
		$query = $mysqli->query($sql);
		$uInfo = $query->fetch_array();

		$sql = "SELECT * FROM board WHERE idx = ".$bId;
		$query = $mysqli->query($sql);
		$bInfo = $query->fetch_array();
		
		if($uInfo['is_admin'] == 0 && $bInfo['write_role'] == 9999){
			exit('<script>alert("작성불가 등급입니다."); history.back()</script>');
		}

		//새로 작성하니 당연히 idx는 +1된 값을 자동 지정
		$sql = "INSERT INTO contents (board_id, user_id, title, contents, file) VALUES ('$bId', '$uId', '$title', '$contents', '$fileList')";
		$query = $mysqli->query($sql);

		if(!$query){
			exit('<script>alert("오류. 다시 시도 하세요."); history.back()</script>');
		}

		$sql = "SELECT * FROM contents WHERE board_id = '$bId' && user_id = '$uId' ORDER BY idx DESC LIMIT 1";
		$query = $mysqli->query($sql);
		$contents_info = $query->fetch_array(MYSQLI_ASSOC);
		$id = $contents_info['idx'];
	} else { //수정할때
		$id = $_POST['id']; //수정할때는 id값이 들어왔을것.
		$sql = "UPDATE contents SET title = '$title', contents = '$contents', file = '$fileList',
									reg_date = '$original_reg_date', update_date = NOW() WHERE idx = $id";
		$query = $mysqli->query($sql);
	
		if(!$query){
			exit('<script>alert("오류. 다시 시도 하세요."); history.back()</script>');
		}

	}

	//php의변수스코프는 유연하므로 $id가 if문안에서 선언되어도 외부에서 사용가능함.
	$goPage = '/board/read.php?id='.$id; 
	header('location:'.$goPage); 
?> 
