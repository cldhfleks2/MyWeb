<?php

// 댓글 내에서 멘션된 사용자를 찾아냄
preg_match_all("/@(\w+)/", $comment, $matches);
$mentioned_users = $matches[1];

// 중복된 알림을 방지하기 위한 배열 선언
$notified_users = array();

// 멘션된 사용자에게 알림을 추가
foreach ($mentioned_users as $mentioned_user) {
    // 멘션된 사용자의 ID를 가져옴
    $sql = "SELECT idx FROM user WHERE name = '$mentioned_user'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $mentioned_user_id = $row['idx'];

    // 멘션된 사용자가 댓글 작성자가 아니고, 아직 알림을 추가하지 않은 경우에만 알림 추가
    if ($mentioned_user_id != $uId && !in_array($mentioned_user_id, $notified_users)) {
        // 알림 데이터베이스에 삽입
        $sql = "INSERT INTO noti (r_user_id, s_user_id, contents_id, contents) VALUES ('$mentioned_user_id', '$uId', '$contents_id', '$notiContents')";
        $query = $mysqli->query($sql);

        // 알림을 추가한 사용자를 배열에 추가하여 중복을 방지함
        $notified_users[] = $mentioned_user_id;
    }
}

// 내가 작성한 게시물에 다른 사용자가 댓글을 단 경우에만 알림 추가
if ($r_user_id != $uId && !in_array($r_user_id, $notified_users)) {
    // 알림 데이터베이스에 삽입
    $sql = "INSERT INTO noti (r_user_id, s_user_id, contents_id, contents) VALUES ('$r_user_id', '$uId', '$contents_id', '$notiContents')";
    $query = $mysqli->query($sql);

    // 알림을 추가한 사용자를 배열에 추가하여 중복을 방지함
    $notified_users[] = $r_user_id;
}
?>
