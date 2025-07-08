<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table = 'admin_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password_hash', 'created_at'];
    protected $useTimestamps = false;
} 