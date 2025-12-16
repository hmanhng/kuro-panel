<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersSaldo extends Migration
{
    public function up()
    {
        // Change 'saldo' column from INT (or whatever it is) to DECIMAL(15, 2)
        // DECIMAL(15, 2) allows for amounts like 9999999999999.99
        $this->forge->modifyColumn('users', [
            'saldo' => [
                'name' => 'saldo',
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
        ]);
    }

    public function down()
    {
        // Revert back to INT if needed (though usually we don't want to lose precision)
        $this->forge->modifyColumn('users', [
            'saldo' => [
                'name' => 'saldo',
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);
    }
}
