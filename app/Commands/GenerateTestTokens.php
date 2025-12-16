<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Firebase\JWT\JWT;

class GenerateTestTokens extends BaseCommand
{
    protected $group = 'Testing';
    protected $name = 'test:tokens';
    protected $description = 'Generate expired Access and Refresh tokens for API testing.';

    public function run(array $params)
    {
        $key = getenv('JWT_SECRET');

        if (!$key) {
            CLI::error('JWT_SECRET not found in .env');
            return;
        }

        // 1. Generate Expired Access Token
        $payload_access_expired = [
            "iss" => base_url(),
            "aud" => base_url(),
            "iat" => time() - 3600, // 1 hour ago
            "nbf" => time() - 3600,
            "exp" => time() - 1800, // Expired 30 mins ago
            "data" => ['id' => 1] // Dummy ID
        ];
        $expired_access = JWT::encode($payload_access_expired, $key, 'HS256');

        // 2. Generate Expired Refresh Token
        $payload_refresh_expired = [
            "iss" => base_url(),
            "aud" => base_url(),
            "iat" => time() - 3600,
            "nbf" => time() - 3600,
            "exp" => time() - 1800,
            "data" => ['id' => 1]
        ];
        $expired_refresh = JWT::encode($payload_refresh_expired, $key, 'HS256');

        // Output as JSON for easy parsing
        CLI::write(json_encode([
            'expired_access_token' => $expired_access,
            'expired_refresh_token' => $expired_refresh
        ]));
    }
}
