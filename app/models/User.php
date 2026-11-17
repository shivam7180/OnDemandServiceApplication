<?php
/**
 * User Model
 * Manages authentication, user records, password hashing, and role checks.
 */

class User extends Model {

    /**
     * Find user by email address
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM `users` WHERE `email` = :email LIMIT 1";
        return $this->fetchOne($sql, [':email' => strtolower(trim($email))]);
    }

    /**
     * Find user by primary ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM `users` WHERE `id` = :id LIMIT 1";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Check if email is already taken
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT `id` FROM `users` WHERE `email` = :email LIMIT 1";
        return (bool)$this->fetchOne($sql, [':email' => strtolower(trim($email))]);
    }

    /**
     * Register a new user with secure password hash
     */
    public function create(array $data): int {
        $sql = "INSERT INTO `users` (`full_name`, `email`, `phone`, `password_hash`, `role`, `status`) 
                VALUES (:full_name, :email, :phone, :password_hash, :role, :status)";

        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 10]);

        $this->query($sql, [
            ':full_name'     => trim($data['full_name']),
            ':email'         => strtolower(trim($data['email'])),
            ':phone'         => trim($data['phone']),
            ':password_hash' => $hash,
            ':role'          => $data['role'] ?? 'customer',
            ':status'        => $data['status'] ?? 'active'
        ]);

        return (int)$this->lastInsertId();
    }

    /**
     * Verify plain-text password against bcrypt hash
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Update user account status (active, inactive, suspended)
     */
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE `users` SET `status` = :status WHERE `id` = :id";
        $stmt = $this->query($sql, [':status' => $status, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
