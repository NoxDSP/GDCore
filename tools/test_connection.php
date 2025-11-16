<?php
/**
 * Database Connection Test Tool
 * Tests if GDCore can connect to the database
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GDCore\Config\Config;
use GDCore\Config\Database;

echo "=== GDCore Database Connection Test ===\n\n";

try {
    // Load config
    $config = Config::getInstance();
    echo "✓ Configuration loaded\n";
    
    // Test database connection
    $db = Database::getInstance();
    echo "✓ Database connection established\n";
    
    // Test query
    $conn = $db->getConnection();
    $stmt = $conn->query("SELECT COUNT(*) as count FROM accounts");
    $result = $stmt->fetch();
    
    echo "✓ Database query successful\n";
    echo "\nDatabase stats:\n";
    echo "- Accounts: " . $result['count'] . "\n";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM levels");
    $result = $stmt->fetch();
    echo "- Levels: " . $result['count'] . "\n";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "- Users: " . $result['count'] . "\n";
    
    echo "\n✓ All tests passed! GDCore is ready.\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check your .env file exists and has correct database credentials\n";
    echo "2. Ensure MySQL/MariaDB is running\n";
    echo "3. Verify the database 'gdcore' exists\n";
    echo "4. Import database/schema.sql if you haven't already\n";
    exit(1);
}
