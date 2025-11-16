<?php
/**
 * Upload Message
 * Sends a private message to another user
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$user = getUserByAccountID($db, $accountID);
if (!$user) {
    exitWithError(-1);
}

$toAccountID = $_POST['toAccountID'] ?? 0;
$subject = $_POST['subject'] ?? '';
$body = $_POST['body'] ?? '';

if (!$toAccountID || empty($subject) || empty($body)) {
    exitWithError(-1);
}

// Check message privacy
$stmt = $db->prepare("SELECT mS FROM accounts WHERE accountID = ?");
$stmt->execute([$toAccountID]);
$targetAccount = $stmt->fetch();

if (!$targetAccount) {
    exitWithError(-1);
}

if ($targetAccount['mS'] == 1) {
    // Check if friends
    $stmt = $db->prepare(
        "SELECT * FROM friendships WHERE accountID = ? AND friendAccountID = ?"
    );
    $stmt->execute([$toAccountID, $accountID]);
    if (!$stmt->fetch()) {
        exitWithError(-1); // Not friends
    }
} elseif ($targetAccount['mS'] == 2) {
    exitWithError(-1); // Messages disabled
}

// Insert message
$stmt = $db->prepare(
    "INSERT INTO messages (accountID, toAccountID, userName, subject, body, uploadDate) 
     VALUES (?, ?, ?, ?, ?, NOW())"
);
$stmt->execute([$accountID, $toAccountID, $user['userName'], $subject, $body]);

echo "1";
