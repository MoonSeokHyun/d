<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class SignController extends Controller
{
    public function logout()
{
    // 모든 세션 데이터 제거
    session()->destroy();

    // 로그아웃 후 홈 페이지로 리다이렉트
    return redirect()->to('/');
}

public function login()
{
    // 로그인 페이지를 렌더링하는 메소드
    return view('sign/login');
}

public function processLogin()
{
    helper(['form', 'url']);

    // AJAX 요청만 처리하도록 설정
    if ($this->request->isAJAX()) {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'password' => 'required|min_length[6]',
        ];

        // 입력 검증 실패 시
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => '아이디와 비밀번호를 확인해 주세요.'
            ]);
        }

        $model = new UserModel();
        $user = $model->where('username', $this->request->getVar('username'))->first();

        // 사용자 및 비밀번호 확인
        if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
            // 기본 이미지 설정
            $profileImage = $user['profile_image'] ?? '/img/basic.png';
            
            // 세션에 사용자 정보 저장
            session()->set([
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'nickname' => $user['nickname'],
                'profile_image' => $profileImage
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => "{$user['username']}님, 환영합니다!"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => '아이디 또는 비밀번호가 일치하지 않습니다.'
            ]);
        }
    }

    // AJAX 요청이 아닌 경우 오류 응답 반환
    return $this->response->setJSON([
        'success' => false,
        'error' => '잘못된 요청입니다.'
    ]);
}


    public function checkEmail()
    {
        $email = $this->request->getPost('email');
        $model = new UserModel();
        $user = $model->where('email', $email)->first();
        return $this->response->setJSON(['exists' => $user ? true : false]);
    }

    public function register()
    {
        helper(['form', 'url']);
    
        if ($this->request->isAJAX()) {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users_vegan.username]',
                'email'    => 'required|valid_email|is_unique[users_vegan.email]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'matches[password]',
            ];
    
            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $this->validator->getErrors()
                ]);
            } else {
                $model = new UserModel();
    
                $data = [
                    'username' => $this->request->getVar('username'),
                    'email'    => $this->request->getVar('email'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'phone'    => $this->request->getVar('phone'),
                ];
    
                $model->save($data);
    
                return $this->response->setJSON(['success' => true]);
            }
        } else {
            return view('sign/register');
        }
    }
    
    public function sendVerificationEmail()
    {
        $email = $this->request->getPost('email');
        $emailService = \Config\Services::email();
        $emailService->setFrom('gjqmaoslwj@naver.com', 'Vegan Community');
        $emailService->setTo($email);
        $emailService->setSubject('이메일 인증');
        $verificationCode = rand(100000, 999999);
        $emailService->setMessage('인증 코드: ' . $verificationCode);
    
        session()->set('verification_code', $verificationCode);
    
        if ($emailService->send()) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function verifyCode()
    {
        $code = $this->request->getPost('code');
        $storedCode = session()->get('verification_code');

        if ($code == $storedCode) {
            return $this->response->setJSON(['valid' => true]);
        } else {
            return $this->response->setJSON(['valid' => false]);
        }
    }

    public function checkUsername()
    {
        $username = $this->request->getPost('username');
        $model = new UserModel();
        $user = $model->where('username', $username)->first();
        return $this->response->setJSON(['exists' => $user ? true : false]);
    }

    // 아이디 찾기 - 이메일로 인증번호 전송
    public function sendIdVerification()
    {
        $email = $this->request->getPost('email');
        $model = new UserModel();
        $user = $model->where('email', $email)->first();
    
        if ($user) {
            $username = $user['username'];
    
            // 이메일 전송
            $emailService = \Config\Services::email();
            $emailService->setFrom('gjqmaoslwj@naver.com', 'Vegan Community');
            $emailService->setTo($email);
            $emailService->setSubject('아이디 찾기 결과');
            $emailService->setMessage("귀하의 아이디는 다음과 같습니다: $username");
    
            if ($emailService->send()) {
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => '이메일 전송에 실패했습니다.']);
            }
        }
    
        return $this->response->setJSON(['success' => false, 'message' => '등록된 이메일이 아닙니다.']);
    }
    
    // 아이디 찾기 - 인증번호 확인 후 아이디 반환
    public function verifyIdCode()
    {
        $code = $this->request->getPost('code');
        if ($code == session()->get('id_verification_code')) {
            $email = session()->get('id_verification_email');
            $model = new UserModel();
            $user = $model->where('email', $email)->first();
            return $this->response->setJSON(['success' => true, 'username' => $user['username']]);
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function sendPasswordVerification()
    {
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $model = new UserModel();
        $user = $model->where(['username' => $username, 'email' => $email])->first();
    
        if ($user) {
            $verificationCode = rand(100000, 999999);
            session()->set('password_verification_code', $verificationCode);
            session()->set('password_verification_email', $email);
    
            $emailService = \Config\Services::email();
            $emailService->setFrom('gjqmaoslwj@naver.com', 'Vegan Community');
            $emailService->setTo($email);
            $emailService->setSubject('비밀번호 찾기 인증번호');
            $emailService->setMessage("인증번호: $verificationCode");
    
            if ($emailService->send()) {
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }
    
    public function resetPassword()
    {
        if (session()->get('password_reset_allowed')) {  // 세션에 비밀번호 재설정이 허용되었는지 확인
            $newPassword = $this->request->getPost('new_password');
            $email = session()->get('password_verification_email');
    
            // 이메일을 기준으로 비밀번호 변경
            $model = new UserModel();
            $model->where('email', $email)
                  ->set(['password' => password_hash($newPassword, PASSWORD_DEFAULT)])
                  ->update();
    
            // 세션 값 삭제 후 성공 메시지 반환
            session()->remove(['password_reset_allowed', 'password_verification_email']);
            return $this->response->setJSON(['success' => true]);
        }
    
        // 세션 값이 유효하지 않은 경우 실패 메시지 반환
        return $this->response->setJSON(['success' => false]);
    }
    

    public function verifyPasswordCode()
{
    $code = $this->request->getPost('code');
    $storedCode = session()->get('password_verification_code');

    if ($code == $storedCode) {
        // 인증번호가 일치할 경우, 비밀번호 재설정 허용
        session()->set('password_reset_allowed', true);
        return $this->response->setJSON(['success' => true]);
    }
    
    // 인증번호가 일치하지 않는 경우
    return $this->response->setJSON(['success' => false]);
}


    public function forgotCredentials()
{
    return view('sign/forgot_credentials');
}
// Controller - SignController.php

public function callback()
{
    $code = $this->request->getGet('code');
    $state = $this->request->getGet('state');

    if (!$code || !$state) {
        return redirect()->to('/sign/login')->with('error', '네이버 로그인에 실패했습니다.');
    }

    $client_id = 'EepRLGsgU1qQXpkevnjh';
    $client_secret = 'sKLN1A6BWd';
    $redirect_uri = 'http://localhost:8080/callback';

    $tokenUrl = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uri}&code={$code}&state={$state}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseArr = json_decode($response, true);

    if (!isset($responseArr['access_token'])) {
        return redirect()->to('/sign/login')->with('error', '토큰을 받아오지 못했습니다.');
    }

    $accessToken = $responseArr['access_token'];

    // 액세스 토큰으로 사용자 정보 가져오기
    $userUrl = "https://openapi.naver.com/v1/nid/me";
    $headers = ["Authorization: Bearer {$accessToken}"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $userResponse = curl_exec($ch);
    curl_close($ch);

    $userResponseArr = json_decode($userResponse, true);

    if (isset($userResponseArr['response'])) {
        $userData = $userResponseArr['response'];
        $userId = $userData['id'] ?? null;
        $userEmail = $userData['email'] ?? null;
        $userName = $userData['name'] ?? null;
        $userNickname = $userData['nickname'] ?? null;
        $userProfileImage = $userData['profile_image'] ?? null;
        $userPhoneNumber = $userData['mobile'] ?? null;

        if (!$userEmail || !$userId) {
            return redirect()->to('/sign/login')->with('error', '네이버 로그인에 필요한 정보가 부족합니다.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $userEmail)->first();

        if (!$user) {
            $userModel->insert([
                'username' => $userId,
                'email' => $userEmail,
                'name' => $userName,
                'nickname' => $userNickname,
                'profile_image' => $userProfileImage,
                'phone_number' => $userPhoneNumber,
                'password' => password_hash('defaultpassword', PASSWORD_DEFAULT),
            ]);
            $user = $userModel->where('email', $userEmail)->first();
        }

        session()->set(['user_id' => $user['user_id'], 'username' => $user['username']]);
        return redirect()->to('/')->with('message', '로그인이 되었습니다.');
    } else {
        return redirect()->to('/sign/login')->with('error', '사용자 정보를 가져오지 못했습니다.');
    }
}


public function naverLogin()
{
    $email = $this->request->getPost('email');
    $name = $this->request->getPost('name');
    $nickname = $this->request->getPost('nickname');
    $profileImage = $this->request->getPost('profile_image');
    $phoneNumber = $this->request->getPost('phone_number');
    $userId = $this->request->getPost('id');  // 네이버 ID

    if (!$email || !$userId) {
        return $this->response->setJSON(['success' => false, 'message' => '잘못된 요청입니다.']);
    }

    $userModel = new UserModel();
    $user = $userModel->where('email', $email)->first();

    if (!$user) {
        $userModel->insert([
            'username' => $userId,
            'email' => $email,
            'name' => $name ?? '',
            'nickname' => $nickname ?? '',
            'profile_image' => $profileImage ?? '',
            'phone_number' => $phoneNumber ?? '',
            'password' => password_hash('defaultpassword', PASSWORD_DEFAULT),
        ]);
        $user = $userModel->where('email', $email)->first();
    }

    session()->set(['user_id' => $user['user_id'], 'username' => $user['username']]);

    return $this->response->setJSON(['success' => true, 'message' => '로그인이 되었습니다.']);
}

}


