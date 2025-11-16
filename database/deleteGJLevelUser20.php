<?php
/**
 * Delete Level
 * Deletes a level owned by the user
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';
$levelID = $_POST['levelID'] ?? 0;

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

if (!$levelID) {
    exitWithError(-1);
}

$stmt = $db->prepare("DELETE FROM levels WHERE levelID = ? AND accountID = ?");
$stmt->execute([$levelID, $accountID]);

if ($stmt->rowCount() > 0) {
    echo "1";
} else {
    exitWithError(-1);
}
