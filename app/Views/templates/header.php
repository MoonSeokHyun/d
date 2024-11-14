<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Vegan Community🇰🇷' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <link rel="stylesheet" href="/assets/css/register.css">
</head>
<body>
    <header>
        <nav>
            <h1>Vegan🍃 Korea🇰🇷</h1>
            <ul>
                <li><a href="/">홈</a></li>

                <?php if (session()->has('user_id')): ?>
                    <!-- 로그인한 사용자의 프로필 이미지와 닉네임 표시 -->
                    <li>
                        <a href="/myinfo">
                            <?php 
                            // 프로필 이미지 설정, 기본 이미지 제공
                            $profileImage = session()->get('profile_image') ?: '/img/basic.png';
                            ?>
                            <img src="<?= esc($profileImage) ?>" alt="프로필 이미지" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle;">
                            <?= esc(session()->get('nickname')) ?> <!-- 닉네임 표시 -->
                        </a>
                    </li>
                    <li><a href="/sign/logout">로그아웃</a></li>
                <?php else: ?>
                    <li><a href="/sign/login">로그인</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <?= $this->renderSection('content'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>
