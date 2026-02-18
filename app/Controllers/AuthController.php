<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Helpers\Response;

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
}
