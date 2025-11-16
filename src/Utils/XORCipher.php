<?php
namespace GDCore\Utils;

/**
 * XOR Cipher for Geometry Dash requests
 * Based on https://github.com/sathoro/php-xor-cipher
 */
class XORCipher {
    const KEY_37526 = '37526';
    const KEY_59182 = '59182';
    const KEY_29481 = '29481';
    const KEY_58281 = '58281';
    const KEY_39673 = '39673';
    const KEY_79244 = '79244';
    const KEY_85271 = '85271';
    
    /**
     * Encrypt/Decrypt data using XOR cipher
     * 
     * @param string $data Data to encrypt/decrypt
     * @param string $key XOR key
     * @return string
     */
    public static function cipher(string $data, string $key): string {
        $dataLen = strlen($data);
        $keyLen = strlen($key);
        $output = '';
        
        for ($i = 0; $i < $dataLen; $i++) {
            $output .= chr(ord($data[$i]) ^ ord($key[$i % $keyLen]));
        }
        
        return $output;
    }

    /**
     * Decrypt level password
     * 
     * @param string $password Encrypted password
     * @return string Decrypted password
     */
    public static function decryptLevelPassword(string $password): string {
        $decoded = base64_decode($password);
        return self::cipher($decoded, self::KEY_26364);
    }

    /**
     * Encrypt level password
     * 
     * @param string $password Plain password
     * @return string Encrypted password
     */
    public static function encryptLevelPassword(string $password): string {
        $encrypted = self::cipher($password, self::KEY_26364);
        return base64_encode($encrypted);
    }

    const KEY_26364 = '26364';
    const KEY_19283 = '19283';
    const KEY_48291 = '48291';
}
