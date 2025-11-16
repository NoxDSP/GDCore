<?php
/**
 * Download Message
 * Returns full message content
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
    "SELECT * FROM messages WHERE messageID = ? AND (toAccountID = ? OR accountID = ?)"
);
$stmt->execute([$messageID, $accountID, $accountID]);
$message = $stmt->fetch();

if (!$message) {
    exitWithError(-1);
}

// Mark as read
if ($message['toAccountID'] == $accountID) {
    $stmt = $db->prepare("UPDATE messages SET isNew = 0 WHERE messageID = ?");
    $stmt->execute([$messageID]);
}

// Build response
$data = [
    '1' => $message['messageID'],
    '2' => $message['accountID'],
    '3' => $message['toAccountID'],
    '4' => base64_encode($message['subject']),
    '5' => $message['userName'],
    '6' => 0,
    '7' => strtotime($message['uploadDate']),
    '8' => base64_encode($message['body']),
    '9' => 0
];

echo buildResponse($data, ':', '~');
