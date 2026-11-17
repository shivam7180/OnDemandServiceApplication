<?php
/**
 * Customer Model
 * Manages customer profile data and coordinates with user records
 */

class Customer extends Model {

    /**
     * Create customer profile record linked to user_id
     */
    public function create(int $userId, array $details = []): int {
        $sql = "INSERT INTO `customers` (`user_id`, `address`, `city`, `state`, `postal_code`, `latitude`, `longitude`)
                VALUES (:user_id, :address, :city, :state, :postal_code, :latitude, :longitude)";

        $this->query($sql, [
            ':user_id'     => $userId,
            ':address'     => $details['address'] ?? null,
            ':city'        => $details['city'] ?? 'San Francisco',
            ':state'       => $details['state'] ?? 'CA',
            ':postal_code' => $details['postal_code'] ?? null,
            ':latitude'    => $details['latitude'] ?? 37.774929,
            ':longitude'   => $details['longitude'] ?? -122.419416
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Get customer profile by user_id including user info
     */
    public function getByUserId(int $userId): ?array {
        $sql = "SELECT c.*, u.full_name, u.email, u.phone, u.status 
                FROM `customers` c
                JOIN `users` u ON u.id = c.user_id
                WHERE c.user_id = :user_id LIMIT 1";
        return $this->fetchOne($sql, [':user_id' => $userId]);
    }

    /**
     * Update customer address & coordinates
     */
    public function updateAddress(int $userId, array $data): bool {
        $sql = "UPDATE `customers` SET 
                `address` = :address, 
                `city` = :city, 
                `state` = :state, 
                `postal_code` = :postal_code,
                `latitude` = :lat,
                `longitude` = :lng
                WHERE `user_id` = :user_id";

        $stmt = $this->query($sql, [
            ':address'     => $data['address'],
            ':city'        => $data['city'] ?? 'San Francisco',
            ':state'       => $data['state'] ?? 'CA',
            ':postal_code' => $data['postal_code'] ?? null,
            ':lat'         => $data['latitude'] ?? null,
            ':lng'         => $data['longitude'] ?? null,
            ':user_id'     => $userId
        ]);

        return $stmt->rowCount() > 0;
    }
}
