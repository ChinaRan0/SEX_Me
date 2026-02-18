<?php

namespace App\Models;

/**
 * Zishi Time Model
 */
class ZishiTime extends Model
{
    protected string $table = 'zishi_times';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
