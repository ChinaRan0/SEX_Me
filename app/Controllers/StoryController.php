<?php

namespace App\Controllers;

use App\Models\StoryMaleRole;
use App\Models\StoryFemaleRole;
use App\Models\StoryRelationship;
use App\Models\StoryInitiative;
use App\Models\StoryBehavior;
use App\Models\StoryAction;
use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Story Controller (随机剧情)
 */
class StoryController extends Controller
{
    private StoryMaleRole $maleRoleModel;
    private StoryFemaleRole $femaleRoleModel;
    private StoryRelationship $relationshipModel;
    private StoryInitiative $initiativeModel;
    private StoryBehavior $behaviorModel;
    private StoryAction $actionModel;

    public function __construct()
    {
        $this->maleRoleModel = new StoryMaleRole();
        $this->femaleRoleModel = new StoryFemaleRole();
        $this->relationshipModel = new StoryRelationship();
        $this->initiativeModel = new StoryInitiative();
        $this->behaviorModel = new StoryBehavior();
        $this->actionModel = new StoryAction();
    }

    /**
     * Get all active story elements (public)
     */
    public function index(): void
    {
        $maleRoles = $this->maleRoleModel->getActive();
        $femaleRoles = $this->femaleRoleModel->getActive();
        $relationships = $this->relationshipModel->getActive();
        $initiatives = $this->initiativeModel->getActive();
        $behaviors = $this->behaviorModel->getActive();
        $actions = $this->actionModel->getActive();

        Response::success([
            'maleRoles' => array_column($maleRoles, 'name'),
            'femaleRoles' => array_column($femaleRoles, 'name'),
            'relationships' => array_column($relationships, 'name'),
            'initiatives' => array_column($initiatives, 'name'),
            'behaviors' => array_column($behaviors, 'name'),
            'actions' => array_column($actions, 'name')
        ]);
    }

    /**
     * Get a random story combination (public)
     */
    public function random(): void
    {
        $maleRole = $this->maleRoleModel->getRandom();
        $femaleRole = $this->femaleRoleModel->getRandom();
        $relationship = $this->relationshipModel->getRandom();
        $initiative = $this->initiativeModel->getRandom();
        $behavior = $this->behaviorModel->getRandom();
        $action = $this->actionModel->getRandom();

        if (!$maleRole || !$femaleRole || !$relationship || !$initiative || !$behavior || !$action) {
            Response::error('Not enough story data available');
            return;
        }

        Response::success([
            'maleRole' => $maleRole['name'],
            'femaleRole' => $femaleRole['name'],
            'relationship' => $relationship['name'],
            'initiative' => $initiative['name'],
            'behavior' => $behavior['name'],
            'action' => $action['name']
        ]);
    }

    /**
     * Get all story data for admin list (admin)
     */
    public function list(): void
    {
        AuthService::requireAuth();
        Response::success([
            'maleRoles' => $this->maleRoleModel->all(),
            'femaleRoles' => $this->femaleRoleModel->all(),
            'relationships' => $this->relationshipModel->all(),
            'initiatives' => $this->initiativeModel->all(),
            'behaviors' => $this->behaviorModel->all(),
            'actions' => $this->actionModel->all()
        ]);
    }

    // ==================== Male Roles CRUD ====================

    /**
     * Get all male roles (admin)
     */
    public function getMaleRoles(): void
    {
        AuthService::requireAuth();
        Response::success($this->maleRoleModel->all());
    }

    /**
     * Get single male role (admin)
     */
    public function showMaleRole(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->maleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Male role not found');
        }

        Response::success($item);
    }

    /**
     * Create male role (admin)
     */
    public function createMaleRole(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->maleRoleModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Male role created']);
    }

    /**
     * Update male role (admin)
     */
    public function updateMaleRole(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->maleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Male role not found');
        }

        $this->maleRoleModel->update($id, $input);
        Response::success(['message' => 'Male role updated']);
    }

    /**
     * Delete male role (admin)
     */
    public function deleteMaleRole(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->maleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Male role not found');
        }

        $this->maleRoleModel->delete($id);
        Response::success(['message' => 'Male role deleted']);
    }

    // ==================== Female Roles CRUD ====================

    /**
     * Get all female roles (admin)
     */
    public function getFemaleRoles(): void
    {
        AuthService::requireAuth();
        Response::success($this->femaleRoleModel->all());
    }

    /**
     * Get single female role (admin)
     */
    public function showFemaleRole(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->femaleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Female role not found');
        }

        Response::success($item);
    }

    /**
     * Create female role (admin)
     */
    public function createFemaleRole(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->femaleRoleModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Female role created']);
    }

    /**
     * Update female role (admin)
     */
    public function updateFemaleRole(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->femaleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Female role not found');
        }

        $this->femaleRoleModel->update($id, $input);
        Response::success(['message' => 'Female role updated']);
    }

    /**
     * Delete female role (admin)
     */
    public function deleteFemaleRole(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->femaleRoleModel->find($id);
        if (!$item) {
            Response::notFound('Female role not found');
        }

        $this->femaleRoleModel->delete($id);
        Response::success(['message' => 'Female role deleted']);
    }

    // ==================== Relationships CRUD ====================

    /**
     * Get all relationships (admin)
     */
    public function getRelationships(): void
    {
        AuthService::requireAuth();
        Response::success($this->relationshipModel->all());
    }

    /**
     * Get single relationship (admin)
     */
    public function showRelationship(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->relationshipModel->find($id);
        if (!$item) {
            Response::notFound('Relationship not found');
        }

        Response::success($item);
    }

    /**
     * Create relationship (admin)
     */
    public function createRelationship(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->relationshipModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Relationship created']);
    }

    /**
     * Update relationship (admin)
     */
    public function updateRelationship(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->relationshipModel->find($id);
        if (!$item) {
            Response::notFound('Relationship not found');
        }

        $this->relationshipModel->update($id, $input);
        Response::success(['message' => 'Relationship updated']);
    }

    /**
     * Delete relationship (admin)
     */
    public function deleteRelationship(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->relationshipModel->find($id);
        if (!$item) {
            Response::notFound('Relationship not found');
        }

        $this->relationshipModel->delete($id);
        Response::success(['message' => 'Relationship deleted']);
    }

    // ==================== Initiatives CRUD ====================

    /**
     * Get all initiatives (admin)
     */
    public function getInitiatives(): void
    {
        AuthService::requireAuth();
        Response::success($this->initiativeModel->all());
    }

    /**
     * Get single initiative (admin)
     */
    public function showInitiative(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->initiativeModel->find($id);
        if (!$item) {
            Response::notFound('Initiative not found');
        }

        Response::success($item);
    }

    /**
     * Create initiative (admin)
     */
    public function createInitiative(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->initiativeModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Initiative created']);
    }

    /**
     * Update initiative (admin)
     */
    public function updateInitiative(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->initiativeModel->find($id);
        if (!$item) {
            Response::notFound('Initiative not found');
        }

        $this->initiativeModel->update($id, $input);
        Response::success(['message' => 'Initiative updated']);
    }

    /**
     * Delete initiative (admin)
     */
    public function deleteInitiative(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->initiativeModel->find($id);
        if (!$item) {
            Response::notFound('Initiative not found');
        }

        $this->initiativeModel->delete($id);
        Response::success(['message' => 'Initiative deleted']);
    }

    // ==================== Behaviors CRUD ====================

    /**
     * Get all behaviors (admin)
     */
    public function getBehaviors(): void
    {
        AuthService::requireAuth();
        Response::success($this->behaviorModel->all());
    }

    /**
     * Get single behavior (admin)
     */
    public function showBehavior(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->behaviorModel->find($id);
        if (!$item) {
            Response::notFound('Behavior not found');
        }

        Response::success($item);
    }

    /**
     * Create behavior (admin)
     */
    public function createBehavior(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->behaviorModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Behavior created']);
    }

    /**
     * Update behavior (admin)
     */
    public function updateBehavior(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->behaviorModel->find($id);
        if (!$item) {
            Response::notFound('Behavior not found');
        }

        $this->behaviorModel->update($id, $input);
        Response::success(['message' => 'Behavior updated']);
    }

    /**
     * Delete behavior (admin)
     */
    public function deleteBehavior(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->behaviorModel->find($id);
        if (!$item) {
            Response::notFound('Behavior not found');
        }

        $this->behaviorModel->delete($id);
        Response::success(['message' => 'Behavior deleted']);
    }

    // ==================== Actions CRUD ====================

    /**
     * Get all actions (admin)
     */
    public function getActions(): void
    {
        AuthService::requireAuth();
        Response::success($this->actionModel->all());
    }

    /**
     * Get single action (admin)
     */
    public function showAction(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->actionModel->find($id);
        if (!$item) {
            Response::notFound('Action not found');
        }

        Response::success($item);
    }

    /**
     * Create action (admin)
     */
    public function createAction(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->actionModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Action created']);
    }

    /**
     * Update action (admin)
     */
    public function updateAction(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $item = $this->actionModel->find($id);
        if (!$item) {
            Response::notFound('Action not found');
        }

        $this->actionModel->update($id, $input);
        Response::success(['message' => 'Action updated']);
    }

    /**
     * Delete action (admin)
     */
    public function deleteAction(int $id): void
    {
        AuthService::requireAuth();

        $item = $this->actionModel->find($id);
        if (!$item) {
            Response::notFound('Action not found');
        }

        $this->actionModel->delete($id);
        Response::success(['message' => 'Action deleted']);
    }
}
