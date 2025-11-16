<?php
namespace GDCore\Controllers;

use GDCore\Utils\Response;
use GDCore\Utils\Hash;

/**
 * Comment Management Controller
 * Handles level comments and profile comments
 */
class CommentController extends BaseController {
    
    /**
     * Upload level comment
     * Endpoint: /database/uploadGJComment21.php
     */
    public function uploadLevelComment(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $user = $this->getUserByAccountID($accountID);
        if (!$user) {
            Response::error(-1);
        }

        $levelID = $this->requirePost('levelID');
        $comment = $this->requirePost('comment');
        $percent = $this->getPost('percent', 0);

        // Decode comment (base64)
        $decodedComment = base64_decode($comment);
        if ($decodedComment === false) {
            Response::error(-1);
        }

        // Insert comment
        $stmt = $this->db->prepare(
            "INSERT INTO comments (levelID, userID, userName, comment, percent, uploadDate) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$levelID, $user['userID'], $user['userName'], $decodedComment, $percent]);

        Response::send($this->db->lastInsertId());
    }

    /**
     * Get level comments
     * Endpoint: /database/getGJComments21.php
     */
    public function getLevelComments(): void {
        $levelID = $this->requirePost('levelID');
        $page = $this->getPost('page', 0);
        $mode = $this->getPost('mode', 0); // 0 = recent, 1 = most liked
        $count = $this->getPost('count', 20);

        $offset = $page * $count;

        $orderBy = $mode == 1 ? 'likes DESC' : 'uploadDate DESC';

        $stmt = $this->db->prepare(
            "SELECT c.*, u.* FROM comments c
             INNER JOIN users u ON c.userID = u.userID
             WHERE c.levelID = ?
             ORDER BY $orderBy
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$levelID, $count, $offset]);
        $comments = $stmt->fetchAll();

        if (empty($comments)) {
            Response::error(-1);
        }

        // Build response
        $commentData = [];
        $userData = [];
        
        foreach ($comments as $comment) {
            $commentData[] = $this->buildCommentData($comment);
            
            if (!isset($userData[$comment['userID']])) {
                $userData[$comment['userID']] = $this->buildUserData($comment);
            }
        }

        $response = implode('|', $commentData);
        $response .= '#' . implode('|', $userData);
        $response .= '#' . count($comments) . ':' . $offset . ':10';

        Response::send($response);
    }

    /**
     * Upload account comment
     * Endpoint: /database/uploadGJAccComment20.php
     */
    public function uploadAccountComment(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $user = $this->getUserByAccountID($accountID);
        if (!$user) {
            Response::error(-1);
        }

        $comment = $this->requirePost('comment');

        // Decode comment (base64)
        $decodedComment = base64_decode($comment);
        if ($decodedComment === false) {
            Response::error(-1);
        }

        // Insert comment
        $stmt = $this->db->prepare(
            "INSERT INTO acccomments (accountID, userName, comment, uploadDate) 
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$accountID, $user['userName'], $decodedComment]);

        Response::send($this->db->lastInsertId());
    }

    /**
     * Get account comments
     * Endpoint: /database/getGJAccountComments20.php
     */
    public function getAccountComments(): void {
        $accountID = $this->requirePost('accountID');
        $page = $this->getPost('page', 0);
        $count = $this->getPost('count', 10);

        $offset = $page * $count;

        $stmt = $this->db->prepare(
            "SELECT * FROM acccomments WHERE accountID = ? ORDER BY uploadDate DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$accountID, $count, $offset]);
        $comments = $stmt->fetchAll();

        if (empty($comments)) {
            Response::error(-1);
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
            $commentData[] = Response::build($data, '~', '~');
        }

        $response = implode('|', $commentData);
        $response .= '#' . count($comments) . ':' . $offset . ':10';

        Response::send($response);
    }

    /**
     * Delete comment
     * Endpoint: /database/deleteGJComment20.php
     */
    public function deleteComment(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $commentID = $this->requirePost('commentID');
        $type = $this->getPost('type', 0); // 0 = level comment, 1 = account comment

        if ($type == 0) {
            $stmt = $this->db->prepare(
                "DELETE c FROM comments c
                 INNER JOIN users u ON c.userID = u.userID
                 WHERE c.commentID = ? AND u.accountID = ?"
            );
        } else {
            $stmt = $this->db->prepare("DELETE FROM acccomments WHERE commentID = ? AND accountID = ?");
        }
        
        $stmt->execute([$commentID, $accountID]);

        if ($stmt->rowCount() > 0) {
            Response::success(1);
        } else {
            Response::error(-1);
        }
    }

    /**
     * Build comment data for response
     */
    private function buildCommentData(array $comment): string {
        $data = [
            '2' => base64_encode($comment['comment']),
            '3' => $comment['userID'],
            '4' => $comment['likes'],
            '6' => $comment['commentID'],
            '7' => $comment['isSpam'] ?? 0,
            '9' => strtotime($comment['uploadDate']),
            '10' => $comment['percent'] ?? 0
        ];
        return Response::build($data, '~', '~');
    }

    /**
     * Build user data for comment response
     */
    private function buildUserData(array $user): string {
        $data = [
            $user['userID'],
            $user['userName'],
            $user['accountID']
        ];
        return implode(':', $data) . ':' . implode(':', [
            $user['icon'],
            $user['color1'],
            $user['color2'],
            $user['iconType'],
            $user['accGlow'] ?? 0,
            $user['accountID']
        ]);
    }
}
