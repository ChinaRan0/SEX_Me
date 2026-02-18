<?php

namespace App\Controllers;

use App\Models\Task;
use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Task Controller (随机任务库)
 */
class TaskController extends Controller
{
    private Task $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    /**
     * Get all active tasks (public)
     */
    public function index(): void
    {
        $tasks = $this->taskModel->getActive();
        Response::success($tasks);
    }

    /**
     * Get random task (public)
     */
    public function random(): void
    {
        $task = $this->taskModel->getRandomTask();

        if (!$task) {
            Response::notFound('No tasks available');
        }

        Response::success($task);
    }

    /**
     * Get all tasks (admin)
     */
    public function list(): void
    {
        AuthService::requireAuth();
        $tasks = $this->taskModel->all();
        Response::success($tasks);
    }

    /**
     * Get single task (admin)
     */
    public function show(int $id): void
    {
        AuthService::requireAuth();
        $task = $this->taskModel->find($id);

        if (!$task) {
            Response::notFound('Task not found');
        }

        Response::success($task);
    }

    /**
     * Create task (admin)
     */
    public function create(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['description'])) {
            Response::validationError(['description' => 'Description is required']);
        }

        $id = $this->taskModel->create([
            'description' => $input['description'],
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Task created']);
    }

    /**
     * Update task (admin)
     */
    public function update(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $task = $this->taskModel->find($id);
        if (!$task) {
            Response::notFound('Task not found');
        }

        $this->taskModel->update($id, $input);
        Response::success(['message' => 'Task updated']);
    }

    /**
     * Delete task (admin)
     */
    public function delete(int $id): void
    {
        AuthService::requireAuth();

        $task = $this->taskModel->find($id);
        if (!$task) {
            Response::notFound('Task not found');
        }

        $this->taskModel->delete($id);
        Response::success(['message' => 'Task deleted']);
    }
}
