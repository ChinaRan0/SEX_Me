<?php

namespace App\Controllers;

use App\Models\ZishiPlace;
use App\Models\ZishiPose;
use App\Models\ZishiTime;
use App\Helpers\Response;
use App\Services\AuthService;

/**
 * Pose Controller
 */
class PoseController extends Controller
{
    private ZishiPlace $placeModel;
    private ZishiPose $poseModel;
    private ZishiTime $timeModel;

    public function __construct()
    {
        $this->placeModel = new ZishiPlace();
        $this->poseModel = new ZishiPose();
        $this->timeModel = new ZishiTime();
    }

    /**
     * Get pose data (public)
     */
    public function index(): void
    {
        $places = $this->placeModel->getActive();
        $poses = $this->poseModel->getActiveWithImages();
        $times = $this->timeModel->getActive();

        // Format poses for frontend
        $poseDetails = [];
        foreach ($poses as $pose) {
            $poseDetails[$pose['name']] = [
                'image' => $pose['image_path'],
                'description' => $pose['description']
            ];
        }

        Response::success([
            'places' => array_column($places, 'name'),
            'poses' => array_column($poses, 'name'),
            'times' => array_column($times, 'name'),
            'poseDetails' => $poseDetails
        ]);
    }

    /**
     * Get all places (admin)
     */
    public function getPlaces(): void
    {
        AuthService::requireAuth();
        Response::success($this->placeModel->all());
    }

    /**
     * Get single place (admin)
     */
    public function showPlace(int $id): void
    {
        AuthService::requireAuth();

        $place = $this->placeModel->find($id);
        if (!$place) {
            Response::notFound('Place not found');
        }

        Response::success($place);
    }

    /**
     * Create place (admin)
     */
    public function createPlace(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->placeModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Place created']);
    }

    /**
     * Update place (admin)
     */
    public function updatePlace(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $place = $this->placeModel->find($id);
        if (!$place) {
            Response::notFound('Place not found');
        }

        $this->placeModel->update($id, $input);
        Response::success(['message' => 'Place updated']);
    }

    /**
     * Delete place (admin)
     */
    public function deletePlace(int $id): void
    {
        AuthService::requireAuth();

        $place = $this->placeModel->find($id);
        if (!$place) {
            Response::notFound('Place not found');
        }

        $this->placeModel->delete($id);
        Response::success(['message' => 'Place deleted']);
    }

    /**
     * Get all poses (admin)
     */
    public function getPoses(): void
    {
        AuthService::requireAuth();
        Response::success($this->poseModel->all());
    }

    /**
     * Get single pose (admin)
     */
    public function showPose(int $id): void
    {
        AuthService::requireAuth();

        $pose = $this->poseModel->find($id);
        if (!$pose) {
            Response::notFound('Pose not found');
        }

        Response::success($pose);
    }

    /**
     * Create pose (admin)
     */
    public function createPose(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->poseModel->create([
            'name' => $input['name'],
            'image_path' => $input['image_path'] ?? null,
            'description' => $input['description'] ?? null,
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Pose created']);
    }

    /**
     * Update pose (admin)
     */
    public function updatePose(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $pose = $this->poseModel->find($id);
        if (!$pose) {
            Response::notFound('Pose not found');
        }

        $this->poseModel->update($id, $input);
        Response::success(['message' => 'Pose updated']);
    }

    /**
     * Delete pose (admin)
     */
    public function deletePose(int $id): void
    {
        AuthService::requireAuth();

        $pose = $this->poseModel->find($id);
        if (!$pose) {
            Response::notFound('Pose not found');
        }

        $this->poseModel->delete($id);
        Response::success(['message' => 'Pose deleted']);
    }

    /**
     * Get all times (admin)
     */
    public function getTimes(): void
    {
        AuthService::requireAuth();
        Response::success($this->timeModel->all());
    }

    /**
     * Get single time (admin)
     */
    public function showTime(int $id): void
    {
        AuthService::requireAuth();

        $time = $this->timeModel->find($id);
        if (!$time) {
            Response::notFound('Time not found');
        }

        Response::success($time);
    }

    /**
     * Create time (admin)
     */
    public function createTime(): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        if (empty($input['name'])) {
            Response::validationError(['name' => 'Name is required']);
        }

        $id = $this->timeModel->create([
            'name' => $input['name'],
            'sort_order' => $input['sort_order'] ?? 0,
            'is_active' => $input['is_active'] ?? 1
        ]);

        Response::success(['id' => $id, 'message' => 'Time created']);
    }

    /**
     * Update time (admin)
     */
    public function updateTime(int $id): void
    {
        AuthService::requireAuth();
        $input = $this->getInput();

        $time = $this->timeModel->find($id);
        if (!$time) {
            Response::notFound('Time not found');
        }

        $this->timeModel->update($id, $input);
        Response::success(['message' => 'Time updated']);
    }

    /**
     * Delete time (admin)
     */
    public function deleteTime(int $id): void
    {
        AuthService::requireAuth();

        $time = $this->timeModel->find($id);
        if (!$time) {
            Response::notFound('Time not found');
        }

        $this->timeModel->delete($id);
        Response::success(['message' => 'Time deleted']);
    }
}
