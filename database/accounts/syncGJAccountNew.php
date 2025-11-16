<?php
/**
 * Sync Account (Cloud Save)
 * Upload save data if gameVersion >= 20
 * Download save data if gameVersion < 20 or no saveData param
 */

require_once dirname(__DIR__, 2) . '/config/connection.php';
require_once dirname(__DIR__, 2) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';
$saveData = $_POST['saveData'] ?? null;

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

if ($saveData !== null) {
    // Upload save data
    $stmt = $db->prepare("UPDATE accounts SET saveData = ? WHERE accountID = ?");
    $stmt->execute([$saveData, $accountID]);
    echo "1";
} else {
    // Download save data
    $stmt = $db->prepare("SELECT saveData FROM accounts WHERE accountID = ?");
    $stmt->execute([$accountID]);
    $account = $stmt->fetch();
    
    if (!$account || empty($account['saveData'])) {
        exitWithError(-1);
    }
    
    echo $account['saveData'];
}
