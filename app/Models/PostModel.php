<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts_vegan';
    protected $primaryKey = 'post_id';
    protected $allowedFields = ['user_id', 'category_id', 'content', 'images', 'created_at'];
}
