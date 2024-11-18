<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ImageVeganModel;

class Home extends BaseController
{
    protected $db;

    public function __construct()
    {
        // 데이터베이스 연결 초기화
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('home', ['title' => 'Vegan Community']);
    }

    public function getPosts()
    {
        $category_id = $this->request->getGet('category_id') ?? 0;
        $page = intval($this->request->getGet('page') ?? 1);
        $perPage = 7;
        $offset = ($page - 1) * $perPage;

        $builder = $this->db->table('posts_vegan');
        $builder->select('posts_vegan.*, categories_vegan.name as category_name');
        $builder->join('categories_vegan', 'categories_vegan.category_id = posts_vegan.category_id', 'left');
        $builder->orderBy('posts_vegan.created_at', 'DESC');
        $builder->limit($perPage, $offset);

        if ($category_id != 0) {
            $builder->where('posts_vegan.category_id', $category_id);
        }

        $posts = $builder->get()->getResultArray();

        $imageModel = new ImageVeganModel();
        foreach ($posts as &$post) {
            $post['images'] = $imageModel->where('post_id', $post['post_id'])->findAll();
        }

        return $this->response->setJSON($posts);
    }

    public function getPostDetail($id)
    {
        $postModel = new PostModel();
        $imageModel = new ImageVeganModel();

        $post = $postModel->find($id);

        if (!$post) {
            return $this->response->setJSON(['error' => 'Post not found']);
        }

        // 카테고리명 가져오기
        $builder = $this->db->table('categories_vegan');
        $builder->where('category_id', $post['category_id']);
        $category = $builder->get()->getRow();

        // 이미지 가져오기
        $images = $imageModel->where('post_id', $id)->findAll();

        // 결과 구성
        $post['category_name'] = $category->name ?? 'Unknown';
        $post['images'] = $images;

        return $this->response->setJSON($post);
    }

    public function createPost()
    {
        if (!session()->has('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please log in first.']);
        }

        $postModel = new PostModel();
        $imageModel = new ImageVeganModel();

        $content = $this->request->getPost('content');
        $category_id = $this->request->getPost('category_id');

        $postModel->insert([
            'user_id' => session()->get('user_id'),
            'category_id' => $category_id,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $post_id = $postModel->insertID();

        $images = $this->request->getFileMultiple('images');
        $uploadPath = FCPATH . 'uploads/' . date('Y-m-d');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if ($images) {
            foreach ($images as $image) {
                if ($image->isValid() && !$image->hasMoved()) {
                    $imageName = $image->getRandomName();
                    $image->move($uploadPath, $imageName);
                    $imageModel->insert([
                        'post_id' => $post_id,
                        'file_path' => '/uploads/' . date('Y-m-d') . '/' . $imageName,
                    ]);
                }
            }
        }

        return $this->response->setJSON(['success' => true]);
    }
}
