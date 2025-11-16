<?php
/**
 * Register Account Endpoint
 * Creates a new user account
 */

require_once dirname(__DIR__, 2) . '/config/connection.php';
require_once dirname(__DIR__, 2) . '/config/security.php';

// Get POST data
$userName = $_POST['userName'] ?? '';
$password = $_POST['password'] ?? '';
$email = $_POST['email'] ?? '';

// Validation
if (strlen($userName) < 3 || strlen($userName) > 20) {
    exitWithError(-4); // Username too short/long
}

if (strlen($password) < 6) {
    exitWithError(-5); // Password too short
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exitWithError(-6); // Invalid email
}

// Check if username exists
$stmt = $db->prepare("SELECT accountID FROM accounts WHERE userName = ?");
$stmt->execute([$userName]);
if ($stmt->fetch()) {
    exitWithError(-2); // Username taken
}

// Check if email exists
$stmt = $db->prepare("SELECT accountID FROM accounts WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    exitWithError(-3); // Email taken
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert account
$stmt = $db->prepare("INSERT INTO accounts (userName, password, email, registerDate) VALUES (?, ?, ?, NOW())");
$stmt->execute([$userName, $hashedPassword, $email]);
$accountID = $db->lastInsertId();

// Create user stats entry
$stmt = $db->prepare("INSERT INTO users (accountID, userName, IP, isRegistered, registerDate) VALUES (?, ?, ?, 1, NOW())");
$stmt->execute([$accountID, $userName, getIP()]);

echo "1";
