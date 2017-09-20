<?php

namespace Gandung\Psr7;

trait UriTrait
{
    protected function validateScheme($scheme)
    {
        return empty($scheme) || !preg_match(self::SCHEME_REGEX_PATTERN, $scheme)
            ? false
            : true;
    }

    protected function validateHost($host)
    {
        $full = '/(?(?='
            . self::IPV4_REGEX_PATTERN
            . ')'
            . self::IPV4_REGEX_PATTERN
            . '|'
            . self::HOST_REGEX_PATTERN
            . ')/';

        return empty($host) || !preg_match($full, $host)
            ? false
            : true;
    }

    protected function validatePort($port)
    {
        return is_int($port)
            ? true
            : (empty($port) || !preg_match(self::PORT_REGEX_PATTERN, $port)
                ? false
                : true);
    }

    protected function validatePath($path)
    {
        return empty($path) || $path[0] !== '/'
            ? false
            : true;
    }
}
