<?php
/**
 * Get Users (Search)
 * Search for users by name
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$str = $_POST['str'] ?? '';
$page = $_POST['page'] ?? 0;
$count = 10;
$offset = $page * $count;

if (empty($str)) {
    exitWithError(-1);
}

$stmt = $db->prepare(
    "SELECT u.*, a.* FROM users u
     INNER JOIN accounts a ON u.accountID = a.accountID
     WHERE u.userName LIKE ? AND u.isBanned = 0
     ORDER BY u.stars DESC
     LIMIT ? OFFSET ?"
);
$stmt->execute(['%' . $str . '%', $count, $offset]);
$users = $stmt->fetchAll();

if (empty($users)) {
    exitWithError(-1);
}

// Build response
$userData = [];

foreach ($users as $user) {
    $data = [
        '1' => $user['userName'],
        '2' => $user['userID'],
        '3' => $user['stars'],
        '4' => $user['demons'],
        '6' => 0, // rank
        '7' => $user['accountID'],
        '8' => $user['creatorPoints'],
        '9' => $user['icon'],
        '10' => $user['color1'],
        '11' => $user['color2'],
        '13' => $user['coins'],
        '14' => $user['iconType'],
        '15' => $user['special'],
        '16' => $user['accountID'],
        '17' => $user['userCoins']
    ];
    $userData[] = buildResponse($data, ':', '~');
}

$response = implode('|', $userData);
$response .= '#' . count($users) . ':' . $offset . ':10';

echo $response;
