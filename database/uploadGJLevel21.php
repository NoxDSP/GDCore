<?php
/**
 * Upload Level (GD 2.1+)
 * Creates or updates a level
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

use GDCore\Utils\GDCrypto;

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$user = getUserByAccountID($db, $accountID);
if (!$user) {
    exitWithError(-1);
}

if (isBanned($db, $accountID)) {
    exitWithError(-1);
}

// Get level data
$levelID = $_POST['levelID'] ?? 0;
$levelName = $_POST['levelName'] ?? 'Unnamed';
$levelDesc = $_POST['levelDesc'] ?? '';
$levelVersion = $_POST['levelVersion'] ?? 1;
$levelLength = $_POST['levelLength'] ?? 0;
$audioTrack = $_POST['audioTrack'] ?? 0;
$password = $_POST['password'] ?? 0;
$original = $_POST['original'] ?? 0;
$twoPlayer = $_POST['twoPlayer'] ?? 0;
$songID = $_POST['songID'] ?? 0;
$objects = $_POST['objects'] ?? 0;
$coins = $_POST['coins'] ?? 0;
$requestedStars = $_POST['requestedStars'] ?? 0;
$unlisted = $_POST['unlisted'] ?? 0;
$ldm = $_POST['ldm'] ?? 0;
$levelString = $_POST['levelString'] ?? '';
$seed = $_POST['seed'] ?? '';
$seed2 = $_POST['seed2'] ?? '';
$gameVersion = $_POST['gameVersion'] ?? 1;
$binaryVersion = $_POST['binaryVersion'] ?? 0;
$extraString = $_POST['extraString'] ?? '';
$levelInfo = $_POST['levelInfo'] ?? '';

if (empty($levelString)) {
    exitWithError(-1);
}

// Check if updating existing level
if ($levelID > 0) {
    $stmt = $db->prepare("SELECT * FROM levels WHERE levelID = ? AND accountID = ?");
    $stmt->execute([$levelID, $accountID]);
    $existingLevel = $stmt->fetch();

    if (!$existingLevel) {
        exitWithError(-1);
    }

    // Update level
    $stmt = $db->prepare(
        "UPDATE levels SET 
        levelName = ?, levelDesc = ?, levelVersion = ?, length = ?, 
        twoPlayer = ?, songID = ?, objects = ?, coins = ?, 
        requestedStars = ?, unlisted = ?, isLDM = ?, levelString = ?,
        password = ?, originalLevel = ?, gameVersion = ?, updateDate = NOW()
        WHERE levelID = ? AND accountID = ?"
    );
    $stmt->execute([
        $levelName, $levelDesc, $levelVersion, $levelLength, $twoPlayer, $songID,
        $objects, $coins, $requestedStars, $unlisted, $ldm, $levelString,
        $password, $original, $gameVersion, $levelID, $accountID
    ]);

    echo $levelID;
} else {
    // Insert new level
    $stmt = $db->prepare(
        "INSERT INTO levels (
            levelName, levelDesc, levelVersion, userID, userName, accountID,
            length, twoPlayer, songID, objects, coins, requestedStars, 
            unlisted, isLDM, levelString, password, originalLevel, gameVersion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $levelName, $levelDesc, $levelVersion, $user['userID'], $user['userName'], $accountID,
        $levelLength, $twoPlayer, $songID, $objects, $coins, $requestedStars,
        $unlisted, $ldm, $levelString, $password, $original, $gameVersion
    ]);

    echo $db->lastInsertId();
}
