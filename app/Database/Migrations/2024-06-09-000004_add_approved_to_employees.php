<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovedToEmployees extends Migration
{
    public function up()
    {
        $this->forge->addColumn('employees', [
            'approved' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'dob',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('employees', 'approved');
    }
} 