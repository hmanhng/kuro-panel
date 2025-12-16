<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TokenPurge extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'token:purge';
    protected $description = 'Purges expired refresh tokens from the database.';

    public function run(array $params)
    {
        CLI::write('Starting Token Purge...', 'yellow');
        CLI::write('Time: ' . date('Y-m-d H:i:s'));

        $db = \Config\Database::connect();

        // 1. Count expired tokens
        $builder = $db->table('refresh_tokens');
        $count = $builder->where('expires_at <', date('Y-m-d H:i:s'))->countAllResults();

        CLI::write("Expired tokens found: " . $count);

        if ($count > 0) {
            // 2. Delete expired tokens
            $builder = $db->table('refresh_tokens');
            $builder->where('expires_at <', date('Y-m-d H:i:s'));
            $builder->delete();

            $affected = $db->affectedRows();
            CLI::write("Successfully deleted $affected tokens.", 'green');
        } else {
            CLI::write("No tokens to purge.", 'green');
        }

        CLI::write('Done.', 'yellow');
    }
}
