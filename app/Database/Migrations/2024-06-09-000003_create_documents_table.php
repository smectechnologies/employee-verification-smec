<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'doc_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'original_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employee_id', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documents');
    }

    public function down()
    {
        $this->forge->dropTable('documents');
    }
} 