<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class MyInfoController extends Controller
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/sign/login')->with('error', '로그인이 필요합니다.');
        }

        $model = new UserModel();
        $user = $model->find($userId);

        return view('myinfo/index', ['user' => $user]);
    }

// 예: 프로필 이미지 수정 후 세션 업데이트
public function uploadProfileImage()
{
    $userId = session()->get('user_id');
    if (!$userId) {
        return $this->response->setJSON(['success' => false, 'error' => '로그인이 필요합니다.']);
    }

    $file = $this->request->getFile('profile_image');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $newName = bin2hex(random_bytes(8)) . '.' . $file->getExtension();
        $thumbnailPath = FCPATH . 'uploads/user_thumbnails/';
        $file->move($thumbnailPath, $newName);

        $filePath = '/uploads/user_thumbnails/' . $newName;
        
        // DB 업데이트
        $model = new UserModel();
        $model->update($userId, ['profile_image' => $filePath]);
        
        // 세션에 새로운 프로필 이미지 경로 업데이트
        session()->set('profile_image', $filePath);

        return $this->response->setJSON(['success' => true, 'filePath' => $filePath]);
    }

    return $this->response->setJSON(['success' => false, 'error' => '파일 업로드 중 오류가 발생했습니다.']);
}

    public function update()
    {
        // 로그인된 사용자 ID를 세션에서 가져옴
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => '로그인이 필요합니다.']);
        }
    
        // 업데이트할 데이터 준비
        $data = [
            'nickname' => $this->request->getPost('nickname'),
            'phone' => $this->request->getPost('phone'),
            'name' => $this->request->getPost('name')
        ];
    
        // 모델 업데이트
        $model = new UserModel();
        $updateResult = $model->update($userId, $data);
    
        if ($updateResult) {
            return $this->response->setJSON(['success' => true, 'message' => '정보가 성공적으로 업데이트되었습니다.']);
        } else {
            return $this->response->setJSON(['success' => false, 'error' => '정보 업데이트에 실패했습니다.']);
        }
    }
}
