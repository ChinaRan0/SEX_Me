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
     * Login attempt check
     */
    public static function checkLoginAttempts(string $ip): bool
    {
        $db = DatabaseService::getInstance();

        // Clean old attempts
        $db->prepare("DELETE FROM login_attempts WHERE created_at < datetime('now', '-15 minutes')")
           ->execute();

        // Count recent failed attempts
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM login_attempts
            WHERE ip_address = ? AND success = 0 AND created_at > datetime('now', '-15 minutes')
        ");
        $stmt->execute([$ip]);
        $result = $stmt->fetch();

        return $result['count'] < MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Record login attempt
     */
    public static function recordLoginAttempt(string $ip, string $username, bool $success): void
    {
        DatabaseService::execute(
            "INSERT INTO login_attempts (ip_address, username, success) VALUES (?, ?, ?)",
            [$ip, $username, $success ? 1 : 0]
        );
    }

    /**
     * Login user
     */
    public static function login(string $username, string $password): array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Check login attempts
        if (!self::checkLoginAttempts($ip)) {
            Response::error('Too many login attempts. Please try again later.', 429);
        }

        // Find admin
        $admin = DatabaseService::fetchOne(
            "SELECT * FROM admins WHERE username = ?",
            [$username]
        );

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            self::recordLoginAttempt($ip, $username, false);
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

        // Record successful login
        self::recordLoginAttempt($ip, $username, true);

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
