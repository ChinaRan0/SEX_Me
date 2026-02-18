<?php
/**
 * Application Configuration
 */

// Database configuration
define('DB_PATH', __DIR__ . '/../database/game.db');

// Security configuration
define('TOKEN_EXPIRY_HOURS', 24);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);

// Session configuration
define('TOKEN_LENGTH', 64);

// Upload configuration
define('UPLOAD_PATH', __DIR__ . '/../storage/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// CORS configuration
define('CORS_ORIGINS', ['*']);

// Error reporting (disable in production)
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Timezone
date_default_timezone_set('Asia/Shanghai');
