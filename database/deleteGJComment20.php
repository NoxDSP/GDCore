<?php
/**
 * Delete Comment
 * Deletes a level or account comment
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$gjp = $_POST['gjp'] ?? '';

if (!checkAuth($db, $accountID, $gjp)) {
    exitWithError(-1);
}

$commentID = $_POST['commentID'] ?? 0;
$type = $_POST['type'] ?? 0; // 0 = level comment, 1 = account comment

if (!$commentID) {
    exitWithError(-1);
}

if ($type == 0) {
    $stmt = $db->prepare(
        "DELETE c FROM comments c
         INNER JOIN users u ON c.userID = u.userID
         WHERE c.commentID = ? AND u.accountID = ?"
    );
} else {
    $stmt = $db->prepare("DELETE FROM acccomments WHERE commentID = ? AND accountID = ?");
}

$stmt->execute([$commentID, $accountID]);

if ($stmt->rowCount() > 0) {
    echo "1";
} else {
    exitWithError(-1);
}
