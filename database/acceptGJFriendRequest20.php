<?php
/**
 * Accept Friend Request
 * Accepts a friend request and creates friendship
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$requestID = $_POST['requestID'] ?? 0;
$targetAccountID = $_POST['targetAccountID'] ?? 0;

if (!$requestID || !$targetAccountID) {
    exitWithError(-1);
}

// Delete friend request
$stmt = $db->prepare(
    "DELETE FROM friendreqs WHERE requestID = ? AND toAccountID = ? AND accountID = ?"
);
$stmt->execute([$requestID, $accountID, $targetAccountID]);

if ($stmt->rowCount() == 0) {
    exitWithError(-1);
}

// Create friendship (both ways)
$stmt = $db->prepare(
    "INSERT INTO friendships (accountID, friendAccountID, uploadDate) VALUES (?, ?, NOW())"
);
$stmt->execute([$accountID, $targetAccountID]);
$stmt->execute([$targetAccountID, $accountID]);

echo "1";
