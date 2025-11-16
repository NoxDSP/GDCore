<?php
/**
 * Upload Friend Request
 * Sends a friend request to another user
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$toAccountID = $_POST['toAccountID'] ?? 0;
$comment = $_POST['comment'] ?? '';

if (!$toAccountID) {
    exitWithError(-1);
}

// Check if already friends
$stmt = $db->prepare(
    "SELECT * FROM friendships WHERE 
     (accountID = ? AND friendAccountID = ?) OR 
     (accountID = ? AND friendAccountID = ?)"
);
$stmt->execute([$accountID, $toAccountID, $toAccountID, $accountID]);
if ($stmt->fetch()) {
    exitWithError(-1); // Already friends
}

// Check if request already sent
$stmt = $db->prepare(
    "SELECT * FROM friendreqs WHERE accountID = ? AND toAccountID = ?"
);
$stmt->execute([$accountID, $toAccountID]);
if ($stmt->fetch()) {
    exitWithError(-1); // Request already sent
}

// Check friend request privacy
$stmt = $db->prepare("SELECT frS FROM accounts WHERE accountID = ?");
$stmt->execute([$toAccountID]);
$target = $stmt->fetch();

if ($target && $target['frS'] == 1) {
    exitWithError(-1); // Friend requests disabled
}

// Insert friend request
$stmt = $db->prepare(
    "INSERT INTO friendreqs (accountID, toAccountID, comment, uploadDate) 
     VALUES (?, ?, ?, NOW())"
);
$stmt->execute([$accountID, $toAccountID, $comment]);

echo "1";
