<?php
namespace GDCore\Controllers;

use GDCore\Utils\Response;
use GDCore\Utils\GDCrypto;
use GDCore\Utils\Hash;

/**
 * Level Management Controller
 * Handles level upload, download, search, rating
 */
class LevelController extends BaseController {
    
    /**
     * Upload level
     * Endpoint: /database/uploadGJLevel21.php
     */
    public function upload(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];

        // Get user
        $user = $this->getUserByAccountID($accountID);
        if (!$user) {
            Response::error(-1);
        }

        // Check if banned
        if ($this->checkBan($accountID)) {
            Response::error(-1);
        }

        // Get level data
        $levelID = $this->getPost('levelID', 0);
        $levelName = $this->requirePost('levelName');
        $levelDesc = $this->getPost('levelDesc', '');
        $levelVersion = $this->getPost('levelVersion', 1);
        $levelLength = $this->getPost('levelLength', 0);
        $audioTrack = $this->getPost('audioTrack', 0);
        $password = $this->getPost('password', 0);
        $original = $this->getPost('original', 0);
        $twoPlayer = $this->getPost('twoPlayer', 0);
        $songID = $this->getPost('songID', 0);
        $objects = $this->getPost('objects', 0);
        $coins = $this->getPost('coins', 0);
        $requestedStars = $this->getPost('requestedStars', 0);
        $unlisted = $this->getPost('unlisted', 0);
        $ldm = $this->getPost('ldm', 0);
        $levelString = $this->requirePost('levelString');
        $seed = $this->getPost('seed', '');
        $seed2 = $this->getPost('seed2', '');
        $gameVersion = $this->getPost('gameVersion', 1);

        // Decode and validate level string
        $decodedLevel = GDCrypto::decodeLevelString($levelString);
        if (empty($decodedLevel)) {
            Response::error(-1);
        }

        // Check if updating existing level
        if ($levelID > 0) {
            $stmt = $this->db->prepare("SELECT * FROM levels WHERE levelID = ? AND accountID = ?");
            $stmt->execute([$levelID, $accountID]);
            $existingLevel = $stmt->fetch();

            if (!$existingLevel) {
                Response::error(-1); // Level not found or not owned by user
            }

            // Update level
            $stmt = $this->db->prepare(
                "UPDATE levels SET 
                levelName = ?, levelDesc = ?, levelVersion = ?, length = ?, 
                twoPlayer = ?, songID = ?, objects = ?, coins = ?, 
                requestedStars = ?, unlisted = ?, isLDM = ?, levelString = ?,
                password = ?, originalLevel = ?, gameVersion = ?, updateDate = NOW()
                WHERE levelID = ? AND accountID = ?"
            );
            $stmt->execute([
                $levelName, $levelDesc, $levelVersion, $levelLength, $twoPlayer, $songID,
                $objects, $coins, $requestedStars, $unlisted, $ldm, $levelString,
                $password, $original, $gameVersion, $levelID, $accountID
            ]);

            Response::send($levelID);
        } else {
            // Insert new level
            $stmt = $this->db->prepare(
                "INSERT INTO levels (
                    levelName, levelDesc, levelVersion, userID, userName, accountID,
                    length, twoPlayer, songID, objects, coins, requestedStars, 
                    unlisted, isLDM, levelString, password, originalLevel, gameVersion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $levelName, $levelDesc, $levelVersion, $user['userID'], $user['userName'], $accountID,
                $levelLength, $twoPlayer, $songID, $objects, $coins, $requestedStars,
                $unlisted, $ldm, $levelString, $password, $original, $gameVersion
            ]);

            $newLevelID = $this->db->lastInsertId();
            Response::send($newLevelID);
        }
    }

    /**
     * Download level
     * Endpoint: /database/downloadGJLevel22.php
     */
    public function download(): void {
        $levelID = $this->requirePost('levelID');
        $inc = $this->getPost('inc', 0); // Increment downloads

        $stmt = $this->db->prepare("SELECT * FROM levels WHERE levelID = ? LIMIT 1");
        $stmt->execute([$levelID]);
        $level = $stmt->fetch();

        if (!$level) {
            Response::error(-1);
        }

        // Increment downloads
        if ($inc == 1) {
            $stmt = $this->db->prepare("UPDATE levels SET downloads = downloads + 1 WHERE levelID = ?");
            $stmt->execute([$levelID]);
        }

        // Build response
        $response = [
            '1' => $level['levelID'],
            '2' => $level['levelName'],
            '3' => base64_encode($level['levelDesc'] ?? ''),
            '4' => $level['levelString'],
            '5' => $level['levelVersion'],
            '6' => $level['userID'],
            '8' => $level['starDifficulty'] ?? 0,
            '9' => $level['starDifficulty'] ?? 0,
            '10' => $level['downloads'],
            '11' => $level['starDifficulty'] ?? 0,
            '12' => $level['songID'],
            '13' => $level['gameVersion'],
            '14' => $level['likes'],
            '15' => $level['length'],
            '17' => $level['starDemon'] ?? 0,
            '18' => $level['stars'] ?? 0,
            '19' => $level['featured'] ?? 0,
            '25' => $level['starAuto'] ?? 0,
            '27' => $level['password'] == 0 ? 0 : 1,
            '28' => strtotime($level['uploadDate']),
            '29' => strtotime($level['updateDate']),
            '30' => $level['originalLevel'],
            '31' => $level['twoPlayer'],
            '35' => $level['songID'],
            '36' => base64_encode($level['extraString'] ?? ''),
            '37' => $level['coins'],
            '38' => $level['starCoins'] ?? 0,
            '39' => $level['requestedStars'],
            '40' => $level['isLDM'] ?? 0,
            '42' => $level['epic'] ?? 0,
            '43' => $level['starDemonDiff'] ?? 0,
            '45' => $level['objects'],
            '46' => 1, // EditorTime
            '47' => 2, // TotalTime
            '52' => 0, // SongIDs
            '53' => 0, // SFXIDs
            '57' => 0, // VerifiedCoins
        ];

        // Add hash
        $responseStr = Response::build($response);
        $hash = Hash::genSolo($level['levelString']);
        $responseStr .= '#' . $hash;

        // Add extra info
        $extraData = [
            '1~|~' . $level['userName'],
            '2~|~' . $level['userID'],
            '3~|~' . $level['accountID']
        ];
        $responseStr .= '#' . implode(':', $extraData) . '#';
        
        // Add hash2
        $hash2 = Hash::genSolo2($level['levelString']);
        $responseStr .= $hash2;

        Response::send($responseStr);
    }

    /**
     * Search levels
     * Endpoint: /database/getGJLevels21.php
     */
    public function search(): void {
        $type = $this->getPost('type', 0);
        $str = $this->getPost('str', '');
        $page = $this->getPost('page', 0);
        $diff = $this->getPost('diff', '-');
        $len = $this->getPost('len', '-');
        $featured = $this->getPost('featured', 0);
        $original = $this->getPost('original', 0);
        $twoPlayer = $this->getPost('twoPlayer', 0);
        $coins = $this->getPost('coins', 0);
        $epic = $this->getPost('epic', 0);
        $star = $this->getPost('star', 0);
        $noStar = $this->getPost('noStar', 0);
        $song = $this->getPost('song', 0);
        $customSong = $this->getPost('customSong', 0);

        $perPage = 10;
        $offset = $page * $perPage;

        // Build query
        $query = "SELECT * FROM levels WHERE 1=1";
        $params = [];

        // Search type
        switch ($type) {
            case 0: // Search
                if (!empty($str)) {
                    $query .= " AND levelName LIKE ?";
                    $params[] = '%' . $str . '%';
                }
                break;
            case 1: // Most downloaded
                $query .= " ORDER BY downloads DESC";
                break;
            case 2: // Most liked
                $query .= " ORDER BY likes DESC";
                break;
            case 3: // Trending
                $query .= " AND uploadDate > DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY likes DESC";
                break;
            case 4: // Recent
                $query .= " ORDER BY uploadDate DESC";
                break;
            case 5: // User levels
                if (!empty($str)) {
                    $query .= " AND accountID = ?";
                    $params[] = $str;
                }
                break;
            case 6: // Featured
                $query .= " AND featured = 1 ORDER BY rateDate DESC";
                break;
            case 7: // Magic
                $query .= " AND objects > 10000 ORDER BY likes DESC";
                break;
            case 10: // Map packs
                break;
            case 11: // Awarded
                $query .= " AND stars > 0 ORDER BY rateDate DESC";
                break;
            case 12: // Followed
                break;
            case 13: // Friends
                break;
            case 16: // Hall of Fame
                $query .= " AND epic = 1 ORDER BY rateDate DESC";
                break;
        }

        // Filters
        if ($diff != '-') {
            $difficulties = explode(',', $diff);
            $query .= " AND starDifficulty IN (" . implode(',', array_fill(0, count($difficulties), '?')) . ")";
            $params = array_merge($params, $difficulties);
        }

        if ($len != '-') {
            $lengths = explode(',', $len);
            $query .= " AND length IN (" . implode(',', array_fill(0, count($lengths), '?')) . ")";
            $params = array_merge($params, $lengths);
        }

        if ($featured) {
            $query .= " AND featured = 1";
        }

        if ($epic) {
            $query .= " AND epic = 1";
        }

        if ($star) {
            $query .= " AND stars > 0";
        }

        if ($noStar) {
            $query .= " AND stars = 0";
        }

        if ($song) {
            $query .= " AND songID = ?";
            $params[] = $song;
        }

        if ($twoPlayer) {
            $query .= " AND twoPlayer = 1";
        }

        if ($coins) {
            $query .= " AND coins > 0";
        }

        // Add unlisted filter
        $query .= " AND unlisted = 0";

        // Count total
        $countQuery = str_replace('SELECT *', 'SELECT COUNT(*)', $query);
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Add pagination
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $levels = $stmt->fetchAll();

        if (empty($levels)) {
            Response::error(-1);
        }

        // Build response
        $levelData = [];
        $userData = [];
        $songData = [];
        
        foreach ($levels as $level) {
            $levelData[] = $this->buildLevelData($level);
            
            // Add user data
            if (!isset($userData[$level['accountID']])) {
                $userData[$level['accountID']] = [
                    $level['userID'],
                    $level['userName'],
                    $level['accountID']
                ];
            }

            // Add song data if custom
            if ($level['songID'] > 0) {
                // TODO: Fetch custom song data
            }
        }

        $response = implode('|', $levelData);
        $response .= '#' . implode('|', array_map(function($u) {
            return implode(':', $u);
        }, $userData));
        $response .= '#' . implode('~:~', $songData);
        $response .= '#' . $total . ':' . $offset . ':' . $perPage;

        // Add hash
        $hashData = '';
        foreach ($levels as $level) {
            $hashData .= $level['levelID'][0] . $level['levelID'][strlen($level['levelID']) - 1];
            $hashData .= $level['stars'];
            $hashData .= $level['starCoins'] ? 1 : 0;
        }
        $hash = sha1($hashData . 'xI25fpAapCQg');
        $response .= '#' . $hash;

        Response::send($response);
    }

    /**
     * Build level data string for response
     */
    private function buildLevelData(array $level): string {
        $data = [
            '1' => $level['levelID'],
            '2' => $level['levelName'],
            '3' => base64_encode($level['levelDesc'] ?? ''),
            '5' => $level['levelVersion'],
            '6' => $level['userID'],
            '8' => $level['starDifficulty'] ?? 0,
            '9' => $level['starDifficulty'] ?? 0,
            '10' => $level['downloads'],
            '11' => $level['starDifficulty'] ?? 0,
            '12' => $level['songID'],
            '13' => $level['gameVersion'],
            '14' => $level['likes'],
            '15' => $level['length'],
            '17' => $level['starDemon'] ?? 0,
            '18' => $level['stars'] ?? 0,
            '19' => $level['featured'] ?? 0,
            '25' => $level['starAuto'] ?? 0,
            '30' => $level['originalLevel'],
            '31' => $level['twoPlayer'],
            '35' => $level['songID'],
            '37' => $level['coins'],
            '38' => $level['starCoins'] ?? 0,
            '39' => $level['requestedStars'],
            '42' => $level['epic'] ?? 0,
            '43' => $level['starDemonDiff'] ?? 0,
            '45' => $level['objects'],
            '46' => 1,
            '47' => 2
        ];

        return Response::build($data, ':', '~');
    }

    /**
     * Delete level
     * Endpoint: /database/deleteGJLevelUser20.php
     */
    public function delete(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        $levelID = $this->requirePost('levelID');

        $stmt = $this->db->prepare("DELETE FROM levels WHERE levelID = ? AND accountID = ?");
        $stmt->execute([$levelID, $accountID]);

        if ($stmt->rowCount() > 0) {
            Response::success(1);
        } else {
            Response::error(-1);
        }
    }

    /**
     * Rate level
     * Endpoint: /database/rateGJStars211.php
     */
    public function rate(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $levelID = $this->requirePost('levelID');
        $stars = $this->requirePost('stars');

        // Check if already rated
        $stmt = $this->db->prepare("SELECT * FROM levelscores WHERE levelID = ? AND accountID = ?");
        $stmt->execute([$levelID, $accountID]);
        
        if ($stmt->fetch()) {
            // Update existing rating
            $stmt = $this->db->prepare("UPDATE levelscores SET stars = ? WHERE levelID = ? AND accountID = ?");
            $stmt->execute([$stars, $levelID, $accountID]);
        } else {
            // Insert new rating
            $user = $this->getUserByAccountID($accountID);
            $stmt = $this->db->prepare(
                "INSERT INTO levelscores (levelID, userID, accountID, stars) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$levelID, $user['userID'], $accountID, $stars]);
        }

        Response::success(1);
    }

    /**
     * Like/dislike level
     * Endpoint: /database/likeGJItem211.php
     */
    public function like(): void {
        $accountID = $this->getPost('accountID', 0);
        $itemID = $this->requirePost('itemID');
        $like = $this->requirePost('like'); // 1 for like, 0 for dislike
        $type = $this->requirePost('type'); // 1 for level, 2 for comment, 3 for account comment

        if ($type == 1) {
            $change = $like == 1 ? 1 : -1;
            $stmt = $this->db->prepare("UPDATE levels SET likes = likes + ? WHERE levelID = ?");
            $stmt->execute([$change, $itemID]);
        }

        Response::success(1);
    }
}
