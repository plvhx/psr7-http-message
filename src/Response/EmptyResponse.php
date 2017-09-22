<?php

namespace Gandung\Psr7\Response;

use Gandung\Psr7\Response;
use Gandung\Psr7\PhpTempStream;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class EmptyResponse extends Response
{
    public function __construct($statusCode = 204, $headers = [])
    {
        $body = new PhpTempStream;
        parent::__construct($body, $statusCode, $headers);
    }
}
