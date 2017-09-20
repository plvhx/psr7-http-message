<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Request;
use Gandung\Psr7\Uri;
use Gandung\Psr7\Stream;

class RequestTest extends TestCase
{
    public function testCanGetInstance()
    {
        $request = new Request(
            'GET',
            '/shit/foo/bar',
            ['Content-Type' => 'application/json']
        );
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testCanGetRequestTarget()
    {
        $request = new Request(
            'GET',
            '/shit/foo/bar',
            ['Content-Type' => 'application/json']
        );
        $this->assertInstanceOf(Request::class, $request);
        $target = $request->getRequestTarget();
        $this->assertInternalType('string', $target);
        $this->assertEquals('/shit/foo/bar', $target);
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentRequestTarget()
    {
        $request = (new Request('GET', null, ['Content-Type' => 'application/json']))
            ->withRequestTarget('/foo/bar/baz');
        $this->assertInstanceOf(Request::class, $request);
        $target = $request->getRequestTarget();
        $this->assertInternalType('string', $target);
        $this->assertEquals('/foo/bar/baz', $target);
    }

    public function testCanGetMethod()
    {
        $request = new Request(
            'GET',
            '/foo/bar/baz',
            ['Content-Type' => 'application/json']
        );
        $this->assertInstanceOf(Request::class, $request);
        $method = $request->getMethod();
        $this->assertInternalType('string', $method);
        $this->assertEquals('GET', $method);
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentHTTPMethod()
    {
        $request = (new Request('GET', '/foo/bar/baz', ['Content-Type' => 'application/json']))
            ->withRequestTarget('/data/update/1')
            ->withMethod('PUT');
        $this->assertInstanceOf(Request::class, $request);
        $target = $request->getRequestTarget();
        $this->assertInternalType('string', $target);
        $this->assertEquals('/data/update/1', $target);
        $method = $request->getMethod();
        $this->assertInternalType('string', $method);
        $this->assertEquals('PUT', $method);
    }

    public function testCanGetUriInstance()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            ['Content-Type' => 'application/json']
        );
        $this->assertInstanceOf(Request::class, $request);
        $uri = $request->getUri();
        $this->assertInstanceOf(Uri::class, $uri);
        $path = $uri->getPath();
        $this->assertInternalType('string', $path);
        $this->assertEquals('/data/list/all', $path);
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentUriInstance()
    {
        $request = (new Request('GET', '/data/list/all', ['Content-Type' => 'application/json']))
            ->withUri(new Uri('http://localhost:1337/data/get/all?api_version=1.0'));
        $this->assertInstanceOf(Request::class, $request);
        $uri = (string)$request->getUri();
        $this->assertInternalType('string', $uri);
        $this->assertEquals('http://localhost:1337/data/get/all?api_version=1.0', $uri);
        $request = (new Request('GET', '/data/get/all', ['Content-Type' => 'application/json']))
            ->withUri(new Uri('http://localhost:1337/data/get/all?api_version=1.0'), true);
        $this->assertInstanceOf(Request::class, $request);
        $uri = (string)$request->getUri();
        $this->assertInternalType('string', $uri);
        $this->assertEquals('http://localhost:1337/data/get/all?api_version=1.0', $uri);
    }

    public function testCanGetProtocolVersion()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            ['Content-Type' => 'application/json']
        );
        $this->assertInstanceOf(Request::class, $request);
        $protocolVersion = $request->getProtocolVersion();
        $this->assertInternalType('string', $protocolVersion);
        $this->assertEquals('1.0', $protocolVersion);
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentProtocolVersion()
    {
        $request = (new Request('GET', '/data/list/all', ['Content-Type' => 'application/json']))
            ->withProtocolVersion('1.1');
        $this->assertInstanceOf(Request::class, $request);
        $protocolVersion = $request->getProtocolVersion();
        $this->assertInternalType('string', $protocolVersion);
        $this->assertEquals('1.1', $protocolVersion);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanThrowExceptionWhileGetRequestInstanceWithDifferentProtocolVersion()
    {
        $request = (new Request('GET', '/data/list/all', ['Content-Type' => 'application/json']))
            ->withProtocolVersion('13.37');
    }

    public function testCanGetHeaders()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            [
                'Host' => ['localhost:1337'],
                'Connection' => ['close'],
                'User-Agent' => ['Gandung/13.37'],
                'Content-Type' => ['application/json', 'text/plain'],
            ]
        );
        $this->assertInstanceOf(Request::class, $request);
        $headers = $request->getHeaders();
        $this->assertInternalType('array', $headers);
        $this->assertNotEmpty($headers);
    }

    public function testIfHasHeader()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            [
                'Host' => ['localhost:1337'],
                'Connection' => ['close'],
                'User-Agent' => ['Gandung/13.37'],
                'Content-Type' => ['application/json', 'text/plain']
            ]
        );
        $this->assertInstanceOf(Request::class, $request);
        $hasUserAgent = $request->hasHeader('user-agent');
        $this->assertInternalType('boolean', $hasUserAgent);
        $this->assertTrue($hasUserAgent);
    }

    public function testCanGetSpecificHeader()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            [
                'Host' => ['localhost:1337'],
                'Connection' => ['close'],
                'User-Agent' => ['Gandung/13.37'],
                'Content-Type' => ['application/json', 'text/plain']
            ]
        );
        $this->assertInstanceOf(Request::class, $request);
        $userAgent = $request->getHeader('user-agent');
        $this->assertInternalType('array', $userAgent);
        $this->assertNotEmpty($userAgent);
    }

    public function testCanGetSpecificHeaderAsLine()
    {
        $request = new Request(
            'GET',
            '/data/list/all',
            [
                'Host' => ['localhost:1337'],
                'Connection' => ['close'],
                'User-Agent' => ['Gandung/13.37'],
                'Content-Type' => ['application/json', 'text/plain']
            ]
        );
        $this->assertInstanceOf(Request::class, $request);
        $userAgent = $request->getHeaderLine('user-agent');
        $this->assertInternalType('string', $userAgent);
        $this->assertEquals('Gandung/13.37', $userAgent);
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentHTTPHeader()
    {
        $request = (new Request('GET', '/data/list/all'))
            ->withHeader('Content-Type', ['application/json']);
        $this->assertInstanceOf(Request::class, $request);
        $contentType = $request->getHeaderLine('content-type');
        $this->assertInternalType('string', $contentType);
        $this->assertEquals('application/json', $contentType);
    }

    public function testCanImmutablyGetRequestInstanceWithAddingValueToExistingHeaderKey()
    {
        $request = (new Request('GET', '/data/list/all', ['Content-Type' => ['application/json']]))
            ->withAddedHeader('content-type', 'text/plain');
        $this->assertInstanceOf(Request::class, $request);
        $contentType = $request->getHeaderLine('content-type');
        $this->assertInternalType('string', $contentType);
        $this->assertEquals('application/json,text/plain', $contentType);
    }

    public function testCanImmutablyGetRequestInstanceWithoutSpecificHeader()
    {
        $request = (new Request('GET', '/data/list/all', ['Content-Type' => ['application/json']]))
            ->withoutHeader('content-type');
        $this->assertInstanceOf(Request::class, $request);
        $contentType = $request->getHeaderLine('content-type');
        $this->assertInternalType('string', $contentType);
        $this->assertEmpty($contentType);
    }

    public function testCanGetRequestBodyInstance()
    {
        $stream = new Stream(
            fopen('php://temp', 'r+')
        );
        $this->assertInstanceOf(Stream::class, $stream);
        $stream->write(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH));
        $request = new Request(
            'GET',
            '/data/get/all',
            ['Content-Type' => ['text/plain']],
            $stream
        );
        $this->assertInstanceOf(Request::class, $request);
        $requestBody = $request->getBody();
        $this->assertInstanceOf(Stream::class, $requestBody);
        $stringableContent = (string)$requestBody;
        $this->assertInternalType('string', $stringableContent);
        $this->assertEquals(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH), $stringableContent);
        $requestBody->close();
    }

    public function testCanImmutablyGetRequestInstanceWithDifferentBodyStream()
    {
        $stream = new Stream(
            fopen('php://temp', 'r+')
        );
        $this->assertInstanceOf(Stream::class, $stream);
        $stream->write(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH));
        $request = (new Request('GET', '/data/get/all', ['Content-Type' => ['text/plain']]))
            ->withBody($stream);
        $this->assertInstanceOf(Request::class, $request);
        $requestBody = $request->getBody();
        $this->assertInstanceOf(Stream::class, $requestBody);
        $stringableContent = (string)$requestBody;
        $this->assertInternalType('string', $stringableContent);
        $this->assertEquals(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH), $stringableContent);
        $requestBody->close();
    }
}
