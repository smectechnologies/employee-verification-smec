<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'employee_id', 'name', 'designation', 'department', 'contact', 'location', 'status', 'start_date', 'end_date', 'dob', 'approved'
    ];
    protected $useTimestamps = false;
} 