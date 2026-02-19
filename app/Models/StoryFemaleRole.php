<?php

namespace App\Models;

/**
 * Story Female Role Model (女方身份)
 */
class StoryFemaleRole extends Model
{
    protected string $table = 'story_female_roles';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
