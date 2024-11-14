<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <h2>로그인</h2>
    <form action="/sign/login" method="POST">
        <?= csrf_field(); ?>

        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email'); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">로그인</button>
    </form>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            console.log("Error:", "<?= session()->getFlashdata('error'); ?>");
            console.log("Error Code:", "<?= session()->getFlashdata('error_code'); ?>");
        </script>
    <?php endif; ?>

    <div class="mt-4">
        <p>회원가입이 아직 없으신가요?</p>
        <a href="/sign/register" class="btn btn-link">회원가입</a>
    </div>

    <div class="mt-2">
        <p>아이디 또는 비밀번호를 잊으셨나요?</p>
        <a href="/sign/forgot-credentials" class="btn btn-link">아이디/비밀번호 찾기</a>
    </div>

    <!-- 네이버 로그인 버튼 -->
    <div class="mt-4">
        <div id="naverIdLogin"></div>
    </div>
</div>

<!-- 네이버 로그인 JavaScript SDK 및 AJAX 에러 로깅 -->
// HTML 및 JavaScript 부분
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://static.nid.naver.com/js/naveridlogin_js_sdk_2.0.2.js"></script>
<script>
    var naverLogin = new naver.LoginWithNaverId({
        clientId: "EepRLGsgU1qQXpkevnjh",
        callbackUrl: "http://localhost:8080/callback",
        isPopup: false,
        loginButton: { color: "green", type: 3, height: 48 }
    });
    naverLogin.init();

    naverLogin.getLoginStatus(function(status) {
        if (status) {
            var userEmail = naverLogin.user.getEmail();
            var userId = naverLogin.user.getId();

            if (userEmail && userId) {
                $.ajax({
                    url: '/sign/naver-login',
                    type: 'POST',
                    data: {
                        email: userEmail,
                        id: userId,
                        name: naverLogin.user.getName(),
                        nickname: naverLogin.user.getNickName(),
                        profile_image: naverLogin.user.getProfileImage(),
                        phone_number: naverLogin.user.getMobile(),
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '/';
                        } else {
                            console.log("Error:", response.message);
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", status, error);
                        console.log("Server Response:", xhr.responseText);
                    }
                });
            } else {
                console.log("Error: 이메일 정보가 없습니다. 네이버 계정에서 이메일 제공 설정을 확인해 주세요.");
            }
        } else {
            console.log("Error: 네이버 로그인에 실패했습니다.");
        }
    });
</script>


<?= $this->endSection(); ?>
