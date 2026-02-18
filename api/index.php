<?php
/**
 * API Entry Point
 *
 * Routes all API requests to appropriate controllers
 */

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Initialize database
App\Services\DatabaseService::initSchema();

// Create default admin if needed
App\Services\AuthService::createDefaultAdmin();

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/api';

// Remove base path and leading slash
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$uri = rtrim($uri, '/');

// Route definitions
$routes = [
    // Public routes
    ['GET', '/dice', 'DiceController@index'],
    ['GET', '/poses', 'PoseController@index'],
    ['GET', '/tasks', 'TaskController@index'],
    ['GET', '/tasks/random', 'TaskController@random'],

    // Preset public route
    ['GET', '/preset/([a-zA-Z0-9]+)', 'PresetController@getByCode'],

    // Auth routes
    ['POST', '/auth/login', 'AuthController@login'],
    ['POST', '/auth/logout', 'AuthController@logout'],
    ['GET', '/auth/me', 'AuthController@me'],

    // Admin - Dice Actions
    ['GET', '/admin/dice/actions', 'DiceController@getActions'],
    ['GET', '/admin/dice/actions/([0-9]+)', 'DiceController@showAction'],
    ['POST', '/admin/dice/actions', 'DiceController@createAction'],
    ['PUT', '/admin/dice/actions/([0-9]+)', 'DiceController@updateAction'],
    ['DELETE', '/admin/dice/actions/([0-9]+)', 'DiceController@deleteAction'],

    // Admin - Dice Parts
    ['GET', '/admin/dice/parts', 'DiceController@getParts'],
    ['GET', '/admin/dice/parts/([0-9]+)', 'DiceController@showPart'],
    ['POST', '/admin/dice/parts', 'DiceController@createPart'],
    ['PUT', '/admin/dice/parts/([0-9]+)', 'DiceController@updatePart'],
    ['DELETE', '/admin/dice/parts/([0-9]+)', 'DiceController@deletePart'],

    // Admin - Poses
    ['GET', '/admin/poses', 'PoseController@getPoses'],
    ['GET', '/admin/poses/([0-9]+)', 'PoseController@showPose'],
    ['POST', '/admin/poses', 'PoseController@createPose'],
    ['PUT', '/admin/poses/([0-9]+)', 'PoseController@updatePose'],
    ['DELETE', '/admin/poses/([0-9]+)', 'PoseController@deletePose'],

    // Admin - Pose Places
    ['GET', '/admin/poses/places', 'PoseController@getPlaces'],
    ['GET', '/admin/poses/places/([0-9]+)', 'PoseController@showPlace'],
    ['POST', '/admin/poses/places', 'PoseController@createPlace'],
    ['PUT', '/admin/poses/places/([0-9]+)', 'PoseController@updatePlace'],
    ['DELETE', '/admin/poses/places/([0-9]+)', 'PoseController@deletePlace'],

    // Admin - Pose Times
    ['GET', '/admin/poses/times', 'PoseController@getTimes'],
    ['GET', '/admin/poses/times/([0-9]+)', 'PoseController@showTime'],
    ['POST', '/admin/poses/times', 'PoseController@createTime'],
    ['PUT', '/admin/poses/times/([0-9]+)', 'PoseController@updateTime'],
    ['DELETE', '/admin/poses/times/([0-9]+)', 'PoseController@deleteTime'],

    // Admin - Tasks
    ['GET', '/admin/tasks', 'TaskController@list'],
    ['GET', '/admin/tasks/([0-9]+)', 'TaskController@show'],
    ['POST', '/admin/tasks', 'TaskController@create'],
    ['PUT', '/admin/tasks/([0-9]+)', 'TaskController@update'],
    ['DELETE', '/admin/tasks/([0-9]+)', 'TaskController@delete'],

    // Admin - Presets (统一预设 - 一个链接包含所有游戏类型)
    ['GET', '/admin/presets', 'PresetController@list'],
    ['GET', '/admin/presets/form-data', 'PresetController@getFormData'],
    ['GET', '/admin/presets/([0-9]+)', 'PresetController@show'],
    ['POST', '/admin/presets', 'PresetController@create'],
    ['POST', '/admin/presets/generate', 'PresetController@generateRandom'],
    ['PUT', '/admin/presets/([0-9]+)', 'PresetController@update'],
    ['DELETE', '/admin/presets/([0-9]+)', 'PresetController@delete'],

    // Upload
    ['POST', '/upload', 'UploadController@upload'],
];

// Match route
$matched = false;
$pathParams = [];

foreach ($routes as $route) {
    [$routeMethod, $routePattern, $handler] = $route;

    if ($method !== $routeMethod) {
        continue;
    }

    // Convert route pattern to regex
    $regex = '#^' . $routePattern . '$#';

    if (preg_match($regex, $uri, $matches)) {
        $matched = true;
        array_shift($matches); // Remove full match
        $pathParams = $matches;

        // Parse handler
        [$controllerName, $methodName] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        // Instantiate and call
        try {
            $controller = new $controllerClass();
            call_user_func_array([$controller, $methodName], $pathParams);
        } catch (Exception $e) {
            App\Helpers\Response::serverError($e->getMessage());
        }
        break;
    }
}

if (!$matched) {
    App\Helpers\Response::notFound('API endpoint not found');
}
