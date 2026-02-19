<?php

namespace App\Models;

/**
 * Story Initiative Model (主动权)
 */
class StoryInitiative extends Model
{
    protected string $table = 'story_initiatives';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
