<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Helpers\Response;
use PDO;

/**
 * Auth Controller
 */
class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(): void
    {
        $input = $this->getInput();

        if (empty($input['username']) || empty($input['password'])) {
            Response::validationError([
                'username' => empty($input['username']) ? 'Username is required' : null,
                'password' => empty($input['password']) ? 'Password is required' : null
            ]);
        }

        $result = AuthService::login($input['username'], $input['password']);
        Response::success($result, 'Login successful');
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $token = AuthService::getTokenFromHeader();

        if ($token) {
            AuthService::logout($token);
        }

        Response::success(null, 'Logout successful');
    }

    /**
     * Get current user info
     */
    public function me(): void
    {
        $admin = AuthService::requireAuth();
        Response::success($admin);
    }

    /**
     * Change password
     */
    public function changePassword(): void
    {
        $admin = AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['old_password']) || empty($input['new_password'])) {
            Response::validationError([
                'old_password' => empty($input['old_password']) ? 'Current password is required' : null,
                'new_password' => empty($input['new_password']) ? 'New password is required' : null
            ]);
        }

        // Get admin data from database to verify current password
        $db = \App\Services\DatabaseService::getInstance();
        $stmt = $db->prepare('SELECT password_hash FROM admins WHERE id = :id');
        $stmt->execute(['id' => $admin['id']]);
        $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$adminData || !password_verify($input['old_password'], $adminData['password_hash'])) {
            Response::error('当前密码错误', 400);
        }

        // Hash new password and update
        $newPasswordHash = password_hash($input['new_password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE admins SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'password_hash' => $newPasswordHash,
            'id' => $admin['id']
        ]);

        Response::success(null, '密码修改成功');
    }
}
