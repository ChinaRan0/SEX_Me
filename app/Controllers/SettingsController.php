<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Services\AuthService;
use App\Services\DatabaseService;

/**
 * Settings Controller
 */
class SettingsController extends Controller
{
    /**
     * Change admin password
     */
    public function changePassword(): void
    {
        $admin = AuthService::requireAuth();
        $input = $this->getInput();

        // Validation
        if (empty($input['current_password']) || empty($input['new_password'])) {
            Response::validationError(['message' => 'Current and new password are required']);
        }

        // Get current admin with password hash
        $adminData = DatabaseService::fetchOne(
            "SELECT * FROM admins WHERE username = ?",
            [$admin['username']]
        );

        if (!$adminData) {
            Response::error('Admin not found', 404);
        }

        // Verify current password
        if (!password_verify($input['current_password'], $adminData['password_hash'])) {
            Response::error('Current password is incorrect', 403);
        }

        // Update password
        $newHash = password_hash($input['new_password'], PASSWORD_DEFAULT);
        DatabaseService::execute(
            "UPDATE admins SET password_hash = ? WHERE id = ?",
            [$newHash, $adminData['id']]
        );

        Response::success(null, 'Password updated successfully');
    }
}
