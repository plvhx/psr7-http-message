<?php

namespace Gandung\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Gandung\Psr7\Uri;

class Request implements RequestInterface
{
    use MessageTrait, RequestTrait;
    
    /**
     * @var string
     */
    private $requestTarget;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var string
     */
    private $method;

    public function __construct($method, $uri, $headers = [], $body = null)
    {
        $this->method = $method;
        $this->uri = !($uri instanceof UriInterface)
            ? new Uri($uri)
            : $uri;
        $this->headers = $headers;
        $this->body = is_resource($body)
            ? new Stream($body)
            : ($body instanceof StreamInterface
                ? $body
                : new Stream(fopen('php://temp', 'r+')));
    }
}
