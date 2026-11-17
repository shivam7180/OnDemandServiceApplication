<?php
/**
 * Base Model Class
 * Provides direct access to PDO and prepared statement helpers
 */

require_once ROOT_PATH . '/config/Database.php';

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Execute a prepared SQL statement with parameters
     */
    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all matching records
     */
    protected function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single matching record
     */
    protected function fetchOne(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Get last inserted ID
     */
    protected function lastInsertId(): string {
        return $this->db->lastInsertId();
    }

    /**
     * Execute transactional operations safely
     */
    protected function transaction(callable $callback) {
        $this->db->beginTransaction();
        try {
            $result = $callback($this->db);
            $this->db->commit();
            return $result;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
