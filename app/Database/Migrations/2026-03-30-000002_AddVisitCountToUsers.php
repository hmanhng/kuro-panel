<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitCountToUsers extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('visit_count', 'users')) {
            $this->forge->addColumn('users', [
                'visit_count' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'after' => 'user_ip',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('visit_count', 'users')) {
            $this->forge->dropColumn('users', 'visit_count');
        }
    }
}
