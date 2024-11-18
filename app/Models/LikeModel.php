<?php
// app/Models/LikeModel.php
namespace App\Models;

use CodeIgniter\Model;

class LikeModel extends Model
{
    protected $table = 'likes_vegan';
    protected $primaryKey = 'like_id';
    protected $allowedFields = ['post_id', 'user_id'];
}
