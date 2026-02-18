<?php

namespace App\Services;

use App\Helpers\Response;
use PDO;

/**
 * Authentication Service
 */
class AuthService
{
    /**
     * Login user
     */
    public static function login(string $username, string $password): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Find admin
        $admin = DatabaseService::fetchOne(
            "SELECT * FROM admins WHERE username = ?",
            [$username]
        );

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            Response::error('Invalid username or password', 401);
        }

        // Generate token
        $token = bin2hex(random_bytes(TOKEN_LENGTH / 2));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY_HOURS . ' hours'));

        // Save session
        DatabaseService::execute(
            "INSERT INTO admin_sessions (admin_id, token, expires_at) VALUES (?, ?, ?)",
            [$admin['id'], $token, $expiresAt]
        );

        return [
            'token' => $token,
            'expires_at' => $expiresAt,
            'admin' => [
                'id' => $admin['id'],
                'username' => $admin['username']
            ]
        ];
    }

    /**
     * Logout user
     */
    public static function logout(string $token): bool
    {
        return DatabaseService::execute(
            "DELETE FROM admin_sessions WHERE token = ?",
            [$token]
        );
    }

    /**
     * Validate token and get admin
     */
    public static function validateToken(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        // Clean expired sessions
        DatabaseService::execute(
            "DELETE FROM admin_sessions WHERE expires_at < datetime('now')"
        );

        $session = DatabaseService::fetchOne("
            SELECT s.*, a.username
            FROM admin_sessions s
            JOIN admins a ON s.admin_id = a.id
            WHERE s.token = ? AND s.expires_at > datetime('now')
        ", [$token]);

        if (!$session) {
            return null;
        }

        return [
            'id' => $session['admin_id'],
            'username' => $session['username']
        ];
    }

    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if ($authHeader && preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if user is authenticated
     */
    public static function requireAuth(): array
    {
        $token = self::getTokenFromHeader();
        $admin = self::validateToken($token);

        if (!$admin) {
            Response::unauthorized('Invalid or expired token');
        }

        return $admin;
    }

    /**
     * Create default admin if not exists
     */
    public static function createDefaultAdmin(): void
    {
        $admin = DatabaseService::fetchOne("SELECT id FROM admins LIMIT 1");

        if (!$admin) {
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            DatabaseService::execute(
                "INSERT INTO admins (username, password_hash) VALUES (?, ?)",
                ['admin', $passwordHash]
            );
        }
    }
}
