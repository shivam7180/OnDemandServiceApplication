<?php
/**
 * Application Front Controller Router
 * Dispatches requests to matching Controller and Action.
 * Supports both mod_rewrite clean URLs and standard query parameters.
 */

class App {
    protected string $controller = 'HomeController';
    protected string $method = 'index';
    protected array $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Resolve Controller
        if (!empty($url[0])) {
            $candidate = ucfirst($url[0]) . 'Controller';
            if (file_exists(ROOT_PATH . '/app/controllers/' . $candidate . '.php')) {
                $this->controller = $candidate;
                unset($url[0]);
            }
        }

        require_once ROOT_PATH . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Resolve Action/Method
        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Resolve Parameters
        $this->params = $url ? array_values($url) : [];

        // 4. Dispatch
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse query parameter or REQUEST_URI
     */
    private function parseUrl(): array {
        if (isset($_GET['url']) && !empty($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = str_replace('\\', '/', dirname($scriptName));

        if ($baseDir !== '/' && $baseDir !== '.' && !empty($baseDir) && strpos($requestUri, $baseDir) === 0) {
            $path = substr($requestUri, strlen($baseDir));
        } else {
            $path = $requestUri;
        }

        $path = strtok($path, '?'); // strip query strings
        $path = trim($path, '/');

        return !empty($path) ? explode('/', filter_var($path, FILTER_SANITIZE_URL)) : [];
    }
}
