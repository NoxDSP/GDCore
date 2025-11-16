<?php
/**
 * Get User Info
 * Returns detailed user information
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$targetAccountID = $_POST['targetAccountID'] ?? 0;

if (!$targetAccountID) {
    exitWithError(-1);
}

// Get account and user data
$stmt = $db->prepare(
    "SELECT a.*, u.* FROM accounts a 
     INNER JOIN users u ON a.accountID = u.accountID 
     WHERE a.accountID = ? LIMIT 1"
);
$stmt->execute([$targetAccountID]);
$user = $stmt->fetch();

if (!$user) {
    exitWithError(-1);
}

// Build response
$response = [
    '1' => $user['userName'],
    '2' => $user['userID'],
    '3' => $user['stars'],
    '4' => $user['demons'],
    '6' => $user['userID'],
    '7' => $user['accountID'],
    '8' => $user['creatorPoints'],
    '9' => $user['icon'],
    '10' => $user['color1'],
    '11' => $user['color2'],
    '13' => $user['coins'],
    '14' => $user['iconType'],
    '15' => $user['special'],
    '16' => $user['accountID'],
    '17' => $user['userCoins'],
    '18' => $user['mS'] ?? 0,
    '19' => $user['frS'] ?? 0,
    '20' => $user['youtubeurl'] ?? '',
    '21' => $user['accIcon'],
    '22' => $user['accShip'],
    '23' => $user['accBall'],
    '24' => $user['accBird'],
    '25' => $user['accDart'],
    '26' => $user['accRobot'],
    '27' => 0,
    '28' => $user['accGlow'],
    '29' => 1,
    '30' => 0, // rank
    '43' => $user['accSpider'],
    '44' => $user['twitter'] ?? '',
    '45' => $user['twitch'] ?? '',
    '46' => 0, // diamonds
    '48' => $user['accExplosion'],
    '49' => 0, // modLevel
    '50' => $user['cS'] ?? 0,
    '53' => $user['accSwing'] ?? 1
];

echo buildResponse($response, ':', '~');
