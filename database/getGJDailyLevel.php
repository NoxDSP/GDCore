<?php
/**
 * Get Daily Level
 * Returns the current daily or weekly level
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$weekly = $_POST['weekly'] ?? 0;
$type = $weekly == 1 ? 1 : 0; // 0 = daily, 1 = weekly

// Get latest daily/weekly
$stmt = $db->prepare(
    "SELECT levelID, DATEDIFF(NOW(), timestamp) as timediff 
     FROM dailylevels 
     WHERE type = ? 
     ORDER BY timestamp DESC 
     LIMIT 1"
);
$stmt->execute([$type]);
$daily = $stmt->fetch();

if (!$daily) {
    exitWithError(-1);
}

// Calculate time left (in seconds)
$daysPassed = $daily['timediff'];
$period = $weekly == 1 ? 7 : 1; // 7 days for weekly, 1 for daily
$timeLeft = ($period - $daysPassed) * 86400;

if ($timeLeft < 0) {
    $timeLeft = 0;
}

echo $daily['levelID'] . '|' . $timeLeft;
