<?php

namespace App\Models;

/**
 * Zishi Pose Model
 */
class ZishiPose extends Model
{
    protected string $table = 'zishi_poses';
    protected array $fillable = ['name', 'image_path', 'description', 'sort_order', 'is_active'];

    /**
     * Get all poses with full image URL
     */
    public function getActiveWithImages(string $orderBy = 'sort_order ASC, id ASC'): array
    {
        $poses = $this->getActive($orderBy);
        foreach ($poses as &$pose) {
            if ($pose['image_path']) {
                $pose['image_url'] = $this->getImageUrl($pose['image_path']);
            }
        }
        return $poses;
    }

    /**
     * Get image URL
     */
    private function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        // Return relative path for frontend
        return $path;
    }
}
