<?php

/**
 * Polyfill for mbstring regex functions that are not covered by
 * symfony/polyfill-mbstring (which ships with Laravel).
 *
 * mb_split, mb_ereg, mb_eregi are part of the "oniguruma" regex
 * portion of ext-mbstring and are NOT polyfilled by Symfony.
 *
 * This file should be loaded BEFORE the Composer autoloader.
 */

if (!function_exists('mb_split')) {
    /**
     * Polyfill for mb_split using preg_split with Unicode support.
     *
     * @param string $pattern  The regex pattern (POSIX-style, without delimiters)
     * @param string $string   The string to split
     * @param int    $limit    Maximum number of elements
     * @return array|false
     */
    function mb_split(string $pattern, string $string, int $limit = -1)
    {
        // mb_split uses POSIX-style patterns; convert to PCRE with Unicode
        $result = @preg_split('/' . $pattern . '/u', $string, $limit);
        return $result !== false ? $result : false;
    }
}

if (!function_exists('mb_ereg')) {
    /**
     * @return bool
     */
    function mb_ereg(string $pattern, string $string, &$matches = null)
    {
        $result = @preg_match('/' . $pattern . '/u', $string, $matches);
        return $result === 1;
    }
}

if (!function_exists('mb_eregi')) {
    /**
     * @return bool
     */
    function mb_eregi(string $pattern, string $string, &$matches = null)
    {
        $result = @preg_match('/' . $pattern . '/ui', $string, $matches);
        return $result === 1;
    }
}

if (!function_exists('mb_ereg_replace')) {
    /**
     * @return string|false
     */
    function mb_ereg_replace(string $pattern, string $replacement, string $string, $options = null)
    {
        $flags = 'u';
        if ($options && str_contains($options, 'i')) {
            $flags .= 'i';
        }
        return @preg_replace('/' . $pattern . '/' . $flags, $replacement, $string);
    }
}

if (!function_exists('mb_eregi_replace')) {
    /**
     * @return string|false
     */
    function mb_eregi_replace(string $pattern, string $replacement, string $string)
    {
        return @preg_replace('/' . $pattern . '/ui', $replacement, $string);
    }
}

if (!function_exists('mb_regex_encoding')) {
    /**
     * @return string|bool
     */
    function mb_regex_encoding($encoding = null)
    {
        if ($encoding === null) {
            return 'UTF-8';
        }
        return true;
    }
}

// Also ensure max_execution_time is sufficient
if ((int)ini_get('max_execution_time') < 120) {
    @set_time_limit(300);
}
