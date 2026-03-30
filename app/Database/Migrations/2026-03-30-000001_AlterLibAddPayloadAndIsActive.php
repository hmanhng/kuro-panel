<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterLibAddPayloadAndIsActive extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('payload', 'lib')) {
            $this->forge->addColumn('lib', [
                'payload' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'file_size',
                ],
            ]);
        }

        if (!$this->db->fieldExists('is_active', 'lib')) {
            $this->forge->addColumn('lib', [
                'is_active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'payload',
                ],
            ]);
        }

        $activeExists = $this->db->table('lib')->where('is_active', 1)->countAllResults();
        if ((int) $activeExists === 0) {
            $latest = $this->db->table('lib')->select('id')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
            if (!empty($latest['id'])) {
                $this->db->table('lib')->where('id', (int) $latest['id'])->update(['is_active' => 1]);
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_active', 'lib')) {
            $this->forge->dropColumn('lib', 'is_active');
        }

        if ($this->db->fieldExists('payload', 'lib')) {
            $this->forge->dropColumn('lib', 'payload');
        }
    }
}
