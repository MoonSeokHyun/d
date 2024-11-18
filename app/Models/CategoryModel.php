<?php
// app/Models/CategoryModel.php
namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories_vegan';
    protected $primaryKey = 'category_id';
    protected $allowedFields = ['name'];
}
