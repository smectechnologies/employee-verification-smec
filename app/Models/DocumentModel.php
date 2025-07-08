<?php
namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'employee_id', 'doc_type', 'file_name', 'original_name', 'uploaded_at'
    ];
    protected $useTimestamps = false;
} 