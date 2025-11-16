<?php
/**
 * Like Item
 * Likes/dislikes levels, comments, or list
 */

require_once dirname(__DIR__) . '/config/connection.php';
require_once dirname(__DIR__) . '/config/security.php';

$accountID = $_POST['accountID'] ?? 0;
$itemID = $_POST['itemID'] ?? 0;
$like = $_POST['like'] ?? 1; // 1 for like, 0 for dislike
$type = $_POST['type'] ?? 1; // 1 = level, 2 = comment, 3 = account comment

if (!$itemID) {
    exitWithError(-1);
}

$change = $like == 1 ? 1 : -1;

switch ($type) {
    case 1: // Level
        $stmt = $db->prepare("UPDATE levels SET likes = likes + ? WHERE levelID = ?");
        $stmt->execute([$change, $itemID]);
        break;
    case 2: // Level comment
        $stmt = $db->prepare("UPDATE comments SET likes = likes + ? WHERE commentID = ?");
        $stmt->execute([$change, $itemID]);
        break;
    case 3: // Account comment
        $stmt = $db->prepare("UPDATE acccomments SET likes = likes + ? WHERE commentID = ?");
        $stmt->execute([$change, $itemID]);
        break;
}

echo "1";
