<?php
/**
 * Upload Account Comment
 * Posts a comment on user's profile
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

$comment = $_POST['comment'] ?? '';

if (empty($comment)) {
    exitWithError(-1);
}

// Decode comment (base64)
$decodedComment = base64_decode($comment);
if ($decodedComment === false) {
    exitWithError(-1);
}

// Insert comment
$stmt = $db->prepare(
    "INSERT INTO acccomments (accountID, userName, comment, uploadDate) 
     VALUES (?, ?, ?, NOW())"
);
$stmt->execute([$accountID, $user['userName'], $decodedComment]);

echo $db->lastInsertId();
