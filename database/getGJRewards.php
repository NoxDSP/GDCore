<?php
/**
 * Get Rewards
 * Returns daily/weekly rewards (chests)
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

use GDCore\Utils\GDCrypto;
use GDCore\Utils\Hash;

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';
$rewardType = $_POST['rewardType'] ?? 0; // 0 = daily, 1 = weekly

if ($accountID && $gjp) {
    if (!checkAuth($db, $accountID, $gjp)) {
        exitWithError(-1);
    }
}

// Generate random rewards
$mana = rand(1, 50);
$diamonds = rand(1, 20);
$shards = rand(1, 10);
$keys = rand(0, 5);

// Build reward string
$chk = GDCrypto::genRS();
$rewards = [
    $mana,
    $diamonds,
    $shards,
    $shards,
    $keys,
    $keys,
    $chk,
    time() + 86400 // 24 hours
];

echo implode(':', $rewards);
