<?php

namespace App\Models;

use CodeIgniter\Model;

class ImageVeganModel extends Model
{
    protected $table = 'image_vegan';
    protected $primaryKey = 'image_id';
    protected $allowedFields = ['post_id', 'file_path'];
}
