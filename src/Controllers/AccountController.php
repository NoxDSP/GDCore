<?php
namespace GDCore\Controllers;

use GDCore\Utils\Response;
use GDCore\Utils\GDCrypto;

/**
 * Account Management Controller
 * Handles registration, login, profile updates
 */
class AccountController extends BaseController {
    
    /**
     * Register new account
     * Endpoint: /database/accounts/registerGJAccount.php
     */
    public function register(): void {
        $userName = $this->requirePost('userName');
        $password = $this->requirePost('password');
        $email = $this->requirePost('email');

        // Validation
        if (strlen($userName) < 3 || strlen($userName) > 20) {
            Response::error(-4); // Username too short/long
        }

        if (strlen($password) < 6) {
            Response::error(-5); // Password too short
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error(-6); // Invalid email
        }

        // Check if username exists
        $stmt = $this->db->prepare("SELECT accountID FROM accounts WHERE userName = ?");
        $stmt->execute([$userName]);
        if ($stmt->fetch()) {
            Response::error(-2); // Username taken
        }

        // Check if email exists
        $stmt = $this->db->prepare("SELECT accountID FROM accounts WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            Response::error(-3); // Email taken
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert account
        $stmt = $this->db->prepare(
            "INSERT INTO accounts (userName, password, email, registerDate) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$userName, $hashedPassword, $email]);
        $accountID = $this->db->lastInsertId();

        // Create user stats entry
        $stmt = $this->db->prepare(
            "INSERT INTO users (accountID, userName, IP, isRegistered, registerDate) VALUES (?, ?, ?, 1, NOW())"
        );
        $stmt->execute([$accountID, $userName, $this->getIP()]);

        Response::success(1);
    }

    /**
     * Login to account
     * Endpoint: /database/accounts/loginGJAccount.php
     */
    public function login(): void {
        $userName = $this->requirePost('userName');
        $password = $this->requirePost('password');
        $udid = $this->getPost('udid', '');

        // Get account
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE userName = ?");
        $stmt->execute([$userName]);
        $account = $stmt->fetch();

        if (!$account) {
            Response::error(-1); // Account not found
        }

        // Verify password
        if (!password_verify($password, $account['password'])) {
            Response::error(-1); // Wrong password
        }

        // Check if banned
        if ($this->checkBan($account['accountID'])) {
            Response::error(-12); // Banned
        }

        // Update IP
        $stmt = $this->db->prepare("UPDATE users SET IP = ? WHERE accountID = ?");
        $stmt->execute([$this->getIP(), $account['accountID']]);

        // Return accountID and userID
        $stmt = $this->db->prepare("SELECT userID FROM users WHERE accountID = ? LIMIT 1");
        $stmt->execute([$account['accountID']]);
        $user = $stmt->fetch();

        Response::send($account['accountID'] . ',' . ($user['userID'] ?? 0));
    }

    /**
     * Update account settings
     * Endpoint: /database/accounts/updateGJAccSettings20.php
     */
    public function updateSettings(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];

        $mS = $this->getPost('mS', 0);  // Message privacy
        $frS = $this->getPost('frS', 0); // Friend request privacy
        $cS = $this->getPost('cS', 0);   // Comment privacy
        $youtube = $this->getPost('yt', '');
        $twitter = $this->getPost('twitter', '');
        $twitch = $this->getPost('twitch', '');

        $stmt = $this->db->prepare(
            "UPDATE accounts SET mS = ?, frS = ?, cS = ?, youtubeurl = ?, twitter = ?, twitch = ? WHERE accountID = ?"
        );
        $stmt->execute([$mS, $frS, $cS, $youtube, $twitter, $twitch, $accountID]);

        Response::success(1);
    }

    /**
     * Get account info
     * Endpoint: /database/getGJUserInfo20.php
     */
    public function getUserInfo(): void {
        $targetAccountID = $this->getPost('targetAccountID');
        
        if (!$targetAccountID) {
            Response::error(-1);
        }

        // Get account data
        $stmt = $this->db->prepare(
            "SELECT a.*, u.* FROM accounts a 
             INNER JOIN users u ON a.accountID = u.accountID 
             WHERE a.accountID = ? LIMIT 1"
        );
        $stmt->execute([$targetAccountID]);
        $user = $stmt->fetch();

        if (!$user) {
            Response::error(-1);
        }

        // Build response
        $response = [
            '1' => $user['userName'],
            '2' => $user['userID'],
            '3' => $user['stars'],
            '4' => $user['demons'],
            '6' => $user['userID'],
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
            '18' => $user['mS'] ?? 0,
            '19' => $user['frS'] ?? 0,
            '20' => $user['youtubeurl'] ?? '',
            '21' => $user['accIcon'],
            '22' => $user['accShip'],
            '23' => $user['accBall'],
            '24' => $user['accBird'],
            '25' => $user['accDart'],
            '26' => $user['accRobot'],
            '27' => 0, // Glow
            '28' => $user['accGlow'],
            '29' => 1, // isRegistered
            '30' => 0, // globalRank
            '43' => $user['accSpider'],
            '44' => $user['twitter'] ?? '',
            '45' => $user['twitch'] ?? '',
            '46' => 0, // diamonds
            '48' => $user['accExplosion'],
            '49' => 0, // modLevel
            '50' => $user['cS'] ?? 0
        ];

        Response::send(Response::build($response));
    }

    /**
     * Update user profile
     * Endpoint: /database/updateGJUserScore22.php
     */
    public function updateUserScore(): void {
        $accountID = $this->getPost('accountID', 0);
        $userName = $this->requirePost('userName');
        $stars = $this->getPost('stars', 0);
        $demons = $this->getPost('demons', 0);
        $icon = $this->getPost('icon', 1);
        $color1 = $this->getPost('color1', 0);
        $color2 = $this->getPost('color2', 3);
        $iconType = $this->getPost('iconType', 0);
        $coins = $this->getPost('coins', 0);
        $userCoins = $this->getPost('userCoins', 0);
        $special = $this->getPost('special', 0);
        $accIcon = $this->getPost('accIcon', 1);
        $accShip = $this->getPost('accShip', 1);
        $accBall = $this->getPost('accBall', 1);
        $accBird = $this->getPost('accBird', 1);
        $accDart = $this->getPost('accDart', 1);
        $accRobot = $this->getPost('accRobot', 1);
        $accGlow = $this->getPost('accGlow', 0);
        $accSpider = $this->getPost('accSpider', 1);
        $accExplosion = $this->getPost('accExplosion', 1);
        $accSwing = $this->getPost('accSwing', 1);

        // Check if user exists
        $stmt = $this->db->prepare("SELECT userID FROM users WHERE userName = ?");
        $stmt->execute([$userName]);
        $user = $stmt->fetch();

        if ($user) {
            // Update existing user
            $stmt = $this->db->prepare(
                "UPDATE users SET 
                accountID = ?, stars = ?, demons = ?, icon = ?, color1 = ?, color2 = ?, 
                iconType = ?, coins = ?, userCoins = ?, special = ?, accIcon = ?, accShip = ?, 
                accBall = ?, accBird = ?, accDart = ?, accRobot = ?, accGlow = ?, accSpider = ?,
                accExplosion = ?, accSwing = ?, IP = ?
                WHERE userName = ?"
            );
            $stmt->execute([
                $accountID, $stars, $demons, $icon, $color1, $color2, $iconType, $coins, $userCoins,
                $special, $accIcon, $accShip, $accBall, $accBird, $accDart, $accRobot, $accGlow,
                $accSpider, $accExplosion, $accSwing, $this->getIP(), $userName
            ]);
            $userID = $user['userID'];
        } else {
            // Create new user
            $stmt = $this->db->prepare(
                "INSERT INTO users (
                    accountID, userName, stars, demons, icon, color1, color2, iconType, coins, 
                    userCoins, special, accIcon, accShip, accBall, accBird, accDart, accRobot, 
                    accGlow, accSpider, accExplosion, accSwing, IP, isRegistered
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $accountID, $userName, $stars, $demons, $icon, $color1, $color2, $iconType, $coins,
                $userCoins, $special, $accIcon, $accShip, $accBall, $accBird, $accDart, $accRobot,
                $accGlow, $accSpider, $accExplosion, $accSwing, $this->getIP(), ($accountID > 0 ? 1 : 0)
            ]);
            $userID = $this->db->lastInsertId();
        }

        Response::send($userID);
    }

    /**
     * Save user data (cloud save)
     * Endpoint: /database/accounts/syncGJAccountNew.php
     */
    public function saveData(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];
        
        $saveData = $this->requirePost('saveData');

        $stmt = $this->db->prepare("UPDATE accounts SET saveData = ? WHERE accountID = ?");
        $stmt->execute([$saveData, $accountID]);

        Response::success(1);
    }

    /**
     * Load user data (cloud save)
     * Endpoint: /database/accounts/syncGJAccountNew.php
     */
    public function loadData(): void {
        $auth = $this->checkAuth();
        $accountID = $auth['accountID'];

        $stmt = $this->db->prepare("SELECT saveData FROM accounts WHERE accountID = ?");
        $stmt->execute([$accountID]);
        $account = $stmt->fetch();

        if (!$account || empty($account['saveData'])) {
            Response::error(-1);
        }

        Response::send($account['saveData']);
    }
}
