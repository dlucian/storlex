<?php

namespace App;

/**
 * Sanitize class
 *
 * Useful to sanitize user input.
 *
 * @link https://www.php.net/manual/en/filter.filters.sanitize.php
 */
class Sanitize
{
    /**
     * Sanitize a string
     *
     * @param string $string
     * @return string
     */
    public static function string(string $string): string
    {
        $sanitized = filter_var($string, FILTER_SANITIZE_STRING);
        return $sanitized ?: '';
    }

    /**
     * Sanitize an URL
     *
     * @param string $url
     * @return string
     */
    public static function url(string $url): string
    {
        $sanitized = filter_var($url, FILTER_SANITIZE_URL);
        return $sanitized ?: '';
    }
}
