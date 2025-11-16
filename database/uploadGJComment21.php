<?php
/**
 * Upload Level Comment
 * Posts a comment on a level
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$user = getUserByAccountID($db, $accountID);
if (!$user) {
    exitWithError(-1);
}

$levelID = $_POST['levelID'] ?? 0;
$comment = $_POST['comment'] ?? '';
$percent = $_POST['percent'] ?? 0;

if (!$levelID || empty($comment)) {
    exitWithError(-1);
}

// Decode comment (base64)
$decodedComment = base64_decode($comment);
if ($decodedComment === false) {
    exitWithError(-1);
}

// Insert comment
$stmt = $db->prepare(
    "INSERT INTO comments (levelID, userID, userName, comment, percent, uploadDate) 
     VALUES (?, ?, ?, ?, ?, NOW())"
);
$stmt->execute([$levelID, $user['userID'], $user['userName'], $decodedComment, $percent]);

echo $db->lastInsertId();
