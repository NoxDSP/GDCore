<?php
/**
 * Backup Account (Load Cloud Save)
 * Downloads save data from server
 */

require_once dirname(__DIR__, 2) . '/config/connection.php';
require_once dirname(__DIR__, 2) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$stmt = $db->prepare("SELECT saveData FROM accounts WHERE accountID = ?");
$stmt->execute([$accountID]);
$account = $stmt->fetch();

if (!$account || empty($account['saveData'])) {
    exitWithError(-1);
}

echo $account['saveData'];
