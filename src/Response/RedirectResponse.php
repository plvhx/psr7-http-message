<?php

namespace Gandung\Psr7\Response;

use Gandung\Psr7\Response;
use Psr\Http\Message\UriInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RedirectResponse extends Response
{
    public function __construct($uri, $statusCode = 302, $headers = [])
    {
        if (!is_string($uri) && !$uri instanceof UriInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Parameter 1 of %s must be a string or instance of Psr\\Http\\Message\\UriInterface",
                    __METHOD__
                )
            );
        }

        $headers['location'] = (string)$uri;
        parent::__construct('php://temp', $statusCode, $headers);
    }
}
