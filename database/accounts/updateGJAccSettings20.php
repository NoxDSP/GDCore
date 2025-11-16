<?php
/**
 * Update Account Settings
 * Updates privacy settings and social links
 */

require_once dirname(__DIR__, 2) . '/config/connection.php';
require_once dirname(__DIR__, 2) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$mS = $_POST['mS'] ?? 0;      // Message privacy: 0=all, 1=friends, 2=none
$frS = $_POST['frS'] ?? 0;    // Friend request privacy: 0=all, 1=none
$cS = $_POST['cS'] ?? 0;      // Comment privacy: 0=all, 1=friends, 2=none
$youtube = $_POST['yt'] ?? '';
$twitter = $_POST['twitter'] ?? '';
$twitch = $_POST['twitch'] ?? '';

$stmt = $db->prepare(
    "UPDATE accounts SET mS = ?, frS = ?, cS = ?, youtubeurl = ?, twitter = ?, twitch = ? WHERE accountID = ?"
);
$stmt->execute([$mS, $frS, $cS, $youtube, $twitter, $twitch, $accountID]);

echo "1";
