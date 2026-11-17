<?php
/**
 * Service Model
 * Handles CRUD operations for individual services and relationships to categories
 */

class Service extends Model {

    /**
     * Get all active services with optional category slug & keyword filter
     */
    public function getAll(array $filters = []): array {
        try {
            $sql = "SELECT s.*, c.name AS category_name, c.slug AS category_slug, c.badge AS category_badge
                    FROM `services` s
                    JOIN `service_categories` c ON c.id = s.category_id
                    WHERE s.is_active = 1";
            $params = [];

            if (!empty($filters['category']) && $filters['category'] !== 'all') {
                $sql .= " AND c.slug = :cat";
                $params[':cat'] = $filters['category'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (s.name LIKE :search OR s.description LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY s.id ASC";

            return $this->fetchAll($sql, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get service by primary ID
     */
    public function getById(int $id): ?array {
        try {
            $sql = "SELECT s.*, c.name AS category_name, c.slug AS category_slug
                    FROM `services` s
                    JOIN `service_categories` c ON c.id = s.category_id
                    WHERE s.id = :id LIMIT 1";
            return $this->fetchOne($sql, [':id' => $id]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Get service by unique slug
     */
    public function getBySlug(string $slug): ?array {
        try {
            $sql = "SELECT s.*, c.name AS category_name, c.slug AS category_slug
                    FROM `services` s
                    JOIN `service_categories` c ON c.id = s.category_id
                    WHERE s.slug = :slug LIMIT 1";
            return $this->fetchOne($sql, [':slug' => $slug]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Create new service
     */
    public function create(array $data): int {
        $sql = "INSERT INTO `services` (`category_id`, `name`, `slug`, `description`, `base_price`, `duration_mins`, `image`, `is_active`)
                VALUES (:category_id, :name, :slug, :description, :base_price, :duration_mins, :image, :is_active)";

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));

        $this->query($sql, [
            ':category_id'   => (int)$data['category_id'],
            ':name'          => trim($data['name']),
            ':slug'          => $slug,
            ':description'   => $data['description'] ?? null,
            ':base_price'    => (float)($data['base_price'] ?? 0.00),
            ':duration_mins' => (int)($data['duration_mins'] ?? 60),
            ':image'         => $data['image'] ?? null,
            ':is_active'     => (int)($data['is_active'] ?? 1)
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Update existing service
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE `services` SET 
                `category_id` = :category_id,
                `name` = :name,
                `description` = :description,
                `base_price` = :base_price,
                `duration_mins` = :duration_mins,
                `is_active` = :is_active
                WHERE `id` = :id";

        $stmt = $this->query($sql, [
            ':category_id'   => (int)$data['category_id'],
            ':name'          => trim($data['name']),
            ':description'   => $data['description'] ?? null,
            ':base_price'    => (float)($data['base_price'] ?? 0.00),
            ':duration_mins' => (int)($data['duration_mins'] ?? 60),
            ':is_active'     => (int)($data['is_active'] ?? 1),
            ':id'            => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete service
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM `services` WHERE `id` = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
