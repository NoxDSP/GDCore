<?php
/**
 * Get Friend Requests
 * Returns pending friend requests
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$page = $_POST['page'] ?? 0;
$count = 10;
$offset = $page * $count;

$stmt = $db->prepare(
    "SELECT fr.*, u.userName, u.icon, u.color1, u.color2, u.iconType, u.accGlow 
     FROM friendreqs fr
     INNER JOIN accounts a ON fr.accountID = a.accountID
     INNER JOIN users u ON a.accountID = u.accountID
     WHERE fr.toAccountID = ?
     ORDER BY fr.uploadDate DESC
     LIMIT ? OFFSET ?"
);
$stmt->execute([$accountID, $count, $offset]);
$requests = $stmt->fetchAll();

if (empty($requests)) {
    exitWithError(-2);
}

// Build response
$requestData = [];

foreach ($requests as $request) {
    $data = [
        '1' => $request['userName'],
        '2' => $request['accountID'],
        '9' => $request['requestID'],
        '10' => $request['icon'],
        '11' => $request['color1'],
        '12' => $request['color2'],
        '13' => $request['iconType'],
        '14' => $request['accGlow'] ?? 0,
        '32' => $request['accountID'],
        '35' => base64_encode($request['comment'] ?? ''),
        '37' => strtotime($request['uploadDate']),
        '41' => $request['isNew'] ?? 1
    ];
    $requestData[] = buildResponse($data, ':', '~');
}

$response = implode('|', $requestData);
$response .= '#' . count($requests) . ':' . $offset . ':10';

echo $response;
