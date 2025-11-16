<?php
/**
 * Get Account Comments
 * Returns profile comments for a user
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$page = $_POST['page'] ?? 0;
$count = $_POST['count'] ?? 10;

if (!$accountID) {
    exitWithError(-1);
}

$offset = $page * $count;

$stmt = $db->prepare(
    "SELECT * FROM acccomments WHERE accountID = ? ORDER BY uploadDate DESC LIMIT ? OFFSET ?"
);
$stmt->execute([$accountID, $count, $offset]);
$comments = $stmt->fetchAll();

if (empty($comments)) {
    exitWithError(-1);
}

// Build response
$commentData = [];

foreach ($comments as $comment) {
    $data = [
        '2' => base64_encode($comment['comment']),
        '4' => $comment['likes'],
        '6' => $comment['commentID'],
        '9' => strtotime($comment['uploadDate'])
    ];
    $commentData[] = buildResponse($data, '~', '~');
}

$response = implode('|', $commentData);
$response .= '#' . count($comments) . ':' . $offset . ':10';

echo $response;
