<?php

namespace App\Models;

use App\Services\DatabaseService;

/**
 * Preset Model (预设配置 - 统一预设，包含骰子+姿势+任务)
 */
class RandomPreset extends Model
{
    protected string $table = 'presets';
    protected array $fillable = ['name', 'share_code', 'rounds', 'is_active'];

    /**
     * Override all() - presets table has no sort_order column
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        return DatabaseService::fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    /**
     * Override getActive() - presets table has no sort_order column
     */
    public function getActive(string $orderBy = 'id DESC'): array
    {
        return DatabaseService::fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY {$orderBy}"
        );
    }

    /**
     * Generate unique share code
     */
    public function generateShareCode(): string
    {
        do {
            $code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
            $exists = DatabaseService::fetchOne(
                "SELECT id FROM {$this->table} WHERE share_code = ?",
                [$code]
            );
        } while ($exists);

        return $code;
    }

    /**
     * Get preset with rounds by share code
     */
    public function getWithRounds(string $shareCode): ?array
    {
        $preset = DatabaseService::fetchOne(
            "SELECT * FROM {$this->table} WHERE share_code = ? AND is_active = 1",
            [$shareCode]
        );

        if (!$preset) {
            return null;
        }

        $preset['rounds_data'] = $this->getRounds($preset['id']);
        return $preset;
    }

    /**
     * Get preset by ID with rounds
     */
    public function getByIdWithRounds(int $id): ?array
    {
        $preset = $this->find($id);

        if (!$preset) {
            return null;
        }

        $preset['rounds_data'] = $this->getRounds($preset['id']);
        return $preset;
    }

    /**
     * Get rounds from unified preset_rounds table (includes dice, pose, task data)
     */
    private function getRounds(int $presetId): array
    {
        return DatabaseService::fetchAll("
            SELECT
                r.round_number,
                -- Dice data
                da.name as dice_action_name,
                dp.name as dice_part_name,
                -- Pose data
                po.name as pose_name,
                po.image_path as pose_image,
                po.description as pose_description,
                pl.name as place_name,
                t.name as time_name,
                -- Task data
                tk.description as task_description
            FROM preset_rounds r
            LEFT JOIN dice_actions da ON r.dice_action_id = da.id
            LEFT JOIN dice_parts dp ON r.dice_part_id = dp.id
            LEFT JOIN zishi_poses po ON r.pose_id = po.id
            LEFT JOIN zishi_places pl ON r.place_id = pl.id
            LEFT JOIN zishi_times t ON r.time_id = t.id
            LEFT JOIN tasks tk ON r.task_id = tk.id
            WHERE r.preset_id = ?
            ORDER BY r.round_number ASC
        ", [$presetId]);
    }

    /**
     * Save rounds to unified preset_rounds table
     */
    public function saveRounds(int $presetId, array $rounds): void
    {
        // Delete existing rounds
        $this->deleteRounds($presetId);

        foreach ($rounds as $round) {
            DatabaseService::execute(
                "INSERT INTO preset_rounds (
                    preset_id, round_number,
                    dice_action_id, dice_part_id,
                    pose_id, place_id, time_id,
                    task_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $presetId,
                    $round['round_number'] ?? 0,
                    $round['dice_action_id'] ?? null,
                    $round['dice_part_id'] ?? null,
                    $round['pose_id'] ?? null,
                    $round['place_id'] ?? null,
                    $round['time_id'] ?? null,
                    $round['task_id'] ?? null
                ]
            );
        }
    }

    /**
     * Delete rounds
     */
    private function deleteRounds(int $presetId): void
    {
        DatabaseService::execute("DELETE FROM preset_rounds WHERE preset_id = ?", [$presetId]);
    }

    /**
     * Delete preset and its rounds
     */
    public function deleteWithRounds(int $id): bool
    {
        $this->deleteRounds($id);
        return $this->delete($id);
    }
}
