<?php
/**
 * Get Gauntlets
 * Returns list of gauntlets
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$stmt = $db->query("SELECT * FROM gauntlets ORDER BY gauntletID ASC");
$gauntlets = $stmt->fetchAll();

if (empty($gauntlets)) {
    exitWithError(-1);
}

// Build response
$gauntletData = [];

foreach ($gauntlets as $gauntlet) {
    $data = [
        '1' => $gauntlet['gauntletID'],
        '3' => implode(',', [
            $gauntlet['level1'],
            $gauntlet['level2'],
            $gauntlet['level3'],
            $gauntlet['level4'],
            $gauntlet['level5']
        ])
    ];
    $gauntletData[] = buildResponse($data, ':', '~');
}

$response = implode('|', $gauntletData);

// Add hash
$hash = sha1(implode('', array_column($gauntlets, 'gauntletID')) . 'xI25fpAapCQg');
$response .= '#' . $hash;

echo $response;
