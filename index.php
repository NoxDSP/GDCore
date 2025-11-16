<?php
/**
 * GDCore - Geometry Dash Private Server
 * Main entry point - redirects to database endpoints
 */

require_once __DIR__ . '/vendor/autoload.php';

use GDCore\Config\Config;

// Set error reporting based on debug mode
$config = Config::getInstance();
if ($config->get('DEBUG_MODE', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set('UTC');

// Simple message for root access
echo "GDCore - Geometry Dash Private Server\n";
echo "Server is running!\n";
echo "\nEndpoints are located at: /database/\n";
echo "Example: /database/accounts/loginGJAccount.php\n";
