<?php

namespace App\Models;

/**
 * Dice Action Model
 */
class DiceAction extends Model
{
    protected string $table = 'dice_actions';
    protected array $fillable = ['name', 'sort_order', 'is_active'];
}
