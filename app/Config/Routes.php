<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// 로그인 페이지 보기
$routes->get('sign/login', 'SignController::login');

// 로그인 처리
$routes->post('sign/login', 'SignController::processLogin');

$routes->match(['get', 'post'], 'sign/register', 'SignController::register');

// 이메일 중복 체크
$routes->post('sign/check-email', 'SignController::checkEmail');

// 이메일 인증 코드 전송
$routes->post('sign/send-verification-email', 'SignController::sendVerificationEmail');

// 인증 코드 확인
$routes->post('sign/verify-code', 'SignController::verifyCode');
$routes->post('sign/check-username', 'SignController::checkUsername');  // 아이디 중복 체크


$routes->post('sign/send-id-verification', 'SignController::sendIdVerification');
$routes->post('sign/send-password-verification', 'SignController::sendPasswordVerification');
$routes->post('sign/verify-id-code', 'SignController::verifyIdCode');
$routes->post('sign/verify-password-code', 'SignController::verifyPasswordCode');
$routes->post('sign/reset-password', 'SignController::resetPassword');


$routes->get('sign/forgot-credentials', 'SignController::forgotCredentials');

// app/Config/Routes.php
$routes->get('/callback', 'SignController::callback');
// app/Config/Routes.php
$routes->post('/sign/naver-login', 'SignController::naverLogin');
$routes->get('/sign/logout', 'SignController::logout');

$routes->get('myinfo', 'MyInfoController::index'); // 내 정보 페이지
$routes->post('myinfo/update', 'MyInfoController::update'); // 내 정보 수정 처리
$routes->post('myinfo/uploadProfileImage', 'MyInfoController::uploadProfileImage');
