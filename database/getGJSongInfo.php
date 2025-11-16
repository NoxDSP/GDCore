<?php
/**
 * Get Song Info
 * Returns information about a custom song
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$songID = $_POST['songID'] ?? 0;

if (!$songID) {
    exitWithError(-1);
}

$stmt = $db->prepare("SELECT * FROM songs WHERE songID = ? AND isDisabled = 0 LIMIT 1");
$stmt->execute([$songID]);
$song = $stmt->fetch();

if (!$song) {
    exitWithError(-1);
}

// Build response
$data = [
    '1' => $song['songID'],
    '2' => $song['name'],
    '3' => $song['authorID'],
    '4' => $song['authorName'],
    '5' => $song['size'],
    '6' => '',
    '7' => '',
    '8' => '1',
    '10' => $song['download']
];

echo buildResponse($data, '~|~', '~|~');
