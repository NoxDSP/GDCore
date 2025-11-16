<?php
namespace GDCore\Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = Config::getInstance();
        
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                $config->get('DB_HOST', 'localhost'),
                $config->get('DB_PORT', '3306'),
                $config->get('DB_NAME', 'gdcore')
            );
            
            $this->connection = new PDO(
                $dsn,
                $config->get('DB_USER', 'root'),
                $config->get('DB_PASS', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            if ($config->get('DEBUG_MODE', false)) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please contact the administrator.");
            }
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $config = Config::getInstance();
            if ($config->get('DEBUG_MODE', false)) {
                throw $e;
            }
            return false;
        }
    }

    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }

    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
