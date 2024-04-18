<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/head.php');
$id = $_GET['id'];
$sql = "SELECT * FROM contents WHERE idx = $id && is_view = 1";
$contents = $mysqli->query($sql);
$contents = $contents->fetch_array();
if(!$contents){
	exit('<script>alert("게시물이 없습니다."); history.back()</script>');
}
$sql = "SELECT * FROM board WHERE idx = ".$contents['board_id'];
$boardInfo = $mysqli->query($sql);
$boardInfo = $boardInfo->fetch_array();

$sql = "SELECT * FROM user WHERE idx = ".$contents['user_id'];
$userInfo = $mysqli->query($sql);
$userInfo = $userInfo->fetch_array();

$sql = "SELECT count(*) FROM comment WHERE contents_id = $id";
$commentCnt = $mysqli->query($sql);
$commentCnt = $commentCnt->fetch_array();
$commentCnt = $commentCnt[0];

if($boardInfo['idx'] == 4){
	$userInfo['name'] = 'Anonnymous';
	$userInfo['pic'] = 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg';
}

$cPage = 0;
$cLIMIT = 15; //댓글리스트 최대 갯수
$sql = "SELECT * FROM comment WHERE contents_id = $id ORDER BY idx DESC LIMIT $cPage, $cLIMIT";
$commentList = $mysqli->query($sql);


//ip당 한번 조회수 나오게 설정
$ip = $_SERVER['REMOTE_ADDR'];

$sql = "SELECT COUNT(*) FROM content_views WHERE content_id = $id AND ip = '$ip'";
$view = $mysqli->query($sql);
$viewCnt = $view->fetch_array()[0];
if($viewCnt == 0){
	$sql = "UPDATE contents SET view = view + 1 WHERE idx = $id";
    $mysqli->query($sql);

    // 조회한 IP 주소와 게시물 정보를 저장
    $sql = "INSERT INTO content_views (content_id, ip) VALUES ($id, '$ip')";
    $mysqli->query($sql);
}

$needLogin = false;
switch($boardInfo['write_role']){
	case 0: break;
	case 1:
	if(!@$_SESSION['user_info']){
		$needLogin = true;
	}
	continue;
}

//로그인 안했으면 하라고 표시
if(!@$_SESSION['user_info']){
	$needLogin = true;
}

$ctLike;
if(@$_SESSION['user_info']){
	$sql = "SELECT * FROM like_list WHERE contents_id = ".$contents['idx']." && user_id = ".$_SESSION['user_info']['idx'];
	$ctLike = $mysqli->query($sql);
	$ctLike = $ctLike->fetch_array();
}

/*$allowed_ext = array('jpg','jpeg','png','gif');
$fileListExp = explode('|',$contents['file']);
$fileList = array_values(array_filter($fileListExp));
$imageList = array(); //빈문자열이아닌 배열로 선언하라고 gpt
for($i = 0; $i < count($fileList); $i++){
	$ext = @array_pop(explode('.', $fileList[$i]));
	$ext = strtolower($ext);
	if(in_array($ext, $allowed_ext) ) {
		$imageList[] = $fileList[$i];
	}
}
head로 옮김
*/

//summernote로 생긴 html태그들을 실제로 적용시켜주는 함수
function display_content($content) {
    $content = htmlspecialchars_decode($content);
    $content = str_replace('&lt;', '<', $content);
    $content = str_replace('&gt;', '>', $content);
    $content = str_replace('&amp;', '&', $content);

    $content = preg_replace_callback('/<img[^>]+src="([^">]+)"/', function ($matches) {
        return '<img src="' . htmlspecialchars_decode($matches[1]) . '" />';
    }, $content);

    return $content;
}
?>

<link rel="stylesheet" type="text/css" href="/css/board.css">
<div class="contents read">
	<div class="bTitle">
		<a href="/board/list.php?id=<?=$boardInfo['idx']?>"><?=$boardInfo['name']?></a>	
		<?php if($contents['user_id'] == @$_SESSION['user_info']['idx']){ ?>
		<div class="writeBtn cp" style="margin-left:10px; background-color:#E75444" onclick="document.location.replace('/board/proc/delete.proc.php?id=<?=$id?>&bid=<?=$contents['board_id']?>')">
			<i class="fas fa-trash-alt"></i> 삭제
		</div>
		<div class="writeBtn cp" onclick="document.location.href='write.php?id=<?=$id?>&update=1'">
			<i class="fas fa-pen-square"></i> 수정
		</div>
		<?php } ?>
	</div>
	<div class="list">
		<div class="title">
			<?=$contents['title']?>
		</div>
		<div class="userInfoLayer">
			<div class="viewLayer">
				<?=display_datetime($contents['reg_date'])?><br><i class="fas fa-eye"></i> <?=$contents['view']?>

			</div>
			<div class="nameLayer">
				<div class="pic" style="background-image:url(<?=pfChk($userInfo['pic'])?>)"></div>
				<div class="name"><?=$userInfo['name']?></div>
			</div>
			
		</div>
		<div class="content">
			<!--본문내용출력-->
			<?= display_content($contents['contents']) ?> 

			<!--첨부된 파일들. 이미지출력-->
			<?php if(@count($imageList) > 0){ ?>
			<br><br><br><br>
				<?php for($i = 0 ; $i < count($imageList); $i++){ ?>
					<img src="<?=$imageList[$i]?>" style="max-width:100%">
				<?php } ?>
			<br><br>
			<?php } ?>

			<!--첨부된 파일의 요약(파란글)-->
			<?php 
			
			if(count($fileList) > 0) { ?>
				<br>
				<span style="font-size:12px">파일리스트</span> 
				<br>
				<?php for($i = 0 ; $i < count($fileList); $i++){ 
					$filename = str_replace('/upload/', '', $fileList[$i]);
					?>
					<a href="<?=$fileList[$i]?>"><?=$filename?></a><br>
				<?php } ?>
				<br><br>
			<?php } ?>
		</div>
		<div class="likeBtns">
			<div class="likeBtn commentBtnt"><i class="fas fa-comment"></i> <?=$commentCnt?></div>
			<!-- 좋아요 버튼 -->
			<div class="likeBtn dislike cp">
				<i class="far fa-thumbs-down <?php if(@$ctLike['is_like'] == '0') {?>on<?php } ?>" 
					id="ccc_dislike_<?=$contents['idx']?>" 
					onclick="<?php if($needLogin) {?>
						alert('로그인이 필요한 서비스입니다.');
						signInOn();
						<?php }else{ ?>
							contentsLike('dislike',<?=$contents['idx']?>,<?=$contents['like_cnt']?>,<?=$contents['dislike_cnt']?>,<?=$contents['dislike_cnt']?>,'<?=@$ctLike['is_like']?>'); 
							toggleLikeBtn('ccc_dislike_<?=$contents['idx']?>', '#F00707', 'dislike_<?=$contents['idx']?>', '#F00707');
						<?php } ?>">
				</i> 
				<span id="dislike_<?=$contents['idx']?>" 
					class="<?php if(@$ctLike['is_like'] == '0') {?>on<?php } ?>"><?=$contents['dislike_cnt']?>
				</span>
			</div>
			<!-- 싫어요 버튼 -->
			<div class="likeBtn like cp">
				<i class="fas fa-thumbs-up <?php if(@$ctLike['is_like'] == 1) {?>on<?php } ?>" 
					id="ccc_like_<?=$contents['idx']?>" 
					onclick="<?php if($needLogin) { ?>
						alert('로그인이 필요한 서비스입니다.');
						signInOn();
						<?php }else{ ?>
							contentsLike('like',<?=$contents['idx']?>,<?=$contents['like_cnt']?>,<?=$contents['dislike_cnt']?>,<?=$contents['like_cnt']?>,'<?=@$ctLike['is_like']?>'); 
							toggleLikeBtn('ccc_like_<?=$contents['idx']?>', '#0713F0', 'like_<?=$contents['idx']?>', '#0713F0');
						<?php } ?>">
				</i> 
				<span id="like_<?=$contents['idx']?>" 
					class="<?php if(@$ctLike['is_like'] == '1') {?>on<?php } ?>"><?=$contents['like_cnt']?>
				</span>
			</div>
		</div>
		<div class="comment">
			<?php if($needLogin) {?>
				<div class="commentWrite needLogin cp" onclick="signInOn()">
					댓글을 작성하시려면 로그인이 필요합니다.
				</div>
			<?php } else { ?>
				<div class="commentWrite"> 
					<input type="hidden" id="atUser"/> 
					<textarea id="commentArea" placeholder="댓글을 입력하세요..."></textarea>
					<div class="commentBtn cp" onclick="commentSave()">댓글작성</div>
				</div>
			<?php } ?>
			<div class="comment_list">
				<?php while($row = $commentList->fetch_array()){ 
					$sql = "SELECT * FROM user WHERE idx = ".$row['user_id'];
					$userInfoC = $mysqli->query($sql);
					$userInfoC = $userInfoC->fetch_array();

					
					if(@$_SESSION['user_info']){
						$sql = "SELECT * FROM comment_like_list WHERE comment_id = ".$row['idx']." && user_id = ".$_SESSION['user_info']['idx'];
						$cLike = $mysqli->query($sql);
						$cLike = $cLike->fetch_array();
					}

					if($boardInfo['idx'] == 4){
						$userInfoC['name'] = 'Anonnymous';
						$userInfoC['pic'] = 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Anonymous_emblem.svg';
					}

					
				?>
				<?php include($_SERVER['DOCUMENT_ROOT'].'/board/proc/comment_form.php') ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div> 
<script>
	var cmtSaveChk = false;
	var myprofile = '<?=pfChk(@$_SESSION['user_info']['pic'])?>';


	function commentSave(){
		if(cmtSaveChk){
			return; 
		}
		var comment = $('#commentArea').val();

		if(!comment){
			alert('댓글을 입력해주세요.');
			return;
		}
		cmtSaveChk = true;
		$.post("/board/proc/comment.proc.php", { comment: comment, cId: <?=$id?> }, function(v){
			cmtSaveChk = false;
			$('#commentArea').val('');
			$('.comment_list').prepend(v);
			
		});
	}

	function commentLike(v, idx, like_cnt, dislike_cnt, cnt, isLike){
		if(v == "like" && isLike == 1){
			cnt = like_cnt - 1;
		}
		if(v == "dislike" && isLike == '0'){
			cnt = dislike_cnt - 1;
		}		

		$('#c_like_'+idx+',#c_dislike_'+idx+',#cc_like_'+idx+',#cc_dislike_'+idx).css('color','#888');
		$('#c_like_'+idx).text(like_cnt);
		$('#c_dislike_'+idx).text(dislike_cnt);
		if(isLike == 1){
			$('#c_like_'+idx).text(like_cnt-1);
		}else if(isLike == '0'){
			$('#c_dislike_'+idx).text(dislike_cnt-1);
		}
		$('#c_'+v+'_'+idx).text(cnt+1).css('color', '#04B3A4'); 
		$('#cc_'+v+'_'+idx).css('color', '#04B3A4'); 


		$.post("/board/proc/cLike.proc.php",{idx:idx, v:v},function(v){
			console.log(v);
		});
	}

	function contentsLike(v, idx, like_cnt, dislike_cnt, cnt, isLike){
		if(v == "like" && isLike == 1){
			cnt = like_cnt - 1;
		}
		if(v == "dislike" && isLike == '0'){
			cnt = dislike_cnt - 1;
		}	
		$('#like_'+idx+',#dislike_'+idx+',#ccc_like_'+idx+',#ccc_dislike_'+idx).css('color', '#888');
		$('#like_'+idx).text(like_cnt);
		$('#dislike_'+idx).text(dislike_cnt);
		if(isLike == 1){
			$('#like_'+idx).text(like_cnt-1);
		}else if(isLike == '0'){
			$('#dislike_'+idx).text(dislike_cnt-1);
		}
		$('#'+v+'_'+idx).text(cnt+1).css('color', '#04B3A4');
		console.log('#'+v+'_'+idx);
		$('#ccc_'+v+'_'+idx).css('color', '#04B3A4');

		$.post("/board/proc/ctLike.proc.php",{idx:idx, v : v},function(v){
			console.log(v);
		});
	}

	function loadUser(idx, name){ //프로필 눌렀을때 유저 페이지 보여주기
		<?php if($boardInfo['idx'] != 4){ ?> //익명상태(익명게시판이용중)일때는 프로필 안보여줌
			$('#atUser').val(idx);
			$('#commentArea').val('@'+name+' ').focus();
		<?php } ?>
	}

	$(window).scroll(function() {   

		var scrolltop = $(document).scrollTop();
		var height = $(document).height();
		var height_win = $(window).height();
		if (Math.round(scrolltop) == height - height_win) {
			commentMore();
		}
	});  

	var cPage = <?=$cPage+1?>;
	var cLIMIT = <?=$cLIMIT?>;
	var cLoading = false;
	function commentMore(){
		if(cLoading){
			return;
		}
		cLoading = true;
		$.post('/board/proc/load.comment.php',{ cId : <?=$id?>, cPage : cPage, cLIMIT : cLIMIT},function(data){

			cLoading = false;
			$('.comment_list').append(data);
			cPage++;
		});
	}

	var cModiOn = false;
	function modiLayerOn(idx){
		if(!cModiOn){
			$('#modiLayer_'+idx).css('display','inline-block');
		}
	}

	function modiLayerOff(idx){
		$('#modiLayer_'+idx).css('display','none');
	}

	function commentModi(idx){
		cModiOn = true;
		var org = $('#cTexts_'+idx).text();
		$('#cTexts_'+idx).html('<textarea id="newC_'+idx+'">'+org+'</textarea><div class="commentBtn cp" onclick="commentModiSave('+idx+')">댓글수정</div>');
	}

	function commentModiSave(idx){
		var cmt = $('#newC_'+idx).val();
		$.post("/board/proc/comment.modi.php",{ comment : cmt, idx: idx}, function(v){
			if(v == 2){
				alert('오류입니다. 잠시후 다시 시도해주세요.');
			}else{
				$('#cTexts_'+idx).text(cmt);
				//cModiOn = false; // 수정 모드 비활성화
			}
		});
	}

	function commentDel(idx){
		$.post("/board/proc/comment.del.php",{ idx: idx}, function(v){
			if(v == 2){
				alert('오류입니다. 잠시후 다시 시도해주세요.');
			}else{
				$('#comment__'+idx).remove();

			}
		});
	}

	//좋아요싫어요버튼을 누르면 색상을 변경하는 코드
	function toggleLikeBtn(btnId, newColor, spanId, spanNewColor) {
		var btn = document.getElementById(btnId);
		btn.style.color = newColor;
		var span = document.getElementById(spanId);
		span.style.color = spanNewColor;
	}

</script>


<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bottom.php');
?>

