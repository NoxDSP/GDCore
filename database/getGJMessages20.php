<?php
/**
 * Get Messages
 * Returns inbox or sent messages
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$page = $_POST['page'] ?? 0;
$getSent = $_POST['getSent'] ?? 0;
$count = 10;
$offset = $page * $count;

if ($getSent == 1) {
    $whereClause = "accountID = ?";
} else {
    $whereClause = "toAccountID = ?";
}

$stmt = $db->prepare(
    "SELECT * FROM messages WHERE $whereClause ORDER BY uploadDate DESC LIMIT ? OFFSET ?"
);
$stmt->execute([$accountID, $count, $offset]);
$messages = $stmt->fetchAll();

if (empty($messages)) {
    exitWithError(-2);
}

// Build response
$messageData = [];

foreach ($messages as $message) {
    $data = [
        '1' => $message['messageID'],
        '2' => $getSent == 1 ? $message['toAccountID'] : $message['accountID'],
        '3' => $getSent == 1 ? $message['toAccountID'] : $message['accountID'],
        '4' => base64_encode($message['subject']),
        '5' => $message['userName'],
        '6' => 0, // userID
        '7' => strtotime($message['uploadDate']),
        '8' => $message['isNew'] ?? 1,
        '9' => $getSent
    ];
    $messageData[] = buildResponse($data, ':', '~');
}

$response = implode('|', $messageData);
$response .= '#' . count($messages) . ':' . $offset . ':10';

echo $response;
