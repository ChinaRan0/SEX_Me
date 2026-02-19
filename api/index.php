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
    ['GET', '/stories', 'StoryController@index'],
    ['GET', '/stories/random', 'StoryController@random'],

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

    // Admin - Stories (随机剧情)
    ['GET', '/admin/stories', 'StoryController@list'],

    // Admin - Story Male Roles
    ['GET', '/admin/stories/male-roles', 'StoryController@getMaleRoles'],
    ['GET', '/admin/stories/male-roles/([0-9]+)', 'StoryController@showMaleRole'],
    ['POST', '/admin/stories/male-roles', 'StoryController@createMaleRole'],
    ['PUT', '/admin/stories/male-roles/([0-9]+)', 'StoryController@updateMaleRole'],
    ['DELETE', '/admin/stories/male-roles/([0-9]+)', 'StoryController@deleteMaleRole'],

    // Admin - Story Female Roles
    ['GET', '/admin/stories/female-roles', 'StoryController@getFemaleRoles'],
    ['GET', '/admin/stories/female-roles/([0-9]+)', 'StoryController@showFemaleRole'],
    ['POST', '/admin/stories/female-roles', 'StoryController@createFemaleRole'],
    ['PUT', '/admin/stories/female-roles/([0-9]+)', 'StoryController@updateFemaleRole'],
    ['DELETE', '/admin/stories/female-roles/([0-9]+)', 'StoryController@deleteFemaleRole'],

    // Admin - Story Relationships
    ['GET', '/admin/stories/relationships', 'StoryController@getRelationships'],
    ['GET', '/admin/stories/relationships/([0-9]+)', 'StoryController@showRelationship'],
    ['POST', '/admin/stories/relationships', 'StoryController@createRelationship'],
    ['PUT', '/admin/stories/relationships/([0-9]+)', 'StoryController@updateRelationship'],
    ['DELETE', '/admin/stories/relationships/([0-9]+)', 'StoryController@deleteRelationship'],

    // Admin - Story Initiatives
    ['GET', '/admin/stories/initiatives', 'StoryController@getInitiatives'],
    ['GET', '/admin/stories/initiatives/([0-9]+)', 'StoryController@showInitiative'],
    ['POST', '/admin/stories/initiatives', 'StoryController@createInitiative'],
    ['PUT', '/admin/stories/initiatives/([0-9]+)', 'StoryController@updateInitiative'],
    ['DELETE', '/admin/stories/initiatives/([0-9]+)', 'StoryController@deleteInitiative'],

    // Admin - Story Behaviors
    ['GET', '/admin/stories/behaviors', 'StoryController@getBehaviors'],
    ['GET', '/admin/stories/behaviors/([0-9]+)', 'StoryController@showBehavior'],
    ['POST', '/admin/stories/behaviors', 'StoryController@createBehavior'],
    ['PUT', '/admin/stories/behaviors/([0-9]+)', 'StoryController@updateBehavior'],
    ['DELETE', '/admin/stories/behaviors/([0-9]+)', 'StoryController@deleteBehavior'],

    // Admin - Story Actions
    ['GET', '/admin/stories/actions', 'StoryController@getActions'],
    ['GET', '/admin/stories/actions/([0-9]+)', 'StoryController@showAction'],
    ['POST', '/admin/stories/actions', 'StoryController@createAction'],
    ['PUT', '/admin/stories/actions/([0-9]+)', 'StoryController@updateAction'],
    ['DELETE', '/admin/stories/actions/([0-9]+)', 'StoryController@deleteAction'],

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
