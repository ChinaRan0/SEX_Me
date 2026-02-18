<?php

namespace App\Models;

/**
 * Dice Part Model
 */
class DicePart extends Model
{
    protected string $table = 'dice_parts';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
