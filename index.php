<?php
/**
 * Application Entry Point
 * Front Controller
 */

// Load Configuration and Core Dependencies
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// Initialize the Application
$app = new App();
