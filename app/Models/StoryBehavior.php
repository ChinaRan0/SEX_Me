<?php

namespace App\Models;

/**
 * Story Behavior Model (行为)
 */
class StoryBehavior extends Model
{
    protected string $table = 'story_behaviors';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
