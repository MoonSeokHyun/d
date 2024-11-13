<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class SignController extends Controller
{
    public function login()
    {
        helper(['form', 'url']);

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

            $model = new UserModel();
            $user = $model->where('email', $this->request->getVar('email'))->first();

            if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
                session()->set('user_id', $user['user_id']);
                session()->set('username', $user['username']);
                return redirect()->to('/dashboard');
            } else {
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

}


