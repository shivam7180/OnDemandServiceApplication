<?php
/**
 * Notification Model
 * Manages user alerts, appointment milestone notifications, and read statuses
 */

class Notification extends Model {

    /**
     * Create notification for user
     */
    public function create(int $userId, string $title, string $message, string $type = 'booking', ?string $link = null): int {
        $sql = "INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`, `is_read`)
                VALUES (:user_id, :title, :message, :type, :link, 0)";

        $this->query($sql, [
            ':user_id' => $userId,
            ':title'   => trim($title),
            ':message' => trim($message),
            ':type'    => $type,
            ':link'    => $link
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Get recent notifications for a user
     */
    public function getByUserId(int $userId, int $limit = 8): array {
        try {
            $sql = "SELECT * FROM `notifications` WHERE `user_id` = :uid ORDER BY `created_at` DESC LIMIT " . (int)$limit;
            return $this->fetchAll($sql, [':uid' => $userId]);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Count unread notifications
     */
    public function getUnreadCount(int $userId): int {
        try {
            $sql = "SELECT COUNT(id) AS cnt FROM `notifications` WHERE `user_id` = :uid AND `is_read` = 0";
            $row = $this->fetchOne($sql, [':uid' => $userId]);
            return (int)($row['cnt'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(int $id, int $userId): bool {
        $sql = "UPDATE `notifications` SET `is_read` = 1 WHERE `id` = :id AND `user_id` = :uid";
        $stmt = $this->query($sql, [':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(int $userId): bool {
        $sql = "UPDATE `notifications` SET `is_read` = 1 WHERE `user_id` = :uid";
        $stmt = $this->query($sql, [':uid' => $userId]);
        return $stmt->rowCount() > 0;
    }
}
