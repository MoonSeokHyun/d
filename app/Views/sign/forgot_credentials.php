<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <h2>아이디 / 비밀번호 찾기</h2>

    <!-- 아이디 찾기 섹션 -->
    <div class="mt-4">
        <h4>아이디 찾기</h4>
        <form id="findIdForm">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label for="email">이메일</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="button" id="sendIdVerification" class="btn btn-primary">아이디 찾기</button>
        </form>
    </div>

    <!-- 비밀번호 찾기 섹션 -->
    <div class="mt-4">
        <h4>비밀번호 찾기</h4>
        <form id="findPasswordForm">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label for="username">아이디</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_email">이메일</label>
                <input type="email" id="password_email" name="password_email" class="form-control" required>
            </div>
            <button type="button" id="sendPasswordVerification" class="btn btn-primary">비밀번호 찾기</button>
        </form>

        <!-- 인증번호 입력 후 비밀번호 재설정 섹션 -->
        <div id="verificationSection" style="display:none;" class="mt-4">
            <h5>인증번호 확인</h5>
            <form id="verifyCodeForm">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label for="verification_code">인증번호</label>
                    <input type="text" id="verification_code" name="verification_code" class="form-control" required>
                </div>
                <button type="button" id="verifyCodeButton" class="btn btn-primary">인증번호 확인</button>
            </form>
        </div>

        <!-- 비밀번호 재설정 섹션 -->
        <div id="resetPasswordSection" style="display:none;" class="mt-4">
            <h5>새로운 비밀번호 설정</h5>
            <form id="resetPasswordForm">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label for="new_password">새로운 비밀번호</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">비밀번호 확인</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <button type="button" id="resetPasswordButton" class="btn btn-success">비밀번호 변경</button>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        // 아이디 찾기
        $('#sendIdVerification').click(function () {
            var email = $('#email').val();
            $.ajax({
                url: '/sign/send-id-verification',
                type: 'POST',
                data: { email: email, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function (response) {
                    if (response.success) {
                        alert('아이디가 이메일로 발송되었습니다. 이메일을 확인해 주세요.');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        // 비밀번호 찾기 - 인증번호 전송
        $('#sendPasswordVerification').click(function () {
            var username = $('#username').val();
            var email = $('#password_email').val();
            $.ajax({
                url: '/sign/send-password-verification',
                type: 'POST',
                data: { username: username, email: email, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function (response) {
                    if (response.success) {
                        alert('인증번호가 이메일로 발송되었습니다. 이메일을 확인해 주세요.');
                        $('#verificationSection').show();
                    } else {
                        alert('등록되지 않은 정보입니다.');
                    }
                }
            });
        });

        // 인증번호 확인
        $('#verifyCodeButton').click(function () {
            var code = $('#verification_code').val();
            $.ajax({
                url: '/sign/verify-password-code',
                type: 'POST',
                data: { code: code, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function (response) {
                    if (response.success) {
                        alert('인증번호가 확인되었습니다. 새로운 비밀번호를 입력해 주세요.');
                        $('#resetPasswordSection').show();
                    } else {
                        alert('인증번호가 일치하지 않습니다.');
                    }
                }
            });
        });

        // 비밀번호 재설정
        $('#resetPasswordButton').click(function () {
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();

            if (newPassword !== confirmPassword) {
                alert('비밀번호가 일치하지 않습니다.');
                return;
            }

            $.ajax({
                url: '/sign/reset-password',
                type: 'POST',
                data: { new_password: newPassword, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function (response) {
                    if (response.success) {
                        alert('비밀번호가 성공적으로 변경되었습니다.');
                        window.location.href = '/sign/login';
                    } else {
                        alert('비밀번호 변경에 실패했습니다.');
                    }
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>
