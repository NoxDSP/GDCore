<?php
/**
 * Get Levels (Search)
 * Searches and returns levels based on filters
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

use GDCore\Utils\Hash;

$type = $_POST['type'] ?? 0;
$str = $_POST['str'] ?? '';
$page = $_POST['page'] ?? 0;
$diff = $_POST['diff'] ?? '-';
$len = $_POST['len'] ?? '-';
$featured = $_POST['featured'] ?? 0;
$original = $_POST['original'] ?? 0;
$twoPlayer = $_POST['twoPlayer'] ?? 0;
$coins = $_POST['coins'] ?? 0;
$epic = $_POST['epic'] ?? 0;
$star = $_POST['star'] ?? 0;
$noStar = $_POST['noStar'] ?? 0;
$song = $_POST['song'] ?? 0;
$customSong = $_POST['customSong'] ?? 0;

$perPage = 10;
$offset = $page * $perPage;

// Build query
$query = "SELECT * FROM levels WHERE 1=1";
$params = [];

// Search type
switch ($type) {
    case 0: // Search
        if (!empty($str)) {
            $query .= " AND levelName LIKE ?";
            $params[] = '%' . $str . '%';
        }
        break;
    case 1: // Most downloaded
        $query .= " ORDER BY downloads DESC";
        break;
    case 2: // Most liked
        $query .= " ORDER BY likes DESC";
        break;
    case 3: // Trending
        $query .= " AND uploadDate > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY likes DESC";
        break;
    case 4: // Recent
        $query .= " ORDER BY uploadDate DESC";
        break;
    case 5: // User levels
        if (!empty($str)) {
            $query .= " AND accountID = ?";
            $params[] = $str;
        }
        break;
    case 6: // Featured
        $query .= " AND featured = 1 ORDER BY rateDate DESC";
        break;
    case 7: // Magic
        $query .= " AND objects > 10000 ORDER BY likes DESC";
        break;
    case 11: // Awarded
        $query .= " AND stars > 0 ORDER BY rateDate DESC";
        break;
    case 16: // Hall of Fame (Epic)
        $query .= " AND epic = 1 ORDER BY rateDate DESC";
        break;
}

// Filters
if ($diff != '-') {
    $difficulties = explode(',', $diff);
    $placeholders = implode(',', array_fill(0, count($difficulties), '?'));
    $query .= " AND starDifficulty IN ($placeholders)";
    $params = array_merge($params, $difficulties);
}

if ($len != '-') {
    $lengths = explode(',', $len);
    $placeholders = implode(',', array_fill(0, count($lengths), '?'));
    $query .= " AND length IN ($placeholders)";
    $params = array_merge($params, $lengths);
}

if ($featured) {
    $query .= " AND featured = 1";
}

if ($epic) {
    $query .= " AND epic = 1";
}

if ($star) {
    $query .= " AND stars > 0";
}

if ($noStar) {
    $query .= " AND stars = 0";
}

if ($song) {
    $query .= " AND songID = ?";
    $params[] = $song;
}

if ($twoPlayer) {
    $query .= " AND twoPlayer = 1";
}

if ($coins) {
    $query .= " AND coins > 0";
}

// Hide unlisted
$query .= " AND unlisted = 0";

// Count total
$countQuery = str_replace('SELECT *', 'SELECT COUNT(*) as count', $query);
$stmt = $db->prepare($countQuery);
$stmt->execute($params);
$total = $stmt->fetch()['count'];

// Add pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;

$stmt = $db->prepare($query);
$stmt->execute($params);
$levels = $stmt->fetchAll();

if (empty($levels)) {
    exitWithError(-1);
}

// Build response
$levelData = [];
$userData = [];

foreach ($levels as $level) {
    $data = [
        '1' => $level['levelID'],
        '2' => $level['levelName'],
        '3' => base64_encode($level['levelDesc'] ?? ''),
        '5' => $level['levelVersion'],
        '6' => $level['userID'],
        '8' => 10,
        '9' => $level['starDifficulty'] ?? 0,
        '10' => $level['downloads'],
        '12' => $level['songID'],
        '13' => $level['gameVersion'],
        '14' => $level['likes'],
        '15' => $level['length'],
        '17' => $level['starDemon'] ?? 0,
        '18' => $level['stars'] ?? 0,
        '19' => $level['featured'] ?? 0,
        '25' => $level['starAuto'] ?? 0,
        '30' => $level['originalLevel'],
        '31' => $level['twoPlayer'],
        '35' => $level['songID'],
        '37' => $level['coins'],
        '38' => $level['starCoins'] ?? 0,
        '39' => $level['requestedStars'],
        '42' => $level['epic'] ?? 0,
        '45' => $level['objects'],
        '46' => 1,
        '47' => 2
    ];
    
    $levelData[] = buildResponse($data, ':', '~');
    
    if (!isset($userData[$level['accountID']])) {
        $userData[$level['accountID']] = [
            $level['userID'],
            $level['userName'],
            $level['accountID']
        ];
    }
}

$response = implode('|', $levelData);
$response .= '#' . implode('|', array_map(function($u) {
    return implode(':', $u);
}, $userData));
$response .= '#' . '' . '#'; // Songs
$response .= $total . ':' . $offset . ':' . $perPage;

// Add hash
$hashData = '';
foreach ($levels as $level) {
    $hashData .= $level['levelID'][0] . $level['levelID'][strlen($level['levelID']) - 1];
    $hashData .= $level['stars'];
    $hashData .= ($level['starCoins'] ?? 0) ? 1 : 0;
}
$hash = sha1($hashData . 'xI25fpAapCQg');
$response .= '#' . $hash;

echo $response;
