<?php

namespace App\Models;

/**
 * Story Male Role Model (男方身份)
 */
class StoryMaleRole extends Model
{
    protected string $table = 'story_male_roles';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
