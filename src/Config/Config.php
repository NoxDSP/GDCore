<?php
namespace GDCore\Config;

class Config {
    private static $instance = null;
    private $config = [];

    private function __construct() {
        $this->loadEnv();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnv(): void {
        $envFile = dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envFile)) {
            $envFile = dirname(__DIR__, 2) . '/.env.example';
        }

        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                $this->config[$key] = $value;
            }
        }
    }

    public function get(string $key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, $value): void {
        $this->config[$key] = $value;
    }

    public function has(string $key): bool {
        return isset($this->config[$key]);
    }

    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
