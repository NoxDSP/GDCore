<?php
/**
 * Request User Access
 * Checks if user has mod permissions
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

// Check role
$stmt = $db->prepare("SELECT roleType FROM roles WHERE accountID = ? ORDER BY roleType DESC LIMIT 1");
$stmt->execute([$accountID]);
$role = $stmt->fetch();

if (!$role) {
    echo "0"; // No permissions
} else {
    // 1 = Mod, 2 = Admin/Elder Mod
    echo $role['roleType'];
}
