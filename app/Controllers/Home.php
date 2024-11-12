<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // 인덱스 뷰 페이지를 반환
        return view('home');
    }
}
