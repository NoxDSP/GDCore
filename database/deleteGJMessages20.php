<?php
/**
 * Delete Message
 * Deletes a message from inbox or sent
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$messageID = $_POST['messageID'] ?? 0;

if (!$messageID) {
    exitWithError(-1);
}

$stmt = $db->prepare(
    "DELETE FROM messages WHERE messageID = ? AND (toAccountID = ? OR accountID = ?)"
);
$stmt->execute([$messageID, $accountID, $accountID]);

if ($stmt->rowCount() > 0) {
    echo "1";
} else {
    exitWithError(-1);
}
