<?php
include_once('../head.php');
$sql = "SELECT *,u.name as uName, n.contents as nContents, c.idx as cIdx, n.reg_date as nDate FROM noti as n 
		INNER JOIN user as u ON(n.s_user_id = u.idx)
		INNER JOIN contents as c ON(c.idx = n.contents_id)
		WHERE r_user_id = ".$_SESSION['user_info']['idx']." 
		&& r_user_id != s_user_id 
		ORDER BY n.idx DESC"; //받은 유저가 나일경우. 내 알림만
$query = $mysqli->query($sql);

$sql = "UPDATE noti SET is_read = 1 WHERE r_user_id = ".$_SESSION['user_info']['idx']; //확인한순간 읽음을 표시
$mysqli->query($sql);
?>
<link rel="stylesheet" type="text/css" href="/css/profile.css">
<div class="contents pf">
	<div class="bTitle">
		<a href="javascript:document.location.reload()">내 알림</a>
	</div>
	<?php while($row = $query->fetch_array()) { ?>
		<div class="notiLayer cp" onclick="document.location.href='/board/read.php?id=<?=$row['cIdx']?>'">
			<div class="pic" style="background-image:url(<?=pfChk($row['pic'])?>)"></div>
			<div class="name"><?=$row['uName']?></div>
			<div class="date"><?=display_datetime($row['nDate'])?></div>
			<div class="contents"><?=$row['nContents']?></div>
		</div>
	<?php } ?>
</div> 
<?php
	include_once('../bottom.php');
?> 

