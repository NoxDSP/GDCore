<?php
/**
 * Remove Friend
 * Removes a friend from friend list
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$targetAccountID = $_POST['targetAccountID'] ?? 0;

if (!$targetAccountID) {
    exitWithError(-1);
}

$stmt = $db->prepare(
    "DELETE FROM friendships WHERE 
     (accountID = ? AND friendAccountID = ?) OR 
     (accountID = ? AND friendAccountID = ?)"
);
$stmt->execute([$accountID, $targetAccountID, $targetAccountID, $accountID]);

echo "1";
