<?php
/**
 * Get Leaderboard Scores
 * Returns top players, creators, or friends
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$type = $_POST['type'] ?? 'top'; // top, creators, relative, friends
$count = $_POST['count'] ?? 100;
$accountID = $_POST['accountID'] ?? 0;

switch ($type) {
    case 'top':
        $stmt = $db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             WHERE u.isBanned = 0
             ORDER BY u.stars DESC, u.demons DESC, u.userCoins DESC
             LIMIT ?"
        );
        $stmt->execute([$count]);
        break;
        
    case 'creators':
        $stmt = $db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             WHERE u.isBanned = 0 AND u.creatorPoints > 0
             ORDER BY u.creatorPoints DESC
             LIMIT ?"
        );
        $stmt->execute([$count]);
        break;
        
    case 'friends':
        $stmt = $db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             INNER JOIN friendships f ON (f.friendAccountID = u.accountID)
             WHERE f.accountID = ? AND u.isBanned = 0
             ORDER BY u.stars DESC
             LIMIT ?"
        );
        $stmt->execute([$accountID, $count]);
        break;
        
    default:
        exitWithError(-1);
}

$users = $stmt->fetchAll();

if (empty($users)) {
    exitWithError(-1);
}

// Build response
$userData = [];
$rank = 1;

foreach ($users as $user) {
    $data = [
        '1' => $user['userName'],
        '2' => $user['userID'],
        '3' => $user['stars'],
        '4' => $user['demons'],
        '6' => $rank,
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
        '21' => $user['accIcon'],
        '22' => $user['accShip'],
        '23' => $user['accBall'],
        '24' => $user['accBird'],
        '25' => $user['accDart'],
        '26' => $user['accRobot'],
        '28' => $user['accGlow'],
        '43' => $user['accSpider'],
        '48' => $user['accExplosion'],
        '53' => $user['accSwing'] ?? 1
    ];
    
    $userData[] = buildResponse($data, ':', '~');
    $rank++;
}

echo implode('|', $userData);
