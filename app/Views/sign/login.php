<?= $this->extend('templates/header'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <!-- 로그인 폼 -->
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
        <div class="alert alert-danger mt-3">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- 회원가입 및 아이디/비밀번호 찾기 링크 -->
    <div class="mt-4">
        <p>회원가입이 아직 없으신가요?</p>
        <a href="/sign/register" class="btn btn-link">회원가입</a>
    </div>

    <!-- 아이디/비밀번호 찾기 페이지로 이동하는 링크 추가 -->
    <div class="mt-2">
        <p>아이디 또는 비밀번호를 잊으셨나요?</p>
        <a href="/sign/forgot-credentials" class="btn btn-link">아이디/비밀번호 찾기</a>
    </div>
</div>

<?= $this->endSection(); ?>
