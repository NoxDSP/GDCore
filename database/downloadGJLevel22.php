<?php
/**
 * Download Level (GD 2.2)
 * Returns level data for playing
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

use GDCore\Utils\Hash;

$levelID = $_POST['levelID'] ?? 0;
$inc = $_POST['inc'] ?? 0;

if (!$levelID) {
    exitWithError(-1);
}

$stmt = $db->prepare("SELECT * FROM levels WHERE levelID = ? LIMIT 1");
$stmt->execute([$levelID]);
$level = $stmt->fetch();

if (!$level) {
    exitWithError(-1);
}

// Increment downloads
if ($inc == 1) {
    $stmt = $db->prepare("UPDATE levels SET downloads = downloads + 1 WHERE levelID = ?");
    $stmt->execute([$levelID]);
}

// Build response
$response = [
    '1' => $level['levelID'],
    '2' => $level['levelName'],
    '3' => base64_encode($level['levelDesc'] ?? ''),
    '4' => $level['levelString'],
    '5' => $level['levelVersion'],
    '6' => $level['userID'],
    '8' => $level['starDifficulty'] ?? 0,
    '9' => $level['starDifficulty'] ?? 0,
    '10' => $level['downloads'],
    '11' => $level['starDifficulty'] ?? 0,
    '12' => $level['songID'],
    '13' => $level['gameVersion'],
    '14' => $level['likes'],
    '15' => $level['length'],
    '17' => $level['starDemon'] ?? 0,
    '18' => $level['stars'] ?? 0,
    '19' => $level['featured'] ?? 0,
    '25' => $level['starAuto'] ?? 0,
    '27' => $level['password'] == 0 ? 0 : 1,
    '28' => strtotime($level['uploadDate']),
    '29' => strtotime($level['updateDate']),
    '30' => $level['originalLevel'],
    '31' => $level['twoPlayer'],
    '35' => $level['songID'],
    '36' => '',
    '37' => $level['coins'],
    '38' => $level['starCoins'] ?? 0,
    '39' => $level['requestedStars'],
    '40' => $level['isLDM'] ?? 0,
    '42' => $level['epic'] ?? 0,
    '43' => $level['starDemonDiff'] ?? 0,
    '45' => $level['objects'],
    '46' => 1,
    '47' => 2,
    '52' => 0,
    '53' => 0,
    '57' => 0
];

$responseStr = buildResponse($response, ':', '~');

// Add hash
$hash = Hash::genSolo($level['levelString']);
$responseStr .= '#' . $hash;

// Add user data
$userData = [
    '1~|~' . $level['userName'],
    '2~|~' . $level['userID'],
    '3~|~' . $level['accountID']
];
$responseStr .= '#' . implode(':', $userData) . '#';

// Add hash2
$hash2 = Hash::genSolo2($level['levelString']);
$responseStr .= $hash2;

echo $responseStr;
