<?php

namespace App\Models;

/**
 * Story Relationship Model (两人关系)
 */
class StoryRelationship extends Model
{
    protected string $table = 'story_relationships';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
