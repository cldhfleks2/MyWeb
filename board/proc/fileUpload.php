<?php
// 설정
$uploads_dir = '/upload';

// 변수 정리
$error = $_FILES['file']['error'];
$name = $_FILES['file']['name'];
$ext = array_pop(explode('.', $name));
 
// 오류 확인
if( $error != UPLOAD_ERR_OK ) {
	switch( $error ) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			echo "5";//"파일이 너무 큽니다. ($error)";
			break;
		case UPLOAD_ERR_NO_FILE:
			echo "6";//"파일이 첨부되지 않았습니다. ($error)";
			break;
		default:
			echo "7";//"파일이 제대로 업로드되지 않았습니다. ($error)";
	}
	exit;
}

// 파일 이동
$uniqid = uniqid();
$nbame = $uniqid.'_'.$name;
move_uploaded_file( $_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploads_dir."/$nbame");

echo "$uploads_dir/$nbame";
?>