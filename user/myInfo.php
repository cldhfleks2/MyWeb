<?php

include_once('../head.php');
$sql = "SELECT * FROM user WHERE idx = ".$_SESSION['user_info']['idx'];
$myInfo = $mysqli->query($sql);
$myInfo = $myInfo->fetch_array();

?>
<link rel="stylesheet" type="text/css" href="/css/profile.css">
<div class="contents pf">
	<form method="POST" action="/user/proc/profile.proc.php" id="profile">
	<div class="profile" id="pfs" style="background-image:url(<?=pfChk($myInfo['pic'])?>)" onclick="fileUp()">
		<i class="fas fa-pencil-alt"></i>
	</div>
	<div class="name">
		<span id="nps"><?=$myInfo['name']?></span>
		<input type="text" value="<?=$myInfo['name']?>" name="name" id="nameChg" style="display:none; padding:5px; font-size:18px; max-width:300px; width:100%;"> <i class="fas fa-pencil-alt" onclick="changeName()" id="ncss"></i></div>
	<div class="infoChange">
		<div class="passOff btn cp" onclick="passOn()">비밀번호 수정</div>
		<div class="passOn">
			<div class="checkPassword">* 비밀번호 수정할때만 입력하세요.</div>
			<input type="password" name="password" id="password" placeholder="비밀번호 수정" /><br>
			<input type="password" id="passwordChk" placeholder="비밀번호 수정 확인"/><br><br>
			<a onclick="passOff()">비밀번호 변경 취소</a>
		</div>
		<div class="submitBtn btn cp" onclick="save()">저장</div>
		<input type="hidden" id="profileImg" name="profile" value="<?=$myInfo['pic']?>">

	</form>
		
	</div>
	<input type="file" id="file" onchange="fileUpload()" style="display:none">
</div> 
<script>
	function passOn(){
		$('.passOff').css('display', 'none');
		$('.passOn').css('display', 'block');
	}
	function passOff(){

		$('.passOff').css('display', 'block');
		$('.passOn').css('display', 'none');
		$('#password,#passwordChk').val('');
	}

	function save(){
		//닉네임변경
    	var originalName = $('#nps').text(); // 원래 닉네임
		var newName = $('#nameChg').val(); // 변경된 닉네임

    	if (newName !== originalName) { 
        	$.ajax({
            	url: '/user/proc/checkName.proc.php',
            	type: 'POST',
            	data: { name: newName },
            	success: function(response) {
            		var data = JSON.parse(response);
            		if(data.error) {
            			alert("중복되는 닉네임입니다."); 
            		} else if(data.success) { 
                    	changePassword(); //닉네임이 중복되지 않은 경우 비밀번호 변경 여부 확인
            		}
            	}
        	});
    	} else {
        	changePassword(); // 닉네임이 변경되지 않은 경우, 비밀번호 변경 여부 확인
    	}
	}
	function changeName(){
		$('#nps,#ncss').css('display', 'none');
		$('#nameChg').css('display', 'inline-block');
	}

	function changePassword(){
		if($('#password').val()){
			if(!confirm("비밀번호를 변경 하시겠습니까?")){
				return;
			}

			if($('#password').val().length < 8){
				alert('비밀번호는 8글자 이상이어야 합니다.');
				return;
			}

			if($('#password').val() != $('#passwordChk').val()){
				alert('비밀번호 확인이 서로 다릅니다.');
				return;
			}
		}
    	//최종적으로 프로필 변경폼 제출
		$("#profile").submit();
	}

	function fileUp(){
		$('#file').click();
	}

	function fileUpload(){ //write.php에서 가져와서 수정함
			var formData = new FormData();
			formData.append("file", $("#file")[0].files[0]);
			$.ajax({
				url: '/board/proc/fileUpload.php',
				type: 'POST',
				processData: false,
				contentType: false,
				data: formData,
				success: function(data) {
					$("#file").val('');
					if(data == 5){
						alert("파일이 너무 큽니다.");
					}else if(data == 6){
						alert("파일이 첨부되지 않았습니다.");
					}else if(data == 7){
						alert("파일이 제대로 업로드 되지 않았습니다.");
					}else{
						if(data.indexOf('/upload/') != -1){
							$('#profileImg').val(data);
							$('#pfs').css('background-image','url('+data+')');
						}else{
							alert("오류입니다!.");
						}
					}
				}
			});
		}
</script>
<?php
	include_once('../bottom.php');
?> 

