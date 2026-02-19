<?php

namespace App\Models;

/**
 * Story Action Model (动作)
 */
class StoryAction extends Model
{
    protected string $table = 'story_actions';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
