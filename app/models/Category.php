<?php
/**
 * Category Model
 * Handles CRUD operations for service categories
 */

class Category extends Model {

    /**
     * Get all active categories ordered by display_order
     */
    public function getAllActive(): array {
        try {
            $sql = "SELECT * FROM `service_categories` WHERE `is_active` = 1 ORDER BY `display_order` ASC, `name` ASC";
            return $this->fetchAll($sql);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get category by slug
     */
    public function getBySlug(string $slug): ?array {
        try {
            $sql = "SELECT * FROM `service_categories` WHERE `slug` = :slug LIMIT 1";
            return $this->fetchOne($sql, [':slug' => $slug]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Get category by ID
     */
    public function getById(int $id): ?array {
        try {
            $sql = "SELECT * FROM `service_categories` WHERE `id` = :id LIMIT 1";
            return $this->fetchOne($sql, [':id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Create new category
     */
    public function create(array $data): int {
        $sql = "INSERT INTO `service_categories` (`name`, `slug`, `icon`, `description`, `badge`, `display_order`, `is_active`)
                VALUES (:name, :slug, :icon, :description, :badge, :display_order, :is_active)";

        $this->query($sql, [
            ':name'          => trim($data['name']),
            ':slug'          => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            ':icon'          => $data['icon'] ?? 'sparkles',
            ':description'   => $data['description'] ?? null,
            ':badge'         => $data['badge'] ?? null,
            ':display_order' => (int)($data['display_order'] ?? 0),
            ':is_active'     => (int)($data['is_active'] ?? 1)
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Update category
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE `service_categories` SET 
                `name` = :name, 
                `description` = :description, 
                `badge` = :badge, 
                `is_active` = :is_active 
                WHERE `id` = :id";

        $stmt = $this->query($sql, [
            ':name'        => trim($data['name']),
            ':description' => $data['description'] ?? null,
            ':badge'       => $data['badge'] ?? null,
            ':is_active'   => (int)($data['is_active'] ?? 1),
            ':id'          => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete category
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM `service_categories` WHERE `id` = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
