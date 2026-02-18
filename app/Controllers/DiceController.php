<?php

namespace App\Controllers;

use App\Models\DiceAction;
use App\Models\DicePart;
use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Dice Controller
 */
class DiceController extends Controller
{
    private DiceAction $actionModel;
    private DicePart $partModel;

    public function __construct()
    {
        $this->actionModel = new DiceAction();
        $this->partModel = new DicePart();
    }

    /**
     * Get dice data (public)
     */
    public function index(): void
    {
        $actions = $this->actionModel->getActive();
        $parts = $this->partModel->getActive();

        Response::success([
            'actions' => array_column($actions, 'name'),
            'parts' => array_column($parts, 'name')
        ]);
    }

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
        $action = $this->actionModel->find($id);
        if (!$action) {
            Response::notFound('Action not found');
        }
        Response::success($action);
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

        $action = $this->actionModel->find($id);
        if (!$action) {
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

        $action = $this->actionModel->find($id);
        if (!$action) {
            Response::notFound('Action not found');
        }

        $this->actionModel->delete($id);
        Response::success(['message' => 'Action deleted']);
    }

    /**
     * Get all parts (admin)
     */
    public function getParts(): void
    {
        AuthService::requireAuth();
        Response::success($this->partModel->all());
    }

    /**
     * Get single part (admin)
     */
    public function showPart(int $id): void
    {
        AuthService::requireAuth();
        $part = $this->partModel->find($id);
        if (!$part) {
            Response::notFound('Part not found');
        }
        Response::success($part);
    }

    /**
     * Create part (admin)
     */
    public function createPart(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->partModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Part created']);
    }

    /**
     * Update part (admin)
     */
    public function updatePart(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $part = $this->partModel->find($id);
        if (!$part) {
            Response::notFound('Part not found');
        }

        $this->partModel->update($id, $input);
        Response::success(['message' => 'Part updated']);
    }

    /**
     * Delete part (admin)
     */
    public function deletePart(int $id): void
    {
        AuthService::requireAuth();

        $part = $this->partModel->find($id);
        if (!$part) {
            Response::notFound('Part not found');
        }

        $this->partModel->delete($id);
        Response::success(['message' => 'Part deleted']);
    }
}
