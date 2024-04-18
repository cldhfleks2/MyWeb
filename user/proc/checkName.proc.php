<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');

$name = $_POST['name'];

if(empty($name)) {
    echo json_encode(array('error' => '이름값이 전달되지 않았습니다.'));
    exit;
}

$sql = "SELECT COUNT(*) AS count FROM user WHERE name = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $name);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if($count > 0) {
    // 중복되는 이름이 있을 경우 'error' : '이미 존재하는 이름입니다. 다시 입력해주세요.' 데이터 반환
    echo json_encode(array('error' => '이미 존재하는 이름입니다. 다시 입력해주세요.'));
} else {
    echo json_encode(array('success' => '사용 가능한 이름입니다.'));
}

?>