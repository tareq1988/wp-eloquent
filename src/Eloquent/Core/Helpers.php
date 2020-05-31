<?php

namespace AmphiBee\Eloquent\Core;

/**
 * Core Helpers
 *
 * @package AmphiBee\Eloquent\Core
 */
class Helpers
{

    /**
     * A copy of WordPress's `is_serialized` function.
     *
     * If within WordPress executino space it'll call the WordPress function, or
     * it will us our fallback shim when outside WordPress.
     *
     * @param mixed $data
     * @param boolean $strict
     * @return boolean
     */
    public static function isSerialized($data, $strict = true)
    {
        if (function_exists('is_serialized')) {
            return \is_serialized($data, $strict);
        }
        // if it isn't a string, it isn't serialized.
        if (! is_string($data)) {
            return false;
        }

        $data = trim($data);

        if ('N;' == $data) {
            return true;
        }

        if (strlen($data) < 4) {
            return false;
        }

        if (':' !== $data[1]) {
            return false;
        }

        if ($strict) {
            $lastc = substr($data, -1);

            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace     = strpos($data, '}');
            // Either ; or } must exist.
            if (false === $semicolon && false === $brace) {
                return false;
            }
            // But neither must be in the first X characters.
            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }
            if (false !== $brace && $brace < 4) {
                return false;
            }
        }

        $token = $data[0];

        switch ($token) {
            case 's':
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
                // or else fall through
            case 'a':
            case 'O':
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }

        return false;
    }
}
