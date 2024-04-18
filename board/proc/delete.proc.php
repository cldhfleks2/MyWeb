<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/global.php');
	$id = $_GET['id'];
	$bid = $_GET['bid'];
	$sql = "UPDATE contents SET is_view = 0 WHERE idx = $id";
	$mysqli->query($sql);

?>
<script>
	alert('삭제되었습니다.');
	document.location.replace('board/list.php?id=<?=$bid?>');
</script>