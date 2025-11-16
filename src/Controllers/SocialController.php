<?php
namespace GDCore\Controllers;

use GDCore\Utils\Response;

/**
 * Social Features Controller
 * Handles friend requests, friendships, messages
 */
class SocialController extends BaseController {
    
    /**
     * Send friend request
     * Endpoint: /database/uploadFriendRequest20.php
     */
    public function sendFriendRequest(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $toAccountID = $this->requirePost('toAccountID');
        $comment = $this->getPost('comment', '');

        // Check if already friends
        $stmt = $this->db->prepare(
            "SELECT * FROM friendships WHERE 
             (accountID = ? AND friendAccountID = ?) OR 
             (accountID = ? AND friendAccountID = ?)"
        );
        $stmt->execute([$accountID, $toAccountID, $toAccountID, $accountID]);
        if ($stmt->fetch()) {
            Response::error(-1); // Already friends
        }

        // Check if request already sent
        $stmt = $this->db->prepare(
            "SELECT * FROM friendreqs WHERE accountID = ? AND toAccountID = ?"
        );
        $stmt->execute([$accountID, $toAccountID]);
        if ($stmt->fetch()) {
            Response::error(-1); // Request already sent
        }

        // Insert friend request
        $stmt = $this->db->prepare(
            "INSERT INTO friendreqs (accountID, toAccountID, comment, uploadDate) 
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$accountID, $toAccountID, $comment]);

        Response::success(1);
    }

    /**
     * Accept friend request
     * Endpoint: /database/acceptGJFriendRequest20.php
     */
    public function acceptFriendRequest(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $requestID = $this->requirePost('requestID');
        $targetAccountID = $this->requirePost('targetAccountID');

        // Delete friend request
        $stmt = $this->db->prepare(
            "DELETE FROM friendreqs WHERE requestID = ? AND toAccountID = ? AND accountID = ?"
        );
        $stmt->execute([$requestID, $accountID, $targetAccountID]);

        if ($stmt->rowCount() == 0) {
            Response::error(-1);
        }

        // Create friendship (both ways)
        $stmt = $this->db->prepare(
            "INSERT INTO friendships (accountID, friendAccountID, uploadDate) VALUES (?, ?, NOW())"
        );
        $stmt->execute([$accountID, $targetAccountID]);
        
        $stmt->execute([$targetAccountID, $accountID]);

        Response::success(1);
    }

    /**
     * Remove friend
     * Endpoint: /database/removeGJFriend20.php
     */
    public function removeFriend(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $targetAccountID = $this->requirePost('targetAccountID');

        $stmt = $this->db->prepare(
            "DELETE FROM friendships WHERE 
             (accountID = ? AND friendAccountID = ?) OR 
             (accountID = ? AND friendAccountID = ?)"
        );
        $stmt->execute([$accountID, $targetAccountID, $targetAccountID, $accountID]);

        Response::success(1);
    }

    /**
     * Get friend requests
     * Endpoint: /database/getGJFriendRequests20.php
     */
    public function getFriendRequests(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $page = $this->getPost('page', 0);
        $count = 10;
        $offset = $page * $count;

        $stmt = $this->db->prepare(
            "SELECT fr.*, u.userName FROM friendreqs fr
             INNER JOIN accounts a ON fr.accountID = a.accountID
             INNER JOIN users u ON a.accountID = u.accountID
             WHERE fr.toAccountID = ?
             ORDER BY fr.uploadDate DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$accountID, $count, $offset]);
        $requests = $stmt->fetchAll();

        if (empty($requests)) {
            Response::error(-2);
        }

        // Build response
        $requestData = [];
        
        foreach ($requests as $request) {
            $data = [
                '1' => $request['userName'],
                '2' => $request['accountID'],
                '9' => $request['requestID'],
                '32' => $request['accountID'],
                '35' => base64_encode($request['comment'] ?? ''),
                '37' => strtotime($request['uploadDate']),
                '41' => $request['isNew'] ?? 1
            ];
            $requestData[] = Response::build($data, ':', '~');
        }

        $response = implode('|', $requestData);
        $response .= '#' . count($requests) . ':' . $offset . ':10';

        Response::send($response);
    }

    /**
     * Send message
     * Endpoint: /database/uploadGJMessage20.php
     */
    public function sendMessage(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $user = $this->getUserByAccountID($accountID);
        if (!$user) {
            Response::error(-1);
        }

        $toAccountID = $this->requirePost('toAccountID');
        $subject = $this->requirePost('subject');
        $body = $this->requirePost('body');

        // Check friend status or message privacy
        $stmt = $this->db->prepare("SELECT mS FROM accounts WHERE accountID = ?");
        $stmt->execute([$toAccountID]);
        $targetAccount = $stmt->fetch();
        
        if (!$targetAccount) {
            Response::error(-1);
        }

        // Check if blocked
        if ($targetAccount['mS'] == 1) {
            // Check if friends
            $stmt = $this->db->prepare(
                "SELECT * FROM friendships WHERE accountID = ? AND friendAccountID = ?"
            );
            $stmt->execute([$toAccountID, $accountID]);
            if (!$stmt->fetch()) {
                Response::error(-1); // Not friends, can't message
            }
        } elseif ($targetAccount['mS'] == 2) {
            Response::error(-1); // Messages disabled
        }

        // Insert message
        $stmt = $this->db->prepare(
            "INSERT INTO messages (accountID, toAccountID, userName, subject, body, uploadDate) 
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$accountID, $toAccountID, $user['userName'], $subject, $body]);

        Response::success(1);
    }

    /**
     * Get messages
     * Endpoint: /database/getGJMessages20.php
     */
    public function getMessages(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $page = $this->getPost('page', 0);
        $getSent = $this->getPost('getSent', 0);
        $count = 10;
        $offset = $page * $count;

        if ($getSent == 1) {
            $whereClause = "accountID = ?";
        } else {
            $whereClause = "toAccountID = ?";
        }

        $stmt = $this->db->prepare(
            "SELECT * FROM messages WHERE $whereClause ORDER BY uploadDate DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$accountID, $count, $offset]);
        $messages = $stmt->fetchAll();

        if (empty($messages)) {
            Response::error(-2);
        }

        // Build response
        $messageData = [];
        
        foreach ($messages as $message) {
            $data = [
                '1' => $message['messageID'],
                '2' => $message['accountID'],
                '3' => $message['toAccountID'],
                '4' => base64_encode($message['subject']),
                '5' => $message['userName'],
                '6' => $message['userID'] ?? 0,
                '7' => strtotime($message['uploadDate']),
                '8' => $message['isNew'] ?? 1,
                '9' => $getSent
            ];
            $messageData[] = Response::build($data, ':', '~');
        }

        $response = implode('|', $messageData);
        $response .= '#' . count($messages) . ':' . $offset . ':10';

        Response::send($response);
    }

    /**
     * Download message
     * Endpoint: /database/downloadGJMessage20.php
     */
    public function downloadMessage(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $messageID = $this->requirePost('messageID');

        $stmt = $this->db->prepare(
            "SELECT * FROM messages WHERE messageID = ? AND (toAccountID = ? OR accountID = ?)"
        );
        $stmt->execute([$messageID, $accountID, $accountID]);
        $message = $stmt->fetch();

        if (!$message) {
            Response::error(-1);
        }

        // Mark as read
        if ($message['toAccountID'] == $accountID) {
            $stmt = $this->db->prepare("UPDATE messages SET isNew = 0 WHERE messageID = ?");
            $stmt->execute([$messageID]);
        }

        // Build response
        $data = [
            '1' => $message['messageID'],
            '2' => $message['accountID'],
            '3' => $message['toAccountID'],
            '4' => base64_encode($message['subject']),
            '5' => $message['userName'],
            '6' => $message['userID'] ?? 0,
            '7' => strtotime($message['uploadDate']),
            '8' => base64_encode($message['body']),
            '9' => $message['isNew'] ?? 0
        ];

        Response::send(Response::build($data, ':', '~'));
    }

    /**
     * Delete message
     * Endpoint: /database/deleteGJMessages20.php
     */
    public function deleteMessage(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $messageID = $this->requirePost('messageID');

        $stmt = $this->db->prepare(
            "DELETE FROM messages WHERE messageID = ? AND (toAccountID = ? OR accountID = ?)"
        );
        $stmt->execute([$messageID, $accountID, $accountID]);

        if ($stmt->rowCount() > 0) {
            Response::success(1);
        } else {
            Response::error(-1);
        }
    }
}
