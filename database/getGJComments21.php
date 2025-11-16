<?php
/**
 * Get Level Comments
 * Returns comments for a level
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$levelID = $_POST['levelID'] ?? 0;
$page = $_POST['page'] ?? 0;
$mode = $_POST['mode'] ?? 0; // 0 = recent, 1 = most liked
$count = $_POST['count'] ?? 20;

if (!$levelID) {
    exitWithError(-1);
}

$offset = $page * $count;
$orderBy = $mode == 1 ? 'likes DESC' : 'uploadDate DESC';

$stmt = $db->prepare(
    "SELECT c.*, u.* FROM comments c
     INNER JOIN users u ON c.userID = u.userID
     WHERE c.levelID = ?
     ORDER BY $orderBy
     LIMIT ? OFFSET ?"
);
$stmt->execute([$levelID, $count, $offset]);
$comments = $stmt->fetchAll();

if (empty($comments)) {
    exitWithError(-1);
}

// Build response
$commentData = [];
$userData = [];

foreach ($comments as $comment) {
    $data = [
        '2' => base64_encode($comment['comment']),
        '3' => $comment['userID'],
        '4' => $comment['likes'],
        '6' => $comment['commentID'],
        '7' => $comment['isSpam'] ?? 0,
        '9' => strtotime($comment['uploadDate']),
        '10' => $comment['percent'] ?? 0,
        '12' => 0 // modBadge
    ];
    $commentData[] = buildResponse($data, '~', '~');
    
    if (!isset($userData[$comment['userID']])) {
        $userStr = implode(':', [
            $comment['userID'],
            $comment['userName'],
            $comment['accountID']
        ]) . ':' . implode(':', [
            $comment['icon'],
            $comment['color1'],
            $comment['color2'],
            $comment['iconType'],
            $comment['accGlow'] ?? 0,
            $comment['accountID']
        ]);
        $userData[$comment['userID']] = $userStr;
    }
}

$response = implode('|', $commentData);
$response .= '#' . implode('|', $userData);
$response .= '#' . count($comments) . ':' . $offset . ':10';

echo $response;
