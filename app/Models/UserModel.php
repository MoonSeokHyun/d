<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users_vegan';  // 데이터베이스 테이블
    protected $primaryKey = 'user_id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $allowedFields = ['username', 'email', 'password', 'phone'];

    // 유효성 검사 규칙
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users_vegan.username]',
        'email'    => 'required|valid_email|is_unique[users_vegan.email]',
        'password' => 'required|min_length[6]|max_length[255]',
        'phone'    => 'permit_empty|min_length[10]|max_length[20]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // 이메일 중복 확인 메서드
    public function checkEmail($email)
    {
        return $this->where('email', $email)->first();  // 이메일이 이미 존재하는지 확인
    }
    public function checkUsername($username)
{
    return $this->where('username', $username)->first();  // 아이디가 이미 존재하는지 확인
}

}
