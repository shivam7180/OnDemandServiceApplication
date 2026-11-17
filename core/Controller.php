<?php
/**
 * Base Controller Class
 * Provides common utilities for views, models, JSON responses, role authorization,
 * CSRF security, and input sanitation.
 */

abstract class Controller {

    /**
     * Load Model instance
     */
    protected function model(string $modelName) {
        $modelFile = ROOT_PATH . '/app/models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $modelName();
        }
        die("Model '$modelName' not found at $modelFile");
    }

    /**
     * Render View with Data
     */
    protected function view(string $viewName, array $data = []) {
        // Automatically inject CSRF token and current user into all views
        $data['csrfToken'] = $this->getCsrfToken();
        $data['currentUser'] = $this->currentUser();

        extract($data);

        $viewFile = ROOT_PATH . '/app/views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View '$viewName' not found at $viewFile");
        }
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirect to internal application URL
     */
    protected function redirect(string $path) {
        $cleanPath = ltrim($path, '/');
        header("Location: " . BASE_URL . '/' . $cleanPath);
        exit;
    }

    /**
     * Get current authenticated user session
     */
    protected function currentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Enforce authentication and role permissions
     */
    protected function requireAuth(array $allowedRoles = []): array {
        if (!isset($_SESSION['user'])) {
            $_SESSION['flash_error'] = "Please sign in to access this page.";
            $this->redirect('auth/login');
        }

        $user = $_SESSION['user'];
        if (!empty($allowedRoles) && !in_array($user['role'], $allowedRoles)) {
            $_SESSION['flash_error'] = "Unauthorized access for role: " . $user['role'];
            
            // Redirect to their respective dashboard
            if ($user['role'] === 'customer') {
                $this->redirect('customer/dashboard');
            } elseif ($user['role'] === 'provider') {
                $this->redirect('provider/dashboard');
            } elseif ($user['role'] === 'admin') {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('');
            }
        }

        return $user;
    }

    /**
     * Generate or fetch CSRF token
     */
    protected function getCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token from POST request
     */
    protected function validateCsrf(): bool {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['flash_error'] = "Invalid or expired security token. Please try again.";
            return false;
        }
        return true;
    }

    /**
     * Sanitize string input
     */
    protected function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
