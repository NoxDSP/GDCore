<?php
namespace GDCore\Utils;

/**
 * Hash generation for Geometry Dash
 * Based on methods figured out by pavlukivan and Italian APK Downloader
 */
class Hash {
    // Salts for different hash types
    const SALT_LEVEL = 'xI25fpAapCQg';
    const SALT_COMMENT = 'xPT6iUrtws0J';
    const SALT_CHALLENGES = 'oC36fpYaPtdg';
    const SALT_REWARDS = 'pC26fpYaQCtg';
    const SALT_LIKE_RATE = 'ysg6pUrtjn0J';
    const SALT_USERSCORE = 'xI35fsAapCRg';

    /**
     * Generate Level Hash (for level upload)
     */
    public static function genLevel(array $levelData): string {
        $values = [
            $levelData['levelID'] ?? 0,
            $levelData['coins'] ?? 0,
            $levelData['starCoins'] ?? 0,
            $levelData['levelVersion'] ?? 1
        ];
        
        $string = implode(',', $values);
        return self::genHash($string, self::SALT_LEVEL);
    }

    /**
     * Generate Comment Hash
     */
    public static function genComment(string $userName, string $comment, int $levelID, int $percent): string {
        $string = $userName . $comment . $levelID . $percent;
        return self::genHash($string, self::SALT_COMMENT);
    }

    /**
     * Generate Solo Hash (CHK value)
     */
    public static function genSolo(string $levelString): string {
        $levelString = str_replace('-', '+', str_replace('_', '/', $levelString));
        $decoded = base64_decode($levelString);
        
        if ($decoded === false) {
            return '0';
        }
        
        $uncompressed = @gzuncompress($decoded);
        if ($uncompressed === false) {
            return '0';
        }
        
        return (string)(strlen($uncompressed) + strlen($levelString) / 40);
    }

    /**
     * Generate Solo2 Hash (CHK2 value for GD 2.1+)
     */
    public static function genSolo2(string $levelString): string {
        return self::genSolo($levelString);
    }

    /**
     * Generate Leaderboard Hash
     */
    public static function genLeaderboard(string $accountID, string $userCoins, string $demons, string $stars, 
                                          string $coins, string $iconType, string $icon, string $diamonds, 
                                          string $color1, string $color2, string $secretCoins, string $type): string {
        $string = $accountID . $userCoins . $demons . $stars . $coins . $iconType . $icon . $diamonds . 
                  $color1 . $color2 . $secretCoins . $type;
        return self::genHash($string, self::SALT_USERSCORE);
    }

    /**
     * Generate generic hash using SHA1
     */
    private static function genHash(string $string, string $salt, int $length = 40): string {
        return substr(sha1($string . $salt), 0, $length);
    }

    /**
     * Generate pack hash (for rewards)
     */
    public static function genPack(string $chk, string $type = 'pack'): string {
        $salt = $type === 'pack' ? self::SALT_REWARDS : self::SALT_CHALLENGES;
        return self::genHash($chk, $salt, 5);
    }
}
