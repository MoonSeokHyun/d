<!-- login.php -->

<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <h2>로그인</h2>

    <!-- 일반 로그인 폼 -->
    <form id="loginForm" method="POST">
        <?= csrf_field(); ?>

        <div class="form-group">
            <label for="username">아이디</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= old('username'); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="button" id="loginButton" class="btn btn-primary">로그인</button>
    </form>

    <div class="mt-4">
        <p>회원가입이 아직 없으신가요?</p>
        <a href="/sign/register" class="btn btn-link">회원가입</a>
    </div>

    <div class="mt-2">
        <p>아이디 또는 비밀번호를 잊으셨나요?</p>
        <a href="/sign/forgot-credentials" class="btn btn-link">아이디/비밀번호 찾기</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$('#loginButton').click(function(event) {
    event.preventDefault(); // 기본 폼 제출 동작 방지

    var username = $('#username').val();
    var password = $('#password').val();

    $.ajax({
        url: '/sign/login',
        type: 'POST',
        contentType: 'application/json', // JSON 형식으로 Content-Type 설정
        dataType: 'json', 
        data: JSON.stringify({
            username: username,
            password: password,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }),
        success: function(response) {
            if (response.success) {
                alert(response.message);
                window.location.href = '/';
            } else {
                alert(response.error);
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", status, error);
            alert("로그인 중 오류가 발생했습니다. 다시 시도해 주세요.");
        }
    });
});

</script>

<?= $this->endSection(); ?>
