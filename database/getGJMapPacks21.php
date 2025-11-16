<?php
/**
 * Get Map Packs
 * Returns list of map packs
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$page = $_POST['page'] ?? 0;
$count = 10;
$offset = $page * $count;

$stmt = $db->prepare("SELECT * FROM mappacks ORDER BY packID ASC LIMIT ? OFFSET ?");
$stmt->execute([$count, $offset]);
$packs = $stmt->fetchAll();

if (empty($packs)) {
    exitWithError(-1);
}

// Build response
$packData = [];

foreach ($packs as $pack) {
    $data = [
        '1' => $pack['packID'],
        '2' => $pack['name'],
        '3' => $pack['levels'],
        '4' => $pack['stars'],
        '5' => $pack['coins'],
        '6' => $pack['difficulty'],
        '7' => $pack['rgbcolors'] ?? '255,255,255',
        '8' => $pack['rgbcolors'] ?? '255,255,255'
    ];
    $packData[] = buildResponse($data, ':', '~');
}

$response = implode('|', $packData);
$response .= '#' . count($packs) . ':' . $offset . ':10';

// Add hash
$hash = sha1(implode('', array_column($packs, 'packID')) . 'xI25fpAapCQg');
$response .= '#' . $hash;

echo $response;
