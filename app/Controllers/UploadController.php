<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Upload Controller
 */
class UploadController extends Controller
{
    /**
     * Upload image
     */
    public function upload(): void
    {
        AuthService::requireAuth();

        if (!isset($_FILES['file'])) {
            Response::error('No file uploaded', 400);
        }

        $file = $_FILES['file'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Response::error('Upload error: ' . $file['error'], 400);
        }

        // Check file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            Response::error('File too large. Max size is ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB', 400);
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
            Response::error('Invalid file type. Allowed: ' . implode(', ', ALLOWED_IMAGE_TYPES), 400);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $relativePath = 'images/' . $filename;
        $uploadPath = dirname(__DIR__, 3) . '/' . $relativePath;

        // Ensure directory exists
        $dir = dirname($uploadPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            Response::error('Failed to save file', 500);
        }

        Response::success([
            'path' => $relativePath,
            'url' => $relativePath
        ]);
    }
}
