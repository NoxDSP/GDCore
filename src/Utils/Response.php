<?php
namespace GDCore\Utils;

/**
 * Response utilities for GD server responses
 */
class Response {
    /**
     * Send response and exit
     */
    public static function send($data): void {
        if (is_array($data)) {
            echo implode(':', $data);
        } else {
            echo $data;
        }
        exit;
    }

    /**
     * Send error response
     */
    public static function error(int $code = -1): void {
        self::send($code);
    }

    /**
     * Send success response
     */
    public static function success(int $code = 1): void {
        self::send($code);
    }

    /**
     * Build GD response string from array
     */
    public static function build(array $data, string $separator = ':', string $itemSeparator = '|'): string {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = $key . $separator . $value;
        }
        return implode($itemSeparator, $result);
    }

    /**
     * Build multiple items response
     */
    public static function buildMultiple(array $items, string $separator = ':', string $itemSeparator = '|'): string {
        $results = [];
        foreach ($items as $item) {
            $results[] = self::build($item, $separator, '~');
        }
        return implode($itemSeparator, $results);
    }

    /**
     * Parse GD request string to array
     */
    public static function parse(string $data, string $separator = '&'): array {
        $result = [];
        parse_str($data, $result);
        return $result;
    }
}
