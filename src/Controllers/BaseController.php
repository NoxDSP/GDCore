<?php
namespace GDCore\Controllers;

use GDCore\Config\Database;
use GDCore\Config\Config;
use GDCore\Utils\Response;
use PDO;

/**
 * Base Controller for all GD endpoints
 */
abstract class BaseController {
    protected PDO $db;
    protected Config $config;
    protected array $post;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->config = Config::getInstance();
        $this->post = $_POST;
    }

    /**
     * Get POST parameter
     */
    protected function getPost(string $key, $default = null) {
        return $this->post[$key] ?? $default;
    }

    /**
     * Require POST parameter
     */
    protected function requirePost(string $key) {
        if (!isset($this->post[$key])) {
            Response::error(-1);
        }
        return $this->post[$key];
    }

    /**
     * Check if user is authenticated
     */
    protected function checkAuth(): array {
        $accountID = $this->getPost('accountID');
        $gjp = $this->getPost('gjp');

        if (!$accountID || !$gjp) {
            Response::error(-1);
        }

        $stmt = $this->db->prepare("SELECT password FROM accounts WHERE accountID = ?");
        $stmt->execute([$accountID]);
        $account = $stmt->fetch();

        if (!$account) {
            Response::error(-1);
        }

        // Verify GJP
        $gjpDecoded = \GDCore\Utils\GDCrypto::decodeGJP($gjp);
        if (!password_verify($gjpDecoded, $account['password'])) {
            Response::error(-1);
        }

        return ['accountID' => $accountID];
    }

    /**
     * Get user info by accountID
     */
    protected function getUserByAccountID(int $accountID): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE accountID = ? LIMIT 1");
        $stmt->execute([$accountID]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get account info by accountID
     */
    protected function getAccountByID(int $accountID): ?array {
        $stmt = $this->db->prepare("SELECT * FROM accounts WHERE accountID = ? LIMIT 1");
        $stmt->execute([$accountID]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get user IP
     */
    protected function getIP(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Check if user is banned
     */
    protected function checkBan(int $accountID): bool {
        $stmt = $this->db->prepare(
            "SELECT * FROM bans WHERE accountID = ? AND (expireDate IS NULL OR expireDate > NOW())"
        );
        $stmt->execute([$accountID]);
        return $stmt->fetch() !== false;
    }

    /**
     * Log action
     */
    protected function logAction(int $type, int $accountID, string $value = '', 
                                 string $value2 = '', string $value3 = '', string $value4 = ''): void {
        $stmt = $this->db->prepare(
            "INSERT INTO actions (type, accountID, value, value2, value3, value4) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$type, $accountID, $value, $value2, $value3, $value4]);
    }
}
