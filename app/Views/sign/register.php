<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <h2>회원가입</h2>
    <form action="/sign/register" method="POST" id="registerForm">
        <?= csrf_field(); ?>

        <div class="form-group">
            <label for="username">아이디</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= old('username'); ?>" required>
            <span id="usernameMessage" style="color:red; display:none;">이 아이디는 이미 사용 중입니다.</span>
            <span id="usernameSuccess" style="color:green; display:none;">사용 가능한 아이디입니다.</span>
        </div>

        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email'); ?>" required>
            <span id="emailMessage" style="color:red; display:none;">이 이메일은 이미 사용 중입니다.</span>
            <span id="emailSuccess" style="color:green; display:none;">사용 가능한 이메일입니다.</span>
        </div>

        <div class="form-group">
            <button type="button" id="sendEmailBtn" class="btn btn-secondary">이메일 인증</button>
        </div>

        <div class="form-group" id="verificationGroup" style="display:none;">
            <label for="verification_code">인증 코드</label>
            <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="인증 코드를 입력하세요" required>
            <span id="verificationMessage" style="color:red; display:none;">인증 코드가 일치하지 않습니다.</span>
            <button type="button" id="verifyBtn" class="btn btn-success">인증 완료</button>
        </div>

        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">비밀번호 확인</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="phone">휴대폰 번호 (선택)</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone'); ?>">
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">가입하기</button>
    </form>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mt-3">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
   $(document).ready(function () {
        // 이메일 중복 체크
        $('#email').on('input', function () {  // 'blur' 대신 'input' 이벤트 사용
            var email = $(this).val();
            if (email) {
                $.ajax({
                    url: '/sign/check-email',  // 이메일 중복 체크 서버 경로
                    type: 'POST',
                    data: { email: email, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                    success: function (response) {
                        if (response.exists) {
                            $('#emailMessage').show();
                            $('#emailSuccess').hide();
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            $('#emailMessage').hide();
                            $('#emailSuccess').show();
                            $('#submitBtn').prop('disabled', false);
                        }
                    }
                });
            } else {
                $('#emailMessage').hide();
                $('#emailSuccess').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });

        // 아이디 중복 체크
        $('#username').on('input', function () {  // 'blur' 대신 'input' 이벤트 사용
            var username = $(this).val();
            if (username) {
                $.ajax({
                    url: '/sign/check-username',  // 아이디 중복 체크 서버 경로
                    type: 'POST',
                    data: { username: username, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                    success: function (response) {
                        if (response.exists) {
                            $('#usernameMessage').show();
                            $('#usernameSuccess').hide();
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            $('#usernameMessage').hide();
                            $('#usernameSuccess').show();
                            $('#submitBtn').prop('disabled', false);
                        }
                    }
                });
            } else {
                $('#usernameMessage').hide();
                $('#usernameSuccess').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });

        // 이메일 인증 버튼 클릭
        $('#sendEmailBtn').click(function () {
            var email = $('#email').val();
            console.log("이메일 인증 버튼 클릭됨: " + email);

            if (email) {
                $.ajax({
                    url: '/sign/send-verification-email',  // 이메일 인증 코드 전송을 위한 서버 경로
                    type: 'POST',
                    data: { email: email, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                    success: function (response) {
                        console.log("서버 응답: ", response);
                        if (response.success) {
                            $('#sendEmailBtn').prop('disabled', true);
                            $('#sendEmailBtn').text('인증 이메일 발송 완료');
                            $('#verificationGroup').show();
                            $('#verificationMessage').hide();
                        } else {
                            alert('이메일 전송에 실패했습니다.');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX 요청에 오류 발생: " + textStatus + ": " + errorThrown);
                        alert('이메일 전송 중 오류가 발생했습니다.');
                    }
                });
            } else {
                alert('이메일을 입력해 주세요.');
            }
        });

        // 인증 코드 확인
        $('#verifyBtn').click(function () {
            var code = $('#verification_code').val();
            $.ajax({
                url: '/sign/verify-code',
                type: 'POST',
                data: { code: code, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                success: function (response) {
                    if (response.valid) {
                        $('#verificationMessage').hide();
                        $('#verificationGroup').hide();
                        $('#sendEmailBtn').hide();
                        $('<span id="verificationSuccess" style="color:green;">인증되었습니다!</span>').insertAfter('#verificationGroup');
                    } else {
                        $('#verificationMessage').show();
                    }
                }
            });
        });

        // 회원가입 폼 제출
        $('#registerForm').submit(function (e) {
            e.preventDefault();  // 기본 폼 제출 방지

            var formData = $(this).serialize(); // serialize()로 폼 데이터를 직렬화

            $.ajax({
                url: '/sign/register',
                type: 'POST',
                data: formData,
                success: function (response) {
                    console.log('회원가입 서버 응답: ', response);  // 서버 응답 확인
                    if (response.success) {
                        alert('회원가입이 완료되었습니다.');
                        window.location.href = '/login';  // 로그인 페이지로 리디렉션
                    } else {
                        alert('회원가입 실패');
                        if (response.message) {
                            alert('실패 이유: ' + response.message);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX 요청에 오류 발생: " + textStatus + ": " + errorThrown);
                    alert('서버 요청에 실패했습니다.');
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>
