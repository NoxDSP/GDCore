<?php
/**
 * Login Account Endpoint
 * Authenticates user and returns accountID and userID
 */

require_once dirname(__DIR__, 2) . '/config/connection.php';
require_once dirname(__DIR__, 2) . '/config/security.php';

$userName = $_POST['userName'] ?? '';
$password = $_POST['password'] ?? '';
$udid = $_POST['udid'] ?? '';

if (empty($userName) || empty($password)) {
    exitWithError(-1);
}

// Get account
$stmt = $db->prepare("SELECT * FROM accounts WHERE userName = ?");
$stmt->execute([$userName]);
$account = $stmt->fetch();

if (!$account) {
    exitWithError(-1); // Account not found
}

// Verify password
if (!password_verify($password, $account['password'])) {
    exitWithError(-1); // Wrong password
}

// Check if banned
if (isBanned($db, $account['accountID'])) {
    exitWithError(-12); // Banned
}

// Update IP
$stmt = $db->prepare("UPDATE users SET IP = ? WHERE accountID = ?");
$stmt->execute([getIP(), $account['accountID']]);

// Get userID
$stmt = $db->prepare("SELECT userID FROM users WHERE accountID = ? LIMIT 1");
$stmt->execute([$account['accountID']]);
$user = $stmt->fetch();

echo $account['accountID'] . ',' . ($user['userID'] ?? 0);
