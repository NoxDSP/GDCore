<?php
namespace GDCore\Router;

use GDCore\Controllers\AccountController;
use GDCore\Controllers\LevelController;
use GDCore\Controllers\CommentController;
use GDCore\Controllers\SocialController;
use GDCore\Controllers\LeaderboardController;

/**
 * Simple router for GD endpoints
 */
class Router {
    private static $routes = [];

    public static function init(): void {
        // Account endpoints
        self::post('/database/accounts/registerGJAccount.php', [AccountController::class, 'register']);
        self::post('/database/accounts/loginGJAccount.php', [AccountController::class, 'login']);
        self::post('/database/accounts/updateGJAccSettings20.php', [AccountController::class, 'updateSettings']);
        self::post('/database/accounts/syncGJAccountNew.php', [AccountController::class, 'saveData']);
        self::post('/database/accounts/backupGJAccountNew.php', [AccountController::class, 'loadData']);
        
        // User endpoints
        self::post('/database/getGJUserInfo20.php', [AccountController::class, 'getUserInfo']);
        self::post('/database/updateGJUserScore22.php', [AccountController::class, 'updateUserScore']);
        
        // Level endpoints
        self::post('/database/uploadGJLevel21.php', [LevelController::class, 'upload']);
        self::post('/database/downloadGJLevel22.php', [LevelController::class, 'download']);
        self::post('/database/getGJLevels21.php', [LevelController::class, 'search']);
        self::post('/database/deleteGJLevelUser20.php', [LevelController::class, 'delete']);
        self::post('/database/rateGJStars211.php', [LevelController::class, 'rate']);
        self::post('/database/likeGJItem211.php', [LevelController::class, 'like']);
        
        // Comment endpoints
        self::post('/database/uploadGJComment21.php', [CommentController::class, 'uploadLevelComment']);
        self::post('/database/getGJComments21.php', [CommentController::class, 'getLevelComments']);
        self::post('/database/uploadGJAccComment20.php', [CommentController::class, 'uploadAccountComment']);
        self::post('/database/getGJAccountComments20.php', [CommentController::class, 'getAccountComments']);
        self::post('/database/deleteGJComment20.php', [CommentController::class, 'deleteComment']);
        
        // Social endpoints
        self::post('/database/uploadFriendRequest20.php', [SocialController::class, 'sendFriendRequest']);
        self::post('/database/acceptGJFriendRequest20.php', [SocialController::class, 'acceptFriendRequest']);
        self::post('/database/removeGJFriend20.php', [SocialController::class, 'removeFriend']);
        self::post('/database/getGJFriendRequests20.php', [SocialController::class, 'getFriendRequests']);
        self::post('/database/uploadGJMessage20.php', [SocialController::class, 'sendMessage']);
        self::post('/database/getGJMessages20.php', [SocialController::class, 'getMessages']);
        self::post('/database/downloadGJMessage20.php', [SocialController::class, 'downloadMessage']);
        self::post('/database/deleteGJMessages20.php', [SocialController::class, 'deleteMessage']);
        
        // Leaderboard endpoints
        self::post('/database/getGJScores20.php', [LeaderboardController::class, 'getScores']);
    }

    public static function post(string $path, array $handler): void {
        self::$routes['POST'][$path] = $handler;
    }

    public static function get(string $path, array $handler): void {
        self::$routes['GET'][$path] = $handler;
    }

    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset(self::$routes[$method][$path])) {
            http_response_code(404);
            echo '-1';
            return;
        }

        $handler = self::$routes[$method][$path];
        list($class, $method) = $handler;

        $controller = new $class();
        call_user_func([$controller, $method]);
    }
}
