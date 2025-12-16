<?php

namespace App\Models;

use CodeIgniter\Model;

class RefreshTokenModel extends Model
{
    protected $table = 'refresh_tokens';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'token_hash',
        'user_id',
        'expires_at',
        'user_agent',
        'ip_address'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Finds a token by its hash
     */
    public function findByToken(string $token)
    {
        $hash = hash('sha256', $token);
        return $this->where('token_hash', $hash)->first();
    }

    /**
     * Stores a new refresh token
     */
    public function storeToken(string $token, int $userId, int $ttl, string $userAgent = '', string $ipAddress = '')
    {
        $hash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);

        return $this->insert([
            'token_hash' => $hash,
            'user_id' => $userId,
            'expires_at' => $expiresAt,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    }

    /**
     * Updates an existing refresh token (Rotation maintaining created_at)
     */
    public function updateToken(int $id, string $newToken, int $ttl, string $userAgent = '', string $ipAddress = '')
    {
        $hash = hash('sha256', $newToken);
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);

        return $this->update($id, [
            'token_hash' => $hash,
            'expires_at' => $expiresAt,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    }

    /**
     * Revokes (deletes) a token
     */
    public function revokeToken(string $token)
    {
        $hash = hash('sha256', $token);
        return $this->where('token_hash', $hash)->delete();
    }

    /**
     * Deletes all tokens for a user (Logout from all devices)
     */
    public function revokeAllForUser(int $userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

}
