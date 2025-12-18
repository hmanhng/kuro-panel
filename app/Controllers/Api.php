<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\HistoryModel;
use App\Models\KeysModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class Api extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $keysModel;
    protected $helpers = ['nata_helper', 'text'];

    public function __construct()
    {
        helper(['nata', 'text']);
        $this->userModel = new UserModel();
        $this->keysModel = new KeysModel();
    }

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        // Disable CSRF protection for API
        if (method_exists($this, 'getValidationRules')) {
            $this->validation->setRules([]);
        }
    }

    /**
     * Login API
     * POST /api/login
     */
    public function login()
    {
        $json = $this->request->getJSON();
        $username = $json->username ?? $this->request->getPost('username');
        $password = $json->password ?? $this->request->getPost('password');

        if (!$username || !$password) {
            return $this->fail('Username and password are required', 400);
        }

        try {
            $user = $this->userModel->getUser($username, 'username');

            if (!$user) {
                return $this->fail('User not found', 404);
            }

            $hashPassword = create_password($password, false);
            if (!password_verify($hashPassword, $user->password)) {
                return $this->fail('Invalid password', 401);
            }

            // Check expiration
            $time = new \CodeIgniter\I18n\Time;
            if ($user->expiration_date && $time::now()->isAfter($time::parse($user->expiration_date))) {
                return $this->fail('Account expired', 403);
            }

            if ($user->status != 1) {
                return $this->fail('Account is not active', 403);
            }

            // Generate Access Token
            $key = getenv('JWT_SECRET');
            $iat = time();
            $access_ttl = getenv('JWT_TIME_TO_LIVE');
            $refresh_ttl = getenv('JWT_REFRESH_TIME_TO_LIVE');

            $payload_access = array(
                "iss" => base_url(),
                "aud" => base_url(),
                "iat" => $iat,
                "nbf" => $iat,
                "exp" => $iat + $access_ttl,
                "data" => array(
                    'id' => $user->id_users,
                    'username' => $user->username,
                    'email' => $user->email,
                    'level' => $user->level
                )
            );

            $access_token = JWT::encode($payload_access, $key, 'HS256');

            // Generate Refresh Token
            $payload_refresh = array(
                "iss" => base_url(),
                "aud" => base_url(),
                "iat" => $iat,
                "nbf" => $iat,
                "exp" => $iat + $refresh_ttl,
                "data" => array(
                    'id' => $user->id_users
                )
            );
            $refresh_token = JWT::encode($payload_refresh, $key, 'HS256');

            // Store Refresh Token in Database
            $refreshTokenModel = new \App\Models\RefreshTokenModel();
            $refreshTokenModel->storeToken(
                $refresh_token,
                $user->id_users,
                $refresh_ttl,
                $this->request->getUserAgent()->getAgentString(),
                $this->request->getIPAddress()
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user->id_users,
                    'username' => $user->username,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'level' => $user->level,
                    'saldo' => $user->saldo,
                    'expiration_date' => $user->expiration_date,
                    'token' => $access_token,
                    'refresh_token' => $refresh_token
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->fail('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh Token API
     * POST /api/refresh
     */
    public function refresh()
    {
        $json = $this->request->getJSON();
        $refreshToken = $json->refresh_token ?? $this->request->getPost('refresh_token');

        if (!$refreshToken) {
            return $this->fail('Refresh token required', 400);
        }

        try {
            // 1. Verify Token Signature
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($refreshToken, new Key($key, 'HS256'));

            // 2. Verify Token in Database (Stateful Check)
            $refreshTokenModel = new \App\Models\RefreshTokenModel();
            $storedToken = $refreshTokenModel->findByToken($refreshToken);

            if (!$storedToken) {
                return $this->fail('Invalid or revoked refresh token', 401);
            }

            // Check if expired in DB (double check)
            if (strtotime($storedToken['expires_at']) < time()) {
                $refreshTokenModel->revokeToken($refreshToken); // Clean up
                return $this->fail('Refresh token expired', 401);
            }

            // Get user ID from refresh token
            $userId = $decoded->data->id;

            // Check if user still exists
            $user = $this->userModel->asObject()->find($userId);
            if (!$user) {
                return $this->fail('User not found', 404);
            }

            // 3. Generate NEW Access Token
            $iat = time();
            $key = getenv('JWT_SECRET');
            $access_ttl = getenv('JWT_TIME_TO_LIVE');
            $refresh_ttl = getenv('JWT_REFRESH_TIME_TO_LIVE');

            $payload_access = array(
                "iss" => base_url(),
                "aud" => base_url(),
                "iat" => $iat,
                "nbf" => $iat,
                "exp" => $iat + $access_ttl,
                "data" => array(
                    'id' => $user->id_users,
                    'username' => $user->username,
                    'email' => $user->email,
                    'level' => $user->level
                )
            );

            $new_access_token = JWT::encode($payload_access, $key, 'HS256');

            // 4. Generate NEW Refresh Token (Token Rotation)
            $payload_refresh = array(
                "iss" => base_url(),
                "aud" => base_url(),
                "iat" => $iat,
                "nbf" => $iat,
                "exp" => $iat + $refresh_ttl,
                "data" => array(
                    'id' => $user->id_users
                )
            );
            $new_refresh_token = JWT::encode($payload_refresh, $key, 'HS256');

            // 5. Rotate via Database (Update Existing to preserve created_at)
            $refreshTokenModel->updateToken(
                (int) $storedToken['id'],
                $new_refresh_token,
                $refresh_ttl,
                $this->request->getUserAgent()->getAgentString(),
                $this->request->getIPAddress()
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'Token refreshed',
                'data' => [
                    'token' => $new_access_token,
                    'refresh_token' => $new_refresh_token
                ]
            ]);

        } catch (ExpiredException $e) {
            return $this->fail('Refresh token expired', 401);
        } catch (\Throwable $e) {
            return $this->fail('Invalid refresh token', 401);
        }
    }

    public function logout()
    {
        $json = $this->request->getJSON();
        $refreshToken = $json->refresh_token ?? $this->request->getPost('refresh_token');

        if ($refreshToken) {
            $refreshTokenModel = new \App\Models\RefreshTokenModel();
            $refreshTokenModel->revokeToken($refreshToken);
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Validate Bearer Token
     */
    private function validateToken()
    {
        $header = null;

        // 1. Try Apache Headers (Case Insensitive)
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $headers = array_change_key_case($requestHeaders, CASE_LOWER);
            if (isset($headers['authorization'])) {
                $header = $headers['authorization'];
            }
        }

        // 2. Try $_SERVER (various variations)
        if (!$header) {
            $header = $this->request->getServer('HTTP_AUTHORIZATION')
                ?? $_SERVER['HTTP_AUTHORIZATION']
                ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
                ?? $_SERVER['HTTP_X_AUTHORIZATION']
                ?? null;
        }

        if (!$header)
            return null;

        // Extract token
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        } else {
            $token = $header;
        }

        if (!$token) {
            return null;
        }

        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Access user data from token
            $userId = $decoded->data->id;

            // Verify user still exists/active in DB (optional but recommended)
            $user = $this->userModel->asObject()->find($userId);

            return $user ?: null;

        } catch (\Throwable $e) {
            // Token invalid or expired
            return null;
        }
    }

    /**
     * Register API
     * POST /api/register
     */
    public function register()
    {
        $json = $this->request->getJSON();
        $email = $json->email ?? $this->request->getPost('email');
        $username = $json->username ?? $this->request->getPost('username');
        $fullname = $json->fullname ?? $this->request->getPost('fullname');
        $password = $json->password ?? $this->request->getPost('password');
        $referral = $json->referral ?? $this->request->getPost('referral');

        // Validation
        if (!$email || !$username || !$fullname || !$password || !$referral) {
            return $this->fail('All fields are required', 400);
        }

        // Check username exists
        $existingUser = $this->userModel->getUser($username, 'username');
        if ($existingUser) {
            return $this->fail('Username already taken', 400);
        }

        // Check referral code
        $mCode = new CodeModel();
        $rCheck = $mCode->checkCode($referral);

        if (!$rCheck) {
            return $this->fail('Invalid referral code', 400);
        }

        if ($rCheck->used_by) {
            return $this->fail('Referral code already used', 400);
        }

        $hashPassword = create_password($password);
        $ipaddress = $_SERVER['REMOTE_ADDR'];

        $data_register = [
            'email' => $email,
            'username' => $username,
            'fullname' => $fullname,
            'level' => $rCheck->level ?? 3,
            'password' => $hashPassword,
            'saldo' => $rCheck->set_saldo ?: 0,
            'uplink' => $rCheck->created_by,
            'user_ip' => $ipaddress,
            'expiration_date' => $rCheck->acc_expiration ?? null
        ];

        $ids = $this->userModel->insert($data_register, true);

        if ($ids) {
            $mCode->useReferral($referral);
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => ['user_id' => $ids]
            ]);
        }

        return $this->fail('Registration failed', 500);
    }

    /**
     * Get all keys
     * GET /api/keys
     */
    public function getKeys()
    {
        $user = $this->validateToken();

        if (!$user) {
            return $this->fail('Unauthorized', 401);
        }

        if ($user->level == 1) {
            // Admin can see all keys
            $keys = $this->keysModel->findAll();
        } else {
            $keys = $this->keysModel->where('registrator', $user->username)->findAll();
        }

        $formattedKeys = [];
        $time = new \CodeIgniter\I18n\Time;

        foreach ($keys as $key) {
            $devices = (!empty($key['devices']) && $key['devices'] !== null)
                ? explode(',', $key['devices'])
                : [];
            $formattedKeys[] = [
                'id' => (string) $key['id_keys'],
                'game' => $key['game'],
                'user_key' => $key['user_key'],
                'duration' => (string) $key['duration'],
                'duration_text' => $this->getDurationText($key['duration']),
                'expired_date' => $key['expired_date'],
                'max_devices' => (string) $key['max_devices'],
                'devices_count' => count($devices),
                'devices' => $key['devices'] ?? null,
                'status' => (string) $key['status'],
                'registrator' => $key['registrator'] ?? null,
                'created_at' => $key['created_at'] ?? null,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data' => $formattedKeys,
            'total' => count($formattedKeys)
        ]);
    }

    /**
     * Get single key
     * GET /api/keys/{id}
     */
    public function getKey($id = null)
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->fail('Unauthorized', 401);
        }

        if (!$id) {
            return $this->fail('Key ID required', 400);
        }

        $key = $this->keysModel->getKeys($id, 'id_keys');

        if (!$key) {
            return $this->fail('Key not found', 404);
        }

        // Check permission
        if ($user->level != 1 && $key->registrator != $user->username) {
            return $this->fail('Not authorized to view this key', 403);
        }

        $devices = $key->devices ? explode(',', $key->devices) : [];

        return $this->respond([
            'status' => 'success',
            'data' => [
                'id' => (string) $key->id_keys,
                'game' => $key->game,
                'user_key' => $key->user_key,
                'duration' => (string) $key->duration,
                'duration_text' => $this->getDurationText($key->duration),
                'expired_date' => $key->expired_date,
                'max_devices' => (string) $key->max_devices,
                'devices_count' => count($devices),
                'devices' => $key->devices,
                'status' => (string) $key->status,
                'registrator' => $key->registrator,
            ]
        ]);
    }

    /**
     * Create new key
     * POST /api/keys
     */
    public function createKey()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->fail('Unauthorized', 401);
        }

        $json = $this->request->getJSON();
        $game = $json->game ?? $this->request->getPost('game') ?? 'PUBG';
        $duration = $json->duration ?? $this->request->getPost('duration') ?? 24;
        $maxDevices = $json->max_devices ?? $this->request->getPost('max_devices') ?? 1;
        $customKey = $json->custom_key ?? $this->request->getPost('custom_key');
        $quantity = $json->quantity ?? $this->request->getPost('quantity') ?? 1;

        $userId = $user->id_users;

        // Calculate price
        $price = $this->getPrice($duration, $quantity, $maxDevices);

        if ($user->saldo < $price) {
            return $this->fail('Insufficient balance. Required: $' . $price . ', Available: $' . $user->saldo, 400);
        }

        $generatedKeys = [];

        for ($i = 0; $i < $quantity; $i++) {
            if ($customKey && $quantity == 1) {
                // Check if custom key exists
                $existingKey = $this->keysModel->getKeys($customKey, 'user_key');
                if ($existingKey) {
                    return $this->fail('Custom key already exists', 400);
                }
                $license = $customKey;
                $license = $customKey;
            } else {
                $license = $user->username . '-' . random_string('alnum', 12);
            }

            $keyData = [
                'game' => $game,
                'user_key' => $license,
                'duration' => $duration,
                'max_devices' => $maxDevices,
                'status' => 1,
                'registrator' => $user->username,
                'devices' => null,
                'expired_date' => null,
            ];

            try {
                $keyId = $this->keysModel->insert($keyData);

                if ($keyId) {
                    $generatedKeys[] = [
                        'id' => $keyId,
                        'user_key' => $license,
                        'duration' => $duration,
                        'max_devices' => $maxDevices,
                    ];

                    // Log history
                    $history = new HistoryModel();
                    $history->insert([
                        'keys_id' => $keyId,
                        'user_do' => $user->username,
                        'info' => "$game|" . substr($license, 0, 5) . "|$duration|$maxDevices"
                    ]);
                }
            } catch (\Exception $e) {
                return $this->fail('Failed to generate key: ' . $e->getMessage(), 500);
            }
        }

        if (empty($generatedKeys)) {
            return $this->fail('Failed to generate any keys', 500);
        }

        // Deduct balance
        $newBalance = $user->saldo - $price;
        $this->userModel->update($userId, ['saldo' => $newBalance]);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Key(s) generated successfully',
            'data' => [
                'keys' => $generatedKeys,
                'quantity' => count($generatedKeys),
                'price_charged' => $price,
                'new_balance' => $newBalance,
            ]
        ]);
    }

    /**
     * Update key
     * PUT /api/keys/{id}
     */
    public function updateKey($id = null)
    {
        if (!$id) {
            return $this->fail('Key ID required', 400);
        }

        $key = $this->keysModel->getKeys($id, 'id_keys');
        if (!$key) {
            return $this->fail('Key not found', 404);
        }

        $currentUser = $this->validateToken();
        if (!$currentUser) {
            return $this->fail('Unauthorized', 401);
        }

        // Check permission
        if ($currentUser->level != 1 && $key->registrator != $currentUser->username) {
            return $this->fail('Not authorized to edit this key', 403);
        }

        $updateData = [];
        $json = $this->request->getJSON();

        if (isset($json->game))
            $updateData['game'] = $json->game;
        if (isset($json->user_key)) {
            // Check if new key already exists
            if ($json->user_key != $key->user_key) {
                $existingKey = $this->keysModel->getKeys($json->user_key, 'user_key');
                if ($existingKey) {
                    return $this->fail('Key already exists', 400);
                }
            }
            $updateData['user_key'] = $json->user_key;
        }
        if (isset($json->duration))
            $updateData['duration'] = $json->duration;
        if (isset($json->max_devices))
            $updateData['max_devices'] = $json->max_devices;
        if (isset($json->status))
            $updateData['status'] = $json->status;
        if (isset($json->expired_date))
            $updateData['expired_date'] = $json->expired_date ?: null;
        if (isset($json->devices))
            $updateData['devices'] = $json->devices ?: null;

        if (empty($updateData)) {
            return $this->fail('No data to update', 400);
        }

        $this->keysModel->update($id, $updateData);

        return $this->respond([
            'status' => 'success',
            'message' => 'Key updated successfully'
        ]);
    }

    /**
     * Delete key
     * DELETE /api/keys/{id}
     */
    public function deleteKey($id = null)
    {
        if (!$id) {
            return $this->fail('Key ID required', 400);
        }

        $key = $this->keysModel->getKeys($id, 'id_keys');
        if (!$key) {
            return $this->fail('Key not found', 404);
        }

        $currentUser = $this->validateToken();

        if (!$currentUser) {
            return $this->fail('Unauthorized', 401);
        }

        // Check permission
        if ($currentUser->level != 1 && $key->registrator != $currentUser->username) {
            return $this->fail('Not authorized to delete this key', 403);
        }

        $this->keysModel->delete($id);

        return $this->respond([
            'status' => 'success',
            'message' => 'Key deleted successfully'
        ]);
    }

    /**
     * Delete expired keys
     * DELETE /api/keys/expired
     */
    public function deleteExpiredKeys()
    {
        $user = $this->validateToken();
        if (!$user) {
            return $this->fail('Unauthorized', 401);
        }

        $time = new \CodeIgniter\I18n\Time;
        $now = $time::now()->toDateTimeString();

        // Admin might want to delete ALL expired keys? 
        // Usually "my expired keys". Let's assume user's own keys.
        // Even admin probably manages their own resale keys here, but if admin wants to clean system...
        // The user request implies "xoa cac key da het han" in THEIR app view.

        $builder = $this->keysModel->builder();

        if ($user->level != 1) {
            $builder->where('registrator', $user->username);
        }

        // Logic: expired_date is not null AND expired_date < NOW
        $builder->where('expired_date IS NOT NULL');
        $builder->where('expired_date <', $now);

        $builder->delete();

        // Affected rows? CodeIgniter 4 delete doesn't always return count easily if using builder directly like this without getting DB result, 
        // but let's assume success.

        return $this->respond([
            'status' => 'success',
            'message' => 'Expired keys deleted successfully'
        ]);
    }

    /**
     * Reset key (clear devices)
     * POST /api/keys/{id}/reset
     */
    public function resetKey($id = null)
    {
        if (!$id) {
            return $this->fail('Key ID required', 400);
        }

        $key = $this->keysModel->getKeys($id, 'id_keys');
        if (!$key) {
            return $this->fail('Key not found', 404);
        }

        $currentUser = $this->validateToken();

        if (!$currentUser) {
            return $this->fail('Unauthorized', 401);
        }

        // Check permission
        if ($currentUser->level != 1 && $key->registrator != $currentUser->username) {
            return $this->fail('Not authorized to reset this key', 403);
        }

        $this->keysModel->update($id, [
            'devices' => null,
            'expired_date' => null
        ]);

        return $this->respond([
            'status' => 'success',
            'message' => 'Key reset successfully'
        ]);
    }

    /**
     * Get user profile
     * GET /api/profile
     */
    public function getProfile()
    {
        $user = $this->validateToken();

        if (!$user) {
            return $this->fail('Unauthorized', 401);
        }

        // Count keys
        $totalKeys = $this->keysModel->where('registrator', $user->username)->countAllResults();

        return $this->respond([
            'status' => 'success',
            'data' => [
                'user_id' => $user->id_users,
                'username' => $user->username,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'level' => $user->level,
                'level_text' => $this->getLevelText($user->level),
                'saldo' => $user->saldo,
                'expiration_date' => $user->expiration_date,
                'total_keys' => (int) $totalKeys,
            ]
        ]);
    }

    /**
     * Helper: Get duration text
     */
    private function getDurationText($hours)
    {
        if ($hours < 24) {
            return $hours . ' Hour' . ($hours > 1 ? 's' : '');
        }
        $days = $hours / 24;
        return $days . ' Day' . ($days > 1 ? 's' : '');
    }

    /**
     * Helper: Get level text
     */
    private function getLevelText($level)
    {
        $levels = [
            1 => 'Owner',
            2 => 'Admin',
            3 => 'Reseller'
        ];
        return $levels[$level] ?? 'User';
    }

    /**
     * Helper: Calculate price
     */
    private function getPrice($duration, $quantity, $maxDevices)
    {
        $prices = [
            1 => 0.5,
            24 => 1,
            168 => 7,
            720 => 20,
        ];

        $basePrice = $prices[$duration] ?? ($duration * 0.1); // Fallback calculation needs to be adjusted too if needed, but primarily relying on map
        return $basePrice * $quantity * $maxDevices;
    }

    /**
     * Get available durations and prices
     * GET /api/durations
     */
    public function getDurations()
    {
        $durations = [
            ['hours' => 1, 'label' => '1 Hour', 'price' => 0.5],
            ['hours' => 24, 'label' => '1 Day', 'price' => 1],
            ['hours' => 168, 'label' => '7 Days', 'price' => 7],
            ['hours' => 720, 'label' => '30 Days', 'price' => 20],
        ];

        return $this->respond([
            'status' => 'success',
            'data' => $durations
        ]);
    }

    /**
     * Get available games
     * GET /api/games
     */
    public function getGames()
    {
        $games = [
            ['code' => 'PUBG', 'name' => 'All Games'],
        ];

        return $this->respond([
            'status' => 'success',
            'data' => $games
        ]);
    }
}
