<div class="comments cp" id="comment__<?=$row['idx']?>" <?php if($row['user_id'] == @$_SESSION['user_info']['idx']){?> onmouseover="modiLayerOn('<?=$row['idx']?>')" onmouseout="modiLayerOff('<?=$row['idx']?>')"<?php } ?>>
	<div class="cInfo">
		<i class="fas fa-thumbs-up cp <?php if(@$cLike['is_like'] == 1) {?>on<?php } ?>" id="cc_like_<?=$row['idx']?>" onclick="<?php if($needLogin) { ?>alert('로그인이 필요한 서비스입니다.');signInOn();<?php }else{ ?>commentLike('like',<?=$row['idx']?>,<?=$row['like_cnt']?>,<?=$row['dislike_cnt']?>,<?=$row['like_cnt']?>,'<?=@$cLike['is_like']?>')<?php } ?>"></i> 
		<span id="c_like_<?=$row['idx']?>" class="<?php if(@$cLike['is_like'] == 1) {?>on<?php } ?>"><?=$row['like_cnt']?></span> · 
		<i class="far fa-thumbs-down cp <?php if(@$cLike['is_like'] == '0') {?>on<?php } ?>" id="cc_dislike_<?=$row['idx']?>" onclick="<?php if($needLogin) { ?>alert('로그인이 필요한 서비스입니다.');signInOn();<?php }else{ ?>commentLike('dislike',<?=$row['idx']?>,<?=$row['like_cnt']?>,<?=$row['dislike_cnt']?>,<?=$row['dislike_cnt']?>,'<?=@$cLike['is_like']?>')<?php } ?>"></i> 
		<span id="c_dislike_<?=$row['idx']?>" class="<?php if(@$cLike['is_like'] == '0') {?>on<?php } ?>"><?=$row['dislike_cnt']?></span>
		<br>
		<?=display_datetime($row['reg_date'])?>
	</div>
	<div class="pic" style="background-image:url(<?=pfChk($userInfoC['pic'])?>)"></div>
	<div class="commentCon">
		<a onclick="loadUser(<?=$userInfoC['idx']?>,'<?=$userInfoC['name']?>')"><?=$userInfoC['name']?></a>
		<span id="cTexts_<?=$row['idx']?>">
			<?php
            // 멘션된 상대방의 닉네임을 찾아내고 파란색으로 표시
			$commentText = url_auto_link($row['comment']);
			$commentText = preg_replace('/@(\w+)/', '<span style="color:#0FBAAB;">@$1</span>', $commentText);
			echo $commentText;
			?>
        </span>
		<span id="modiLayer_<?=$row['idx']?>" style="display:none">
			<i class="fas fa-pencil-alt" onclick="commentModi(<?=$row['idx']?>)"></i>
			<i class="fas fa-times" onclick="commentDel(<?=$row['idx']?>)"></i>
		</span>
	</div>
	
</div>