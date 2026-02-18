<?php
/**
 * Data Seeder - Import existing JSON data to SQLite
 *
 * Run: php database/seed.php
 */

require_once __DIR__ . '/../config/config.php';

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

use App\Services\DatabaseService;

echo "Starting data import...\n";

// Delete old database and recreate
$dbPath = DB_PATH;
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "Old database deleted.\n";
}

// Initialize schema
DatabaseService::initSchema();
$db = DatabaseService::getInstance();

// Import dice data
echo "Importing dice data...\n";
$diceData = json_decode(file_get_contents(__DIR__ . '/../touzi.json'), true);

if ($diceData) {
    // Import actions
    $stmt = $db->prepare("INSERT INTO dice_actions (name, sort_order, is_active) VALUES (?, ?, 1)");
    foreach ($diceData['actions'] as $index => $action) {
        $stmt->execute([$action, $index]);
    }
    echo "  - Imported " . count($diceData['actions']) . " actions\n";

    // Import parts
    $stmt = $db->prepare("INSERT INTO dice_parts (name, sort_order, is_active) VALUES (?, ?, 1)");
    foreach ($diceData['parts'] as $index => $part) {
        $stmt->execute([$part, $index]);
    }
    echo "  - Imported " . count($diceData['parts']) . " parts\n";
}

// Import pose data
echo "Importing pose data...\n";
$poseData = json_decode(file_get_contents(__DIR__ . '/../zishi.json'), true);

if ($poseData) {
    // Import places
    $stmt = $db->prepare("INSERT INTO zishi_places (name, sort_order, is_active) VALUES (?, ?, 1)");
    foreach ($poseData['places'] as $index => $place) {
        $stmt->execute([$place, $index]);
    }
    echo "  - Imported " . count($poseData['places']) . " places\n";

    // Import times
    $stmt = $db->prepare("INSERT INTO zishi_times (name, sort_order, is_active) VALUES (?, ?, 1)");
    foreach ($poseData['times'] as $index => $time) {
        $stmt->execute([$time, $index]);
    }
    echo "  - Imported " . count($poseData['times']) . " times\n";

    // Import poses with details
    $stmt = $db->prepare("INSERT INTO zishi_poses (name, image_path, description, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
    foreach ($poseData['poses'] as $index => $poseName) {
        $details = $poseData['poseDetails'][$poseName] ?? [];
        $image = $details['image'] ?? null;
        $description = $details['description'] ?? null;
        $stmt->execute([$poseName, $image, $description, $index]);
    }
    echo "  - Imported " . count($poseData['poses']) . " poses\n";
}

// Import task data (only description field now)
echo "Importing task data...\n";
$taskData = json_decode(file_get_contents(__DIR__ . '/../18.json'), true);

if ($taskData && isset($taskData['tasks'])) {
    // Import tasks
    $stmt = $db->prepare("INSERT INTO tasks (description, is_active) VALUES (?, 1)");
    $count = 0;
    foreach ($taskData['tasks'] as $task) {
        $description = $task['description'] ?? '';
        if (!empty($description)) {
            $stmt->execute([$description]);
            $count++;
        }
    }
    echo "  - Imported {$count} tasks\n";
}

// Create default admin
echo "Creating default admin...\n";
App\Services\AuthService::createDefaultAdmin();
echo "  - Default admin created (username: admin, password: admin123)\n";

echo "\nData import complete!\n";
echo "Database location: " . DB_PATH . "\n";

// Show table counts
echo "\nTable counts:\n";
$tables = ['dice_actions', 'dice_parts', 'zishi_places', 'zishi_poses', 'zishi_times', 'tasks', 'admins'];
foreach ($tables as $table) {
    $result = DatabaseService::fetchOne("SELECT COUNT(*) as count FROM {$table}");
    echo "  - {$table}: {$result['count']}\n";
}
