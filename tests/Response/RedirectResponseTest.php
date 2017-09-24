<?php

namespace Gandung\Psr7\Tests\Response;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Uri;
use Gandung\Psr7\Response\RedirectResponse;

class RedirectResponseTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanRaiseExceptionByGettingInstanceWithInvalidURI()
    {
        $response = new RedirectResponse(null);
    }

    public function testCanGetInstance()
    {
        $uri = (new Uri())
            ->withScheme('https')
            ->withHost('example.com')
            ->withPort(1337)
            ->withPath('/a/b/c')
            ->withQuery('a=b&foo=bar')
            ->withFragment('shit');
        $this->assertInstanceOf(Uri::class, $uri);
        $response = new RedirectResponse($uri);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
