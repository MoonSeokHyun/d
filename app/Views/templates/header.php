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
                <li><a href="/sign/login">로그인</a></li>
            </ul>
        </nav>
    </header>

    <?= $this->renderSection('content'); ?>

    <!-- jQuery를 여기로 이동 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>
