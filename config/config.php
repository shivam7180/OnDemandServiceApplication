<?php
/**
 * Application Global Configuration
 * On-Demand Service Platform
 */

// Environment settings
define('APP_ENV', 'development'); // 'development' or 'production'

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Application Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('CONFIG_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'config');
define('PUBLIC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'public');

// Base URL Detection (Dynamically adapts to XAMPP htdocs subdirectory or virtual host)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '.') {
    $scriptDir = '';
}
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');
$baseUrl = preg_replace('/\/public$/', '', $baseUrl);
define('BASE_URL', $baseUrl);

// Application Meta
define('APP_NAME', 'AuraCraft On-Demand');
define('APP_TAGLINE', 'Bespoke Home & Living Services');

// Database Credentials (Standard XAMPP default)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'ondemand_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Google Maps API Key placeholder
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY_HERE');

// Brand Colors (Reference for dynamic styles)
define('COLOR_WARM_IVORY', '#FAF8F3');
define('COLOR_FOREST_GREEN', '#234C3D');
define('COLOR_TERRACOTTA', '#D9795B');
define('COLOR_SOFT_SAGE', '#DCE8DA');
define('COLOR_CHARCOAL', '#292B27');
