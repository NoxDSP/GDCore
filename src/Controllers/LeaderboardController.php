<?php
namespace GDCore\Controllers;

use GDCore\Utils\Response;
use GDCore\Utils\Hash;

/**
 * Leaderboard Controller
 * Handles top players, creator leaderboards
 */
class LeaderboardController extends BaseController {
    
    /**
     * Get leaderboard scores
     * Endpoint: /database/getGJScores20.php
     */
    public function getScores(): void {
        $type = $this->requirePost('type'); // top, creators, relative, friends
        $count = $this->getPost('count', 100);
        $accountID = $this->getPost('accountID', 0);

        switch ($type) {
            case 'top':
                $this->getTopPlayers($count);
                break;
            case 'creators':
                $this->getCreators($count);
                break;
            case 'relative':
                $this->getRelative($accountID, $count);
                break;
            case 'friends':
                $this->getFriends($accountID, $count);
                break;
            default:
                Response::error(-1);
        }
    }

    /**
     * Get top players
     */
    private function getTopPlayers(int $count): void {
        $stmt = $this->db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             WHERE u.isBanned = 0
             ORDER BY u.stars DESC, u.demons DESC, u.userCoins DESC
             LIMIT ?"
        );
        $stmt->execute([$count]);
        $users = $stmt->fetchAll();

        $this->buildLeaderboardResponse($users, 'top');
    }

    /**
     * Get creator leaderboard
     */
    private function getCreators(int $count): void {
        $stmt = $this->db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             WHERE u.isBanned = 0 AND u.creatorPoints > 0
             ORDER BY u.creatorPoints DESC
             LIMIT ?"
        );
        $stmt->execute([$count]);
        $users = $stmt->fetchAll();

        $this->buildLeaderboardResponse($users, 'creators');
    }

    /**
     * Get relative leaderboard (around user)
     */
    private function getRelative(int $accountID, int $count): void {
        // Get user's rank
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) + 1 as rank FROM users u
             WHERE u.stars > (SELECT stars FROM users WHERE accountID = ?)
             AND u.isBanned = 0"
        );
        $stmt->execute([$accountID]);
        $userRank = $stmt->fetchColumn();

        $offset = max(0, $userRank - ($count / 2));

        $stmt = $this->db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             WHERE u.isBanned = 0
             ORDER BY u.stars DESC, u.demons DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$count, $offset]);
        $users = $stmt->fetchAll();

        $this->buildLeaderboardResponse($users, 'relative');
    }

    /**
     * Get friends leaderboard
     */
    private function getFriends(int $accountID, int $count): void {
        $stmt = $this->db->prepare(
            "SELECT u.*, a.* FROM users u
             INNER JOIN accounts a ON u.accountID = a.accountID
             INNER JOIN friendships f ON (f.friendAccountID = u.accountID)
             WHERE f.accountID = ? AND u.isBanned = 0
             ORDER BY u.stars DESC, u.demons DESC
             LIMIT ?"
        );
        $stmt->execute([$accountID, $count]);
        $users = $stmt->fetchAll();

        $this->buildLeaderboardResponse($users, 'friends');
    }

    /**
     * Build leaderboard response
     */
    private function buildLeaderboardResponse(array $users, string $type): void {
        if (empty($users)) {
            Response::error(-1);
        }

        $userData = [];
        $rank = 1;

        foreach ($users as $user) {
            $data = [
                '1' => $user['userName'],
                '2' => $user['userID'],
                '3' => $user['stars'],
                '4' => $user['demons'],
                '6' => $rank,
                '7' => $user['accountID'],
                '8' => $user['creatorPoints'],
                '9' => $user['icon'],
                '10' => $user['color1'],
                '11' => $user['color2'],
                '13' => $user['coins'],
                '14' => $user['iconType'],
                '15' => $user['special'],
                '16' => $user['accountID'],
                '17' => $user['userCoins'],
                '21' => $user['accIcon'],
                '22' => $user['accShip'],
                '23' => $user['accBall'],
                '24' => $user['accBird'],
                '25' => $user['accDart'],
                '26' => $user['accRobot'],
                '28' => $user['accGlow'],
                '43' => $user['accSpider'],
                '48' => $user['accExplosion'],
                '53' => $user['accSwing'] ?? 1
            ];
            
            $userData[] = Response::build($data, ':', '~');
            $rank++;
        }

        Response::send(implode('|', $userData));
    }
}
