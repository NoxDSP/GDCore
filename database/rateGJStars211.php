<?php
/**
 * Rate Level Stars
 * User rates a level (suggests star rating)
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$levelID = $_POST['levelID'] ?? 0;
$stars = $_POST['stars'] ?? 0;

if (!$levelID) {
    exitWithError(-1);
}

$user = getUserByAccountID($db, $accountID);

// Check if already rated
$stmt = $db->prepare("SELECT * FROM levelscores WHERE levelID = ? AND accountID = ?");
$stmt->execute([$levelID, $accountID]);

if ($stmt->fetch()) {
    // Update existing rating
    $stmt = $db->prepare("UPDATE levelscores SET stars = ? WHERE levelID = ? AND accountID = ?");
    $stmt->execute([$stars, $levelID, $accountID]);
} else {
    // Insert new rating
    $stmt = $db->prepare(
        "INSERT INTO levelscores (levelID, userID, accountID, stars) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$levelID, $user['userID'], $accountID, $stars]);
}

echo "1";
