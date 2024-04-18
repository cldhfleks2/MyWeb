<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/head.php');

	if(!@$_GET['update']){ //게시글 작성할때
		$bId = $_GET['id'];
		$sql = "SELECT * FROM board WHERE idx = $bId";
		$boardInfo = $mysqli->query($sql); 
		$boardInfo = $boardInfo->fetch_array();
		switch($boardInfo['write_role']){
			case 0: break;
			case 1:
				if(!$_SESSION['user_info']){
					exit('<script>alert("로그인이 필요한 서비스입니다.");document.location.replace("/#login")</script>');
				}
				continue;

			case 9999:
				$sql = "SELECT * FROM user WHERE idx = ".$_SESSION['user_info']['idx'];
				$user_info = $mysqli->query($sql);
				$user_info = $user_info->fetch_array();

				if($user_info['is_admin'] != 1){
					exit('<script>alert("등급이 맞지않아 글 작성이 불가능합니다.");history.back()</script>');
				}
				break;
		}
	} else { //게시글 수정할때
		$id = $_GET['id'];
		$sql = "SELECT * FROM contents WHERE idx = $id";
		$contents = $mysqli->query($sql);
		$contents = $contents->fetch_array();

		$bId = $contents['board_id'];
		$sql = "SELECT * FROM board WHERE idx = $bId";
		$boardInfo = $mysqli->query($sql); 
		$boardInfo = $boardInfo->fetch_array();

		$original_reg_date = $contents['reg_date'];
	}

?>
<link rel="stylesheet" type="text/css" href="/css/board.css">
<!-- include libraries(bootstrap) -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="contents write">
	<div class="bTitle">
		<a href="/board/list.php?id=<?=$boardInfo['idx']?>"><?=$boardInfo['name']?></a>
	</div>
	<div class="list">
		<!--글작성 폼-->
		<form method="POST" action="/board/proc/write.proc.php" name="write" enctype="multipart/form-data">
			<!--폼으로 전달할 데이터들-->
			<input type="hidden" name="bId" value="<?=$bId?>"/>
			<input type="hidden" name="id" value="<?=@$id?>"/>
			<input type="hidden" name="update" value="<?=@$_GET['update']?>"/>
`			<input type="hidden" name="original_reg_date" value="<?=@$original_reg_date?>"/>
			<div class="title">
				<input type="text" name="title" id="title" placeholder="제목" value="<?=@$contents['title']?>">
			</div>
			<div class="content"> 
				<textarea id="contents" name="contents"  placeholder="내용"><?=@$contents['contents']?></textarea>
			</div>
			<input type="hidden" name="fileList" id="fileList"><!--첨부파일리스트값이 있으면 가져옴-->
		</form>
		<div class="file">
			<input type="file" id="file" onchange="fileUpload()"> 
			<div class="uploading" style="display: none;">업로드 중...</div>
		</div>
		<div class="fileList" style="display: none;">
			<br>
			<span style="font-size: 12px;">파일 리스트</span> 
			<br>
		</div>
		<div class="saveLayer">
			<div class="cancelBtn cp" onclick="history.back()">취소</div>
			<div class="saveBtn cp" onclick="save()">저장</div>

		</div>
	</div>
</div> 
<script>

	$(document).ready(function() {
		//summernote 실행
		$('#contents').summernote({
			placeholder: '내용',
			tabsize: 2,
			height: 500,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video']],
				['view', ['fullscreen', 'codeview', 'help']]
				],

			callbacks: {
				onImageUpload: function (files, editor, welEditable) {
                    // 파일 업로드 (다중 업로드를 위해 반복문 사용)
					for (var i = files.length - 1; i >= 0; i--) {
						uploadSummernoteImageFile(files[i], this);
					}
				},
			},
		});

		// 기존 파일 리스트가 테이블에 존재할 경우
		var existingFileList = "<?= isset($contents['file']) ? $contents['file'] : '' ?>";
		if (existingFileList) {
			var fileListArray = existingFileList.split('|');
			fileListArray = fileListArray.filter(function(file) {
            return file !== ''; // 빈 문자열 제거
        });

        // 파일 리스트 화면에 표시(파란링크)
		for (var i = 0; i < fileListArray.length; i++) {
			var fileURL = fileListArray[i];
			var filename = fileURL.substring(fileURL.lastIndexOf('/') + 1);
			$('.fileList').append('<div class="fileItem"><a href="' + fileURL + '">' + filename + '</a><i class="fas fa-times cp" onclick="imgDel(\'' + fileURL + '\', \'' + filename + '\')" aria-hidden="true"></i></div>');
			// 기존 파일 리스트를 먼저 불러옴
			$('#fileList').val(function(index, currentValue) {
                return currentValue + '|' + fileURL;
            });
		}
		$('.fileList').css('display', 'block');


		}
	})


    function uploadSummernoteImageFile(file, el) {
        var data = new FormData();
        data.append('file', file);
        $.ajax({
            url: '/board/proc/imageUpload.php',
            type: 'POST',
            data: data,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            success: function (data) {
                $(el).summernote(
                    'editor.insertImage',
                    data
                );
            }
        });
    }

	function save(){
		var title = $('#title').val();
		var contents = $('#contents').val();

		if(!title){
			alert('제목을 입력해주세요.');
			return;
		}
		if(!contents){
			alert('내용을 입력해주세요.');
			return;
		}

        write.submit();
	}

	function fileUpload() {
		$("#file").css('display', 'none');
		$('.uploading').css('display', 'block');
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
				$("#file").css('display', 'block');
				$('.uploading').css('display', 'none');
				if(data == 5) {
					alert("파일이 너무 큽니다.");
				} else if(data == 6) {
					alert("파일이 첨부되지 않았습니다.");
				} else if(data == 7) {
					alert("파일이 제대로 업로드되지 않았습니다.");
				} else {
					//파일선택으로 할때마다 파일리스트에 추가
					if(data.indexOf('/upload/') != -1) {
						//기존 파일리스트에 구분자로 '|' 넣고 추가
						$('#fileList').val($('#fileList').val()+'|'+data+'|'); 
						var filename = data.substring(data.lastIndexOf('/') + 1);
						var fileURL = data;
						$('.fileList').append('<div class="fileItem"><a href="' + fileURL + '">' + filename + '</a><i class="fas fa-times cp" onclick="imgDel(\'' + fileURL + '\', \'' + filename + '\')" aria-hidden="true"></i></div>');
						$('.fileList').css('display', 'block');
					} else {
						alert("오류입니다.");
					}
				}
			}
		});
	}

	function imgDel(filePath, fileName) {
		if (confirm('"' + fileName + '" 파일을 삭제하시겠습니까?')) {
			$.post("/board/proc/fileDel.php", { filePath: filePath }, function(data) {
				if (data == 1) {
					$('.fileItem').each(function() {
						if ($(this).find('a').attr('href') == filePath) {
							$(this).remove();
							// 파일 리스트에서 해당 파일 제거
							var fileListVal = $('#fileList').val();
							var deletedFile = '|' + filePath + '|';
							fileListVal = fileListVal.replace(deletedFile, '|');
							$('#fileList').val(fileListVal);
						}
					});
				} else {
					alert('오류!. 파일 삭제에 실패했습니다.');
				}
			});
		}
	}



</script>

<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/bottom.php');
?> 

