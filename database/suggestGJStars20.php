<?php
/**
 * Suggest Stars (Mod Action)
 * Moderators can suggest star rating for levels
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

// Check if moderator
$stmt = $db->prepare("SELECT roleType FROM roles WHERE accountID = ? AND roleType >= 1");
$stmt->execute([$accountID]);
if (!$stmt->fetch()) {
    exitWithError(-1); // Not a moderator
}

$levelID = $_POST['levelID'] ?? 0;
$stars = $_POST['stars'] ?? 0;
$feature = $_POST['feature'] ?? 0;

if (!$levelID) {
    exitWithError(-1);
}

// Update level
$stmt = $db->prepare(
    "UPDATE levels SET starDifficulty = ?, featured = ?, rateDate = NOW() WHERE levelID = ?"
);
$stmt->execute([$stars, $feature, $levelID]);

// Log action
$stmt = $db->prepare(
    "INSERT INTO modactions (type, accountID, value, value2, timestamp) VALUES (1, ?, ?, ?, NOW())"
);
$stmt->execute([$accountID, $levelID, $stars]);

echo "1";
