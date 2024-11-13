<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class SignController extends Controller
{
    public function login()
    {
        helper(['form', 'url']);

        // 로그인 폼 제출 시
        if ($this->request->getMethod() == 'post') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('sign/login', [
                    'validation' => $this->validator,
                ]);
            }

            // 로그인 처리
            $model = new UserModel();
            $user = $model->where('email', $this->request->getVar('email'))->first();

            if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
                // 로그인 성공
                session()->set('user_id', $user['user_id']);
                session()->set('username', $user['username']);
                return redirect()->to('/dashboard');  // 로그인 후 대시보드로 이동
            } else {
                // 로그인 실패
                session()->setFlashdata('error', '이메일 또는 비밀번호가 일치하지 않습니다.');
                return redirect()->back()->withInput();
            }
        }

        return view('sign/login');
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

        // 유효성 검사
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users_vegan.username]',
            'email'    => 'required|valid_email|is_unique[users_vegan.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'password_confirm' => 'matches[password]',
        ];

        if (!$this->validate($rules)) {
            return view('sign/register', [
                'validation' => $this->validator,
            ]);
        } else {
            $model = new UserModel();

            // 비밀번호 암호화 및 데이터 저장
            $data = [
                'username' => $this->request->getVar('username'),
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'phone'    => $this->request->getVar('phone'),
            ];

            // 사용자 저장
            $model->save($data);

            // 성공 메시지 후 로그인 페이지로 리디렉션
            return redirect()->to('/login')->with('success', '회원가입이 완료되었습니다.');
        }
    }

    public function sendVerificationEmail()
    {
        $email = $this->request->getPost('email');
    
        // 이메일 전송 로직
        $emailService = \Config\Services::email();
        $emailService->setFrom('gjqmaoslwj@naver.com', 'Vegan Community');
        $emailService->setTo($email);
        $emailService->setSubject('이메일 인증');
        $verificationCode = rand(100000, 999999);  // 인증 코드 생성
        $emailService->setMessage('인증 코드: ' . $verificationCode);
    
        // 인증 코드 세션에 저장
        session()->set('verification_code', $verificationCode);
    
        // 이메일 전송 및 에러 처리
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
    
    // 아이디가 데이터베이스에 이미 존재하는지 확인
    $user = $model->where('username', $username)->first();
    
    if ($user) {
        // 아이디가 존재하면, 이미 사용 중이라고 반환
        return $this->response->setJSON(['exists' => true]);
    } else {
        // 아이디가 존재하지 않으면 사용 가능하다고 반환
        return $this->response->setJSON(['exists' => false]);
    }
}

}
