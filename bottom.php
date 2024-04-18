		<div class="bottom">
			<div class="bottom_sns">
				<a href="https://facebook.com/profile.php?id=100017112346944" target="_blank"><i class="fab fa-facebook-f"></i></a>
				<a href="https://www.instagram.com/cldhfleks2/" target="_blank"><i class="fab fa-instagram"></i></a>
			</div>
			<?=$site_setting['site_info']?>
		</div>
		</div>
		<div class="login cls loginLayer">
			<div class="login_inner">
				<script src="리캡챠링크"></script>
				<form method="post" action="/proc/login.php" name="login" onsubmit="checkSign('login'); return false;">
					<input type="text" name="email" id="email_login" value="<?=@$_GET['email']?>" placeholder="이메일" />
					<input type="password" name="password" id="password_login" placeholder="비밀번호" />
					<input type="hidden" id="g-recaptcha" name="g-recaptcha">
					<button type="submit">로그인</button>
					<hr/>
					<div class="signBtn signlink"><a onclick="signUpOn()">회원가입</a></div>
					<div class="idpwSearch signlink"><a href="/user/search_pw.php">비밀번호찾기</a></div>
				</form>
			</div>
		</div>
		<div class="login cls signLayer">
			<div class="login_inner">
				
				<form method="post" name="sign" action="/proc/sign.php" onsubmit="checkSign('sign'); return false; ">
					<input type="text" name="email" id="email_sign" value="<?=@$_GET['email']?>" placeholder="이메일(비밀번호찾기에 이용됩니다.)" />
					<input type="text" name="name" id="name_sign" value="<?=@$_GET['name']?>" placeholder="닉네임" />
					<input type="password" name="password" id="password_sign" placeholder="비밀번호 (8글자)" />
					<input type="password" name="passwordChk" id= "passwordChk_sign"placeholder="비밀번호확인 (8글자)" />
					<input type="checkbox" id="yk_agree" style="width:18px"><a href="/privacy/yakgwan.html" target="_blank">이용약관</a> 및 <a href="/privacy/privacy.html" target="_blank">개인정보 취급방침</a> 에 동의합니다.
					<button type="submit">회원가입</button>
					<hr/>
					<div class="signBtn signlink"><a onclick="signInOn()">로그인</a></div>
					<div class="idpwSearch signlink"><a href="/user/search_pw.php">비밀번호찾기</a></div>
				</form>
			</div>
		</div>
		<!-- <script type="text/javascript">
			grecaptcha.ready(function() {
				grecaptcha.execute('사이트키일듯?', {action: 'homepage'}).then(function(token){
					document.getElementById('g-recaptcha').value = token;
				});
			});
		</script> -->
		<script>

			var menuOnChk = false;
			function menuOn(){
				if(!menuOnChk){
					$('.bg').fadeIn();
					$('.menu').css('display', 'block').animate({"margin-left": '0'}, 200, function(){
						$('.menu').css('display', 'block')
					});
					menuOnChk = true;
		
				} else {
					$('.menu').animate({"margin-left": '-100%'}, 200, function(){
						$('.menu').css('display', 'none')
					});
					close_layer();
					menuOnChk = false;
				}
	
			}


			var profileOnChk = false;
			function openProfile(){

				if(!profileOnChk){
					profileOnChk = true;
					$('.userInfo').fadeIn();

				}else{
					profileOnChk = false;
					$('.userInfo').fadeOut();
				}

			}

			$('.contents').click(function(){
				profileOnChk = false;
				$('.userInfo').fadeOut();
			});

			function close_layer(){
				$('.cls').fadeOut();
				if(menuOnChk){
					$('.menu').animate({"margin-left": '-100%'}, function(){
						$('.menu').css('display', 'none')
					});
					menuOnChk = false;
				}
				
			}

			function signInOn(){
				close_layer();
				$('.bg,.loginLayer').fadeIn();
			}

			function signUpOn(){
				close_layer();
				$('.bg,.signLayer').fadeIn();
			}
			
			function checkSign(k){
				var email = $('#email_'+k).val();
				if(!email){
					alert('이메일을 입력해주세요.')
					return;
				}
				if(!CheckEmail(email)){
					alert('올바른 이메일 형식이 아닙니다.')
					return;
				}
				var password = $('#password_'+k).val();
				if(!password){
					alert('비밀번호를 입력해주세요.')
					return;
				}

				if(password.length < 8){
					alert("비밀번호가 8자 이상으로 입력하세요.")
					return;
				}

				if(k == 'sign'){
					var name = $('#name_'+k).val();
					if(!name){
						alert('이름을 입력해주세요.')
						return;
					}
					var passwordChk = $('#passwordChk_'+k).val();
					if(!passwordChk){
						alert('비밀번호 확인을 입력해주세요.')
						return;
					}
					if(password != passwordChk){
						alert("비밀번호가 다릅니다. 다시 확인 해주세요.");
						return;
					}
					if($('#yk_agree').is(":checked") == false){
						alert('개인정보 취급 방침 및 이용약관에 동의해주세요.');
						return;
					}

					var formData = new FormData();
					formData.append('name', name);
					$.ajax({
						url : '/user/proc/checkName.proc.php',
						type : 'POST',
						data : formData,
						processData: false,
            			contentType: false,
						success: function(response){
							var data = JSON.parse(response);
        					if(data.error) {
           						alert(data.error);
        					} else if(data.success) {
            					sign.submit(); //중복안되면 회원가입
        					}
						}
					});
					
				}else{
					login.submit();
				}
			}
			function CheckEmail(str){
				var reg_email = /^([0-9a-zA-Z_\.-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
				if(!reg_email.test(str)){
 					return false;
				}else{  
					return true;
				}
			}

			$(document).ready(function(){
				setTimeout(function(){
					$('.gsc-input').attr('placeholder', '검색');
				}, 500);

				if(window.location.hash == '#login'){
					signInOn();
				}else if(window.location.hash == '#sign'){
					signUpOn();
				}
			});

			function loadNoti(){ //알림 
				document.location.href="/user/mynoti.php";
			}
		</script>
	</body>
</html>