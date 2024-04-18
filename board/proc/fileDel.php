<?php
// 파일 삭제 처리

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["filePath"])) {
    $filePath = $_POST["filePath"];
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filePath)) {
        if (unlink($_SERVER['DOCUMENT_ROOT'] . $filePath)) {
            // 파일 삭제 성공
            echo 1;
        } else {
            // 파일 삭제 실패
            echo 0;
        }
    } else {
        // 파일이 존재하지 않음
        echo -1;
    }
} else {
    // 올바른 요청이 아님
    echo -2;
}
?>
