<?php
/**
 * Database Connection
 * Shared connection file for all endpoints
 */

// Load environment configuration
require_once dirname(__DIR__) . '/vendor/autoload.php';

use GDCore\Config\Config;

$config = Config::getInstance();

try {
    $db = new PDO(
        sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $config->get('DB_HOST', 'localhost'),
            $config->get('DB_PORT', '3306'),
            $config->get('DB_NAME', 'gdcore')
        ),
        $config->get('DB_USER', 'root'),
        $config->get('DB_PASS', ''),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
    
    // Connection security settings
    $db->exec("SET SESSION sql_mode='STRICT_ALL_TABLES'");
    
} catch (PDOException $e) {
    // Log error securely (don't expose details to client)
    error_log('Database connection failed: ' . $e->getMessage());
    
    if ($config->get('DEBUG_MODE', false)) {
        die("Connection failed: " . $e->getMessage());
    }
    die("-1");
}
