<?php
namespace GDCore\Utils;

/**
 * Geometry Dash cryptography utilities
 */
class GDCrypto {
    // Base64 URL-safe encoding/decoding
    public static function base64UrlDecode(string $data): string {
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= str_repeat('=', 4 - $mod4);
        }
        return base64_decode($data);
    }

    public static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Generate CHK value for level data
     * Based on XOR and salting
     */
    public static function genSolo(string $levelString): string {
        $hash = 0;
        $len = strlen($levelString);
        
        for ($i = 0; $i < $len; $i++) {
            $hash += ord($levelString[$i]);
        }
        
        return (string)$hash;
    }

    /**
     * Generate CHK2 value for level data (GD 2.1+)
     */
    public static function genSolo2(string $levelString): string {
        return self::genSolo($levelString);
    }

    /**
     * Decode level string
     */
    public static function decodeLevelString(string $levelString): string {
        // Level string is gzipped and base64 encoded
        $decoded = base64_decode(str_replace('_', '/', str_replace('-', '+', $levelString)));
        
        if ($decoded === false) {
            return '';
        }
        
        $uncompressed = @gzuncompress($decoded);
        return $uncompressed !== false ? $uncompressed : '';
    }

    /**
     * Encode level string
     */
    public static function encodeLevelString(string $levelData): string {
        $compressed = gzcompress($levelData, 9);
        $encoded = base64_encode($compressed);
        return str_replace('/', '_', str_replace('+', '-', $encoded));
    }

    /**
     * Generate RS (random string) for rewards
     */
    public static function genRS(): string {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 5);
    }

    /**
     * Generate Udid (unique device identifier)
     */
    public static function genUdid(): string {
        return sprintf(
            'S%d%d%d%d',
            rand(10000000, 99999999),
            rand(10000000, 99999999),
            rand(10000000, 99999999),
            rand(1000, 9999)
        );
    }

    /**
     * Decode password (GJP)
     */
    public static function decodeGJP(string $gjp): string {
        return XORCipher::cipher(base64_decode($gjp), XORCipher::KEY_37526);
    }

    /**
     * Encode password (GJP)
     */
    public static function encodeGJP(string $password): string {
        return base64_encode(XORCipher::cipher($password, XORCipher::KEY_37526));
    }
}
