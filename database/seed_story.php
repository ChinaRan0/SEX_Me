<?php
/**
 * Story Data Seeder - Import story data to SQLite
 *
 * Run: php database/seed_story.php
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

echo "Starting story data import...\n";

// Initialize schema
DatabaseService::initSchema();
$db = DatabaseService::getInstance();

// Clear existing story data (optional - comment out if you want to keep existing data)
echo "Clearing existing story data...\n";
$db->exec("DELETE FROM story_male_roles");
$db->exec("DELETE FROM story_female_roles");
$db->exec("DELETE FROM story_relationships");
$db->exec("DELETE FROM story_initiatives");
$db->exec("DELETE FROM story_behaviors");
$db->exec("DELETE FROM story_actions");

// Import male roles (男方身份)
echo "Importing male roles...\n";
$maleRoles = ['快递员', '修水管道', '健身教练', '警察', '房东', '家教'];
$stmt = $db->prepare("INSERT INTO story_male_roles (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($maleRoles as $index => $role) {
    $stmt->execute([$role, $index]);
}
echo "  - Imported " . count($maleRoles) . " male roles\n";

// Import female roles (女方身份)
echo "Importing female roles...\n";
$femaleRoles = ['主播', '空姐', '学生', '主妇', '白领', '护士', '人妻', '贵妇', '女警', '女仆', '管家'];
$stmt = $db->prepare("INSERT INTO story_female_roles (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($femaleRoles as $index => $role) {
    $stmt->execute([$role, $index]);
}
echo "  - Imported " . count($femaleRoles) . " female roles\n";

// Import relationships (两人关系)
echo "Importing relationships...\n";
$relationships = ['主雇', '兄妹', '姐弟', '母子', '父女', '陌生人', '朋友'];
$stmt = $db->prepare("INSERT INTO story_relationships (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($relationships as $index => $relationship) {
    $stmt->execute([$relationship, $index]);
}
echo "  - Imported " . count($relationships) . " relationships\n";

// Import initiatives (主动权)
echo "Importing initiatives...\n";
$initiatives = ['男方', '女方'];
$stmt = $db->prepare("INSERT INTO story_initiatives (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($initiatives as $index => $initiative) {
    $stmt->execute([$initiative, $index]);
}
echo "  - Imported " . count($initiatives) . " initiatives\n";

// Import behaviors (行为)
echo "Importing behaviors...\n";
$behaviors = ['一见钟情', '性压抑'];
$stmt = $db->prepare("INSERT INTO story_behaviors (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($behaviors as $index => $behavior) {
    $stmt->execute([$behavior, $index]);
}
echo "  - Imported " . count($behaviors) . " behaviors\n";

// Import actions (动作)
echo "Importing actions...\n";
$actions = ['在写作业', '空姐', '女主播', '在做家务'];
$stmt = $db->prepare("INSERT INTO story_actions (name, sort_order, is_active) VALUES (?, ?, 1)");
foreach ($actions as $index => $action) {
    $stmt->execute([$action, $index]);
}
echo "  - Imported " . count($actions) . " actions\n";

echo "\nStory data import complete!\n";
echo "Database location: " . DB_PATH . "\n";

// Show table counts
echo "\nStory table counts:\n";
$tables = ['story_male_roles', 'story_female_roles', 'story_relationships', 'story_initiatives', 'story_behaviors', 'story_actions'];
foreach ($tables as $table) {
    $result = DatabaseService::fetchOne("SELECT COUNT(*) as count FROM {$table}");
    echo "  - {$table}: {$result['count']}\n";
}
