<?php

namespace App\Controllers;

use App\Models\RandomPreset;
use App\Models\DiceAction;
use App\Models\DicePart;
use App\Models\ZishiPose;
use App\Models\ZishiPlace;
use App\Models\ZishiTime;
use App\Models\Task;
use App\Models\StoryMaleRole;
use App\Models\StoryFemaleRole;
use App\Models\StoryRelationship;
use App\Models\StoryInitiative;
use App\Models\StoryBehavior;
use App\Models\StoryAction;
use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Preset Controller (预设 - 统一预设，一个链接包含所有游戏类型)
 */
class PresetController extends Controller
{
    private RandomPreset $presetModel;
    private DiceAction $actionModel;
    private DicePart $partModel;
    private ZishiPose $poseModel;
    private ZishiPlace $placeModel;
    private ZishiTime $timeModel;
    private Task $taskModel;
    private StoryMaleRole $storyMaleRoleModel;
    private StoryFemaleRole $storyFemaleRoleModel;
    private StoryRelationship $storyRelationshipModel;
    private StoryInitiative $storyInitiativeModel;
    private StoryBehavior $storyBehaviorModel;
    private StoryAction $storyActionModel;

    public function __construct()
    {
        $this->presetModel = new RandomPreset();
        $this->actionModel = new DiceAction();
        $this->partModel = new DicePart();
        $this->poseModel = new ZishiPose();
        $this->placeModel = new ZishiPlace();
        $this->timeModel = new ZishiTime();
        $this->taskModel = new Task();
        $this->storyMaleRoleModel = new StoryMaleRole();
        $this->storyFemaleRoleModel = new StoryFemaleRole();
        $this->storyRelationshipModel = new StoryRelationship();
        $this->storyInitiativeModel = new StoryInitiative();
        $this->storyBehaviorModel = new StoryBehavior();
        $this->storyActionModel = new StoryAction();
    }

    /**
     * Get preset by share code (public)
     */
    public function getByCode(string $code): void
    {
        $preset = $this->presetModel->getWithRounds($code);

        if (!$preset) {
            Response::notFound('预设不存在');
        }

        Response::success($preset);
    }

    /**
     * Get all presets (admin)
     */
    public function list(): void
    {
        AuthService::requireAuth();
        $presets = $this->presetModel->all();
        Response::success($presets);
    }

    /**
     * Get preset with rounds (admin)
     */
    public function show(int $id): void
    {
        AuthService::requireAuth();
        $preset = $this->presetModel->getByIdWithRounds($id);

        if (!$preset) {
            Response::notFound('预设不存在');
        }

        Response::success($preset);
    }

    /**
     * Create preset (admin)
     */
    public function create(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => '请输入预设名称']);
        }

        $shareCode = $input['share_code'] ?? $this->presetModel->generateShareCode();

        $id = $this->presetModel->create([
            'name' => $input['name'],
            'share_code' => $shareCode,
            'rounds' => $input['rounds'] ?? 5,
            'is_active' => $input['is_active'] ?? 1
        ]);

        // Save rounds if provided
        if (!empty($input['rounds_data'])) {
            $this->presetModel->saveRounds($id, $input['rounds_data']);
        }

        Response::success([
            'id' => $id,
            'share_code' => $shareCode,
            'message' => '预设创建成功'
        ]);
    }

    /**
     * Update preset (admin)
     */
    public function update(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $preset = $this->presetModel->find($id);
        if (!$preset) {
            Response::notFound('预设不存在');
        }

        $updateData = [];
        if (isset($input['name'])) $updateData['name'] = $input['name'];
        if (isset($input['rounds'])) $updateData['rounds'] = $input['rounds'];
        if (isset($input['is_active'])) $updateData['is_active'] = $input['is_active'];

        if (!empty($updateData)) {
            $this->presetModel->update($id, $updateData);
        }

        // Save rounds if provided
        if (isset($input['rounds_data'])) {
            $this->presetModel->saveRounds($id, $input['rounds_data']);
        }

        Response::success(['message' => '预设更新成功']);
    }

    /**
     * Delete preset (admin)
     */
    public function delete(int $id): void
    {
        AuthService::requireAuth();

        $preset = $this->presetModel->find($id);
        if (!$preset) {
            Response::notFound('预设不存在');
        }

        $this->presetModel->deleteWithRounds($id);
        Response::success(['message' => '预设删除成功']);
    }

    /**
     * Generate random rounds (admin helper)
     * Generates random data for all four game types simultaneously
     */
    public function generateRandom(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $roundsCount = $input['rounds'] ?? 5;

        // Load all required data
        $actions = $this->actionModel->getActive();
        $parts = $this->partModel->getActive();
        $poses = $this->poseModel->getActive();
        $places = $this->placeModel->getActive();
        $times = $this->timeModel->getActive();
        $tasks = $this->taskModel->getActive();
        $maleRoles = $this->storyMaleRoleModel->getActive();
        $femaleRoles = $this->storyFemaleRoleModel->getActive();
        $relationships = $this->storyRelationshipModel->getActive();
        $initiatives = $this->storyInitiativeModel->getActive();
        $behaviors = $this->storyBehaviorModel->getActive();
        $storyActions = $this->storyActionModel->getActive();

        // Validate we have enough data
        if (empty($actions) || empty($parts)) {
            Response::error('骰子数据不足，请先添加动作和部位', 400);
        }
        if (empty($poses) || empty($places) || empty($times)) {
            Response::error('姿势数据不足，请先添加姿势、地点和时间', 400);
        }
        if (empty($tasks)) {
            Response::error('任务数据不足，请先添加任务', 400);
        }
        if (empty($maleRoles) || empty($femaleRoles) || empty($relationships) || empty($initiatives) || empty($behaviors) || empty($storyActions)) {
            Response::error('剧情数据不足，请先添加所有剧情元素', 400);
        }

        $roundsData = [];

        for ($i = 1; $i <= $roundsCount; $i++) {
            $roundsData[] = [
                'round_number' => $i,
                // Dice data
                'dice_action_id' => $actions[array_rand($actions)]['id'],
                'dice_part_id' => $parts[array_rand($parts)]['id'],
                // Pose data
                'pose_id' => $poses[array_rand($poses)]['id'],
                'place_id' => $places[array_rand($places)]['id'],
                'time_id' => $times[array_rand($times)]['id'],
                // Task data
                'task_id' => $tasks[array_rand($tasks)]['id'],
                // Story data
                'story_male_role_id' => $maleRoles[array_rand($maleRoles)]['id'],
                'story_female_role_id' => $femaleRoles[array_rand($femaleRoles)]['id'],
                'story_relationship_id' => $relationships[array_rand($relationships)]['id'],
                'story_initiative_id' => $initiatives[array_rand($initiatives)]['id'],
                'story_behavior_id' => $behaviors[array_rand($behaviors)]['id'],
                'story_action_id' => $storyActions[array_rand($storyActions)]['id']
            ];
        }

        Response::success(['rounds' => $roundsData]);
    }

    /**
     * Get form data for preset editing (admin)
     * Returns all data needed for creating/editing presets
     */
    public function getFormData(): void
    {
        AuthService::requireAuth();

        $data = [
            'actions' => $this->actionModel->getActive(),
            'parts' => $this->partModel->getActive(),
            'poses' => $this->poseModel->getActive(),
            'places' => $this->placeModel->getActive(),
            'times' => $this->timeModel->getActive(),
            'tasks' => $this->taskModel->getActive(),
            'maleRoles' => $this->storyMaleRoleModel->getActive(),
            'femaleRoles' => $this->storyFemaleRoleModel->getActive(),
            'relationships' => $this->storyRelationshipModel->getActive(),
            'initiatives' => $this->storyInitiativeModel->getActive(),
            'behaviors' => $this->storyBehaviorModel->getActive(),
            'storyActions' => $this->storyActionModel->getActive()
        ];

        Response::success($data);
    }
}
