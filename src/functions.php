<?php
/**
 * Xet - Utility methods for php
 *
 * @package   php-xet
 * @author    Jayson Wang <jayson@laboys.org>
 * @copyright Copyright (C) 2020 Jayson Wang
 * @license   MIT License
 * @link      https://github.com/wjiec/php-xet
 */
declare(strict_types=1);

namespace Xet;


if (!function_exists('base64_encode_safe')) {
    /**
     * Encode string by base64 with url safe
     *
     * @param string $data
     * @return string
     */
    function base64_encode_safe(string $data) {
        return str_replace(['+', '/'], ['-', '_'], trim(base64_encode($data), '='));
    }
}

if (!function_exists('base64_decode_safe')) {
    /**
     * Decode string by base64 with url safe
     *
     * @param string $data
     * @return string
     */
    function base64_decode_safe(string $data) {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data) .
            substr('====', strlen($data) % 4));
    }
}

if (!function_exists('aes_encrypt')) {
    /**
     * Encrypt by aes-* with iv and base64_encode automatically
     *
     * @param string $data
     * @param string $key
     * @param string $iv
     * @param string $method
     * @return false|string
     */
    function aes_encrypt(string $data, string $key, string $iv = '', string $method = 'AES-256-CBC') {
        return base64_encode_safe(openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv));
    }
}

if (!function_exists('aes_decrypt')) {
    /**
     * Decrypt by aes-* with iv and base64_decode automatically
     *
     * @param string $data
     * @param string $key
     * @param string $iv
     * @param string $method
     * @return false|string
     */
    function aes_decrypt(string $data, string $key, string $iv = '', string $method = 'AES-256-CBC') {
        return openssl_decrypt(base64_decode_safe($data), $method, $key, OPENSSL_RAW_DATA, $iv);
    }
}

if (!function_exists('array_at')) {
    /**
     * Gets the value from parameter object and default when key not found in object
     *
     * @param array $object
     * @param string|array $paths
     * @param null $default
     * @param string $delimiter
     * @return array|mixed|null
     */
    function array_at(array $object, $paths, $default = null, string $delimiter = '.') {
        if (!is_array($paths)) {
            $paths = explode($delimiter, strval($paths));
        }

        $find = &$object;
        foreach ($paths as $index) {
            if (!isset($find[$index])) {
                return $default;
            }
            $find = &$find[$index];
        }

        return $find;
    }
}

if (!function_exists('array_any')) {
    /**
     * Returns true if any item in an iterable are true, otherwise it returns false
     *
     * @param array $object
     * @param null $handler
     * @return bool
     */
    function array_any(array $object, $handler = null): bool {
        if (!$handler || !is_callable($handler)) {
            $handler = 'boolval';
        }

        foreach ($object as $value) {
            if (call_user_func($handler, $value)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('array_every')) {
    /**
     * @param array $object
     * @param null $handler
     * @return bool
     */
    function array_every(array $object, $handler = null): bool {
        if (!$handler || !is_callable($handler)) {
            $handler = 'boolval';
        }

        foreach ($object as $value) {
            if (!call_user_func($handler, $value)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('str_has_prefix')) {
    /**
     * Returns true when needle is prefix of haystack, false otherwise
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_has_prefix(string $haystack, string $needle): bool {
        return mb_strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_has_suffix')) {
    /**
     * Returns true when needle is suffix of haystack, false otherwise
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_has_suffix(string $haystack, string $needle): bool {
        return mb_strpos($haystack, $needle) === mb_strlen($haystack) - mb_strlen($needle);
    }
}

if (!function_exists('str_contains')) {
    /**
     * Returns true when haystack contains needle, false otherwise
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains(string $haystack, string $needle): bool {
        return mb_strpos($haystack, $needle) !== false;
    }
}
