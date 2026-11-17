<?php
/**
 * Review Model
 * Manages customer reviews, ratings, and calculates provider aggregates
 */

class Review extends Model {

    /**
     * Get all reviews for a specific provider
     */
    public function getByProviderId(int $providerId): array {
        try {
            $sql = "SELECT r.*, u.full_name AS customer_name, u.avatar AS customer_avatar
                    FROM `reviews` r
                    JOIN `customers` c ON c.id = r.customer_id
                    JOIN `users` u ON u.id = c.user_id
                    WHERE r.provider_id = :pid
                    ORDER BY r.created_at DESC";
            return $this->fetchAll($sql, [':pid' => $providerId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Create a new review for a booking
     */
    public function create(array $data): int {
        return $this->transaction(function(PDO $db) use ($data) {
            $sql = "INSERT INTO `reviews` (`booking_id`, `customer_id`, `provider_id`, `rating`, `comment`)
                    VALUES (:booking_id, :customer_id, :provider_id, :rating, :comment)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':booking_id'  => (int)$data['booking_id'],
                ':customer_id' => (int)$data['customer_id'],
                ':provider_id' => (int)$data['provider_id'],
                ':rating'      => (int)$data['rating'],
                ':comment'     => trim($data['comment'] ?? '')
            ]);

            $reviewId = (int)$db->lastInsertId();

            // Recompute provider average rating and review count
            $avgSql = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_count 
                       FROM `reviews` WHERE `provider_id` = :pid";
            $avgStmt = $db->prepare($avgSql);
            $avgStmt->execute([':pid' => (int)$data['provider_id']]);
            $stats = $avgStmt->fetch();

            if ($stats) {
                $updSql = "UPDATE `providers` SET `rating_avg` = :avg, `reviews_count` = :count WHERE `id` = :pid";
                $updStmt = $db->prepare($updSql);
                $updStmt->execute([
                    ':avg'   => round((float)$stats['avg_rating'], 2),
                    ':count' => (int)$stats['total_count'],
                    ':pid'   => (int)$data['provider_id']
                ]);
            }

            return $reviewId;
        });
    }
}
