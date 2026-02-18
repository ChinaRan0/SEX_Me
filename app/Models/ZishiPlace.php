<?php

namespace App\Models;

/**
 * Zishi Place Model
 */
class ZishiPlace extends Model
{
    protected string $table = 'zishi_places';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
