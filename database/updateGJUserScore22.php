<?php
/**
 * Update User Score
 * Updates user stats and cosmetics
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

use GDCore\Utils\Hash;

$accountID = $_POST['accountID'] ?? 0;
$userName = $_POST['userName'] ?? '';
$stars = $_POST['stars'] ?? 0;
$demons = $_POST['demons'] ?? 0;
$icon = $_POST['icon'] ?? 1;
$color1 = $_POST['color1'] ?? 0;
$color2 = $_POST['color2'] ?? 3;
$iconType = $_POST['iconType'] ?? 0;
$coins = $_POST['coins'] ?? 0;
$userCoins = $_POST['userCoins'] ?? 0;
$special = $_POST['special'] ?? 0;
$accIcon = $_POST['accIcon'] ?? 1;
$accShip = $_POST['accShip'] ?? 1;
$accBall = $_POST['accBall'] ?? 1;
$accBird = $_POST['accBird'] ?? 1;
$accDart = $_POST['accDart'] ?? 1;
$accRobot = $_POST['accRobot'] ?? 1;
$accGlow = $_POST['accGlow'] ?? 0;
$accSpider = $_POST['accSpider'] ?? 1;
$accExplosion = $_POST['accExplosion'] ?? 1;
$accSwing = $_POST['accSwing'] ?? 1;
$diamonds = $_POST['diamonds'] ?? 0;

if (empty($userName)) {
    exitWithError(-1);
}

// Check if user exists
$stmt = $db->prepare("SELECT userID FROM users WHERE userName = ?");
$stmt->execute([$userName]);
$user = $stmt->fetch();

if ($user) {
    // Update existing user
    $stmt = $db->prepare(
        "UPDATE users SET 
        accountID = ?, stars = ?, demons = ?, icon = ?, color1 = ?, color2 = ?, 
        iconType = ?, coins = ?, userCoins = ?, special = ?, accIcon = ?, accShip = ?, 
        accBall = ?, accBird = ?, accDart = ?, accRobot = ?, accGlow = ?, accSpider = ?,
        accExplosion = ?, accSwing = ?, IP = ?
        WHERE userName = ?"
    );
    $stmt->execute([
        $accountID, $stars, $demons, $icon, $color1, $color2, $iconType, $coins, $userCoins,
        $special, $accIcon, $accShip, $accBall, $accBird, $accDart, $accRobot, $accGlow,
        $accSpider, $accExplosion, $accSwing, getIP(), $userName
    ]);
    $userID = $user['userID'];
} else {
    // Create new user
    $stmt = $db->prepare(
        "INSERT INTO users (
            accountID, userName, stars, demons, icon, color1, color2, iconType, coins, 
            userCoins, special, accIcon, accShip, accBall, accBird, accDart, accRobot, 
            accGlow, accSpider, accExplosion, accSwing, IP, isRegistered
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $accountID, $userName, $stars, $demons, $icon, $color1, $color2, $iconType, $coins,
        $userCoins, $special, $accIcon, $accShip, $accBall, $accBird, $accDart, $accRobot,
        $accGlow, $accSpider, $accExplosion, $accSwing, getIP(), ($accountID > 0 ? 1 : 0)
    ]);
    $userID = $db->lastInsertId();
}

echo $userID;
