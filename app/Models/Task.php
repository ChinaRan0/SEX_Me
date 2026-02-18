<?php

namespace App\Models;

/**
 * Task Model (随机任务库)
 */
class Task extends Model
{
    protected string $table = 'tasks';
    protected array $fillable = ['description', 'is_active'];

    /**
     * Get active records only (override - no sort_order column)
     */
    public function getActive(string $orderBy = 'id ASC'): array
    {
        return parent::getActive($orderBy);
    }

    /**
     * Override all() - tasks table has no sort_order column
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        return parent::all($orderBy);
    }

    /**
     * Get random task
     */
    public function getRandomTask(): ?array
    {
        return $this->getRandom();
    }
}
