<?php
/**
 * Security and Helper Functions
 * Shared security functions for all endpoints
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GDCore\Utils\GDCrypto;
use GDCore\Utils\Hash;
use GDCore\Utils\XORCipher;
use GDCore\Config\Config;

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy (adjust as needed)
$config = Config::getInstance();
if (!$config->get('DEBUG_MODE', false)) {
    header("Content-Security-Policy: default-src 'self'");
}

// Rate limiting check (basic implementation)
function checkRateLimit($identifier, $maxRequests = 100, $timeWindow = 60) {
    $cacheFile = sys_get_temp_dir() . '/gdcore_ratelimit_' . md5($identifier);
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        $now = time();
        
        // Clean old entries
        $data = array_filter($data, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        if (count($data) >= $maxRequests) {
            return false; // Rate limit exceeded
        }
        
        $data[] = $now;
    } else {
        $data = [time()];
    }
    
    file_put_contents($cacheFile, json_encode($data));
    return true;
}

/**
 * Get client IP address
 */
function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check if user is authenticated
 */
function checkAuth($db, $accountID, $gjp) {
    if (empty($accountID) || empty($gjp)) {
        return false;
    }

    $stmt = $db->prepare("SELECT password FROM accounts WHERE accountID = ?");
    $stmt->execute([$accountID]);
    $account = $stmt->fetch();

    if (!$account) {
        return false;
    }

    $gjpDecoded = GDCrypto::decodeGJP($gjp);
    return password_verify($gjpDecoded, $account['password']);
}

/**
 * Check if user is banned
 */
function isBanned($db, $accountID) {
    $stmt = $db->prepare(
        "SELECT * FROM bans WHERE accountID = ? AND (expireDate IS NULL OR expireDate > NOW())"
    );
    $stmt->execute([$accountID]);
    return $stmt->fetch() !== false;
}

/**
 * Get user by account ID
 */
function getUserByAccountID($db, $accountID) {
    $stmt = $db->prepare("SELECT * FROM users WHERE accountID = ? LIMIT 1");
    $stmt->execute([$accountID]);
    return $stmt->fetch();
}

/**
 * Exit with error code
 */
function exitWithError($code = -1) {
    echo $code;
    exit;
}

/**
 * Build GD response string
 */
function buildResponse($data, $separator = ':', $itemSeparator = '|') {
    $result = [];
    foreach ($data as $key => $value) {
        $result[] = $key . $separator . $value;
    }
    return implode($itemSeparator, $result);
}
