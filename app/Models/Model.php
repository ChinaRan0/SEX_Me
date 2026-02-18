<?php

namespace App\Models;

use App\Services\DatabaseService;

/**
 * Base Model Class
 */
abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    /**
     * Get all records
     */
    public function all(string $orderBy = 'sort_order ASC, id ASC'): array
    {
        return DatabaseService::fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy}"
        );
    }

    /**
     * Get active records only
     */
    public function getActive(string $orderBy = 'sort_order ASC, id ASC'): array
    {
        return DatabaseService::fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY {$orderBy}"
        );
    }

    /**
     * Find by primary key
     */
    public function find(int $id): ?array
    {
        return DatabaseService::fetchOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        DatabaseService::execute(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );

        return (int) DatabaseService::lastInsertId();
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';

        return DatabaseService::execute(
            "UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?",
            [...array_values($data), $id]
        );
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        return DatabaseService::execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Soft delete (set is_active = 0)
     */
    public function softDelete(int $id): bool
    {
        return DatabaseService::execute(
            "UPDATE {$this->table} SET is_active = 0 WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Restore soft deleted record
     */
    public function restore(int $id): bool
    {
        return DatabaseService::execute(
            "UPDATE {$this->table} SET is_active = 1 WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        return DatabaseService::execute(
            "UPDATE {$this->table} SET is_active = NOT is_active WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
            $sql .= " WHERE {$where}";
            $params = array_values($conditions);
        }

        $result = DatabaseService::fetchOne($sql, $params);
        return (int) $result['count'];
    }

    /**
     * Filter data by fillable fields
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Get random record
     */
    public function getRandom(): ?array
    {
        return DatabaseService::fetchOne(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY RANDOM() LIMIT 1"
        );
    }

    /**
     * Get multiple random records
     */
    public function getRandomMultiple(int $count): array
    {
        return DatabaseService::fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY RANDOM() LIMIT {$count}"
        );
    }
}
