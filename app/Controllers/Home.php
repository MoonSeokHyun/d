<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ImageVeganModel;

class Home extends BaseController
{
    public function index()
    {
        return view('home', ['title' => 'Vegan Community']);
    }

    public function getPosts()
    {
        $category_id = $this->request->getGet('category_id') ?? 0;
        $db = \Config\Database::connect();

        // 게시글 가져오기
        $builder = $db->table('posts_vegan');
        $builder->select('posts_vegan.*, categories_vegan.name as category_name');
        $builder->join('categories_vegan', 'categories_vegan.category_id = posts_vegan.category_id', 'left');
        $builder->orderBy('posts_vegan.created_at', 'DESC');

        if ($category_id != 0) {
            $builder->where('posts_vegan.category_id', $category_id);
        }

        $posts = $builder->get()->getResultArray();

        // 이미지 데이터 가져오기
        $imageModel = new ImageVeganModel();
        foreach ($posts as &$post) {
            $post['images'] = $imageModel->where('post_id', $post['post_id'])->findAll();
        }

        return $this->response->setJSON($posts);
    }

    public function createPost()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please log in first.']);
        }

        $postModel = new PostModel();
        $imageModel = new ImageVeganModel();

        // 게시글 데이터 처리
        $content = $this->request->getPost('content');
        $category_id = $this->request->getPost('category_id');

        $postModel->insert([
            'user_id' => session()->get('user_id'),
            'category_id' => $category_id,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 삽입된 게시글 ID 가져오기
        $post_id = $postModel->insertID();

        // 이미지 데이터 처리
        $images = $this->request->getFileMultiple('images'); // 다중 파일 가져오기
        $uploadPath = FCPATH . 'uploads/' . date('Y-m-d'); // public/uploads/YYYY-MM-DD 디렉토리
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($images) {
            foreach ($images as $image) { // 모든 파일 처리
                if ($image->isValid() && !$image->hasMoved()) {
                    $imageName = $image->getRandomName();
                    $image->move($uploadPath, $imageName);
        
                    // 중복 체크: 동일한 경로가 있는지 확인
                    $existingImage = $imageModel->where('file_path', '/uploads/' . date('Y-m-d') . '/' . $imageName)->first();
                    if (!$existingImage) {
                        $imageModel->insert([
                            'post_id' => $post_id,
                            'file_path' => '/uploads/' . date('Y-m-d') . '/' . $imageName,
                        ]);
                    }
                }
            }
        }
        

        return $this->response->setJSON(['success' => true]);
    }
}
