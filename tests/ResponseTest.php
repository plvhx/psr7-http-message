<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Response;
use Gandung\Psr7\Stream;

class ResponseTest extends TestCase
{
    public function testCanGetInstance()
    {
        $response = new Response(
            file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
            200,
            ['Content-Type' => ['text/plain']]
        );
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testCanGetStatusCode()
    {
        $response = new Response(
            file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
            200,
            ['Content-Type' => ['text/plain']]
        );
        $this->assertInstanceOf(Response::class, $response);
        $statusCode = $response->getStatusCode();
        $this->assertInternalType('integer', $statusCode);
        $this->assertEquals(200, $statusCode);
        $reason = $response->getReasonPhrase();
        $this->assertInternalType('string', $reason);
        $this->assertEquals('OK', $reason);
    }

    public function testCanImmutablyGetResponseObjectWithDifferentStatusCodeAndReasonPhrase()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = ['Content-Type' => ['text/plain']];
        $response = (new Response($body, $httpCode, $header))
            ->withStatus($httpCode, 'OK asshole, get it done..');
        $this->assertInstanceOf(Response::class, $response);
        $statusCode = $response->getStatusCode();
        $this->assertInternalType('integer', $statusCode);
        $this->assertEquals(200, $statusCode);
        $reason = $response->getReasonPhrase();
        $this->assertInternalType('string', $reason);
        $this->assertEquals('OK asshole, get it done..', $reason);
    }

    public function testCanGetProtocolVersion()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = ['Content-Type' => ['text/plain']];
        $response = new Response($body, $httpCode, $header);
        $this->assertInstanceOf(Response::class, $response);
        $protocolVersion = $response->getProtocolVersion();
        $this->assertInternalType('string', $protocolVersion);
        $this->assertEquals('1.0', $protocolVersion);
    }

    public function testCanImmutablyGetResponseObjectWithDifferentProtocolVersion()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = ['Content-Type' => ['text/plain']];
        $response = (new Response($body, $httpCode, $header))
            ->withProtocolVersion('1.1');
        $this->assertInstanceOf(Response::class, $response);
        $protocolVersion = $response->getProtocolVersion();
        $this->assertInternalType('string', $protocolVersion);
        $this->assertEquals('1.1', $protocolVersion);
    }

    public function testCanGetHeaders()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = [
            'Content-Type' => ['text/plain', 'application/json'],
            'User-Agent' => ['Gandung/13.37']
        ];
        $response = new Response($body, $httpCode, $header);
        $this->assertInstanceOf(Response::class, $response);
        $headers = $response->getHeaders();
        $this->assertInternalType('array', $headers);
        $this->assertNotEmpty($headers);
    }

    public function testIfHasHeader()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = [
            'Content-Type' => ['text/plain', 'application/json'],
            'User-Agent' => ['Gandung/13.37']
        ];
        $response = new Response($body, $httpCode, $header);
        $this->assertInstanceOf(Response::class, $response);
        $hasUserAgent = $response->hasHeader('user-agent');
        $this->assertInternalType('boolean', $hasUserAgent);
        $this->assertTrue($hasUserAgent);
    }

    public function testCanGetHeader()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = [
            'Content-Type' => ['text/plain', 'application/json'],
            'User-Agent' => ['Gandung/13.37']
        ];
        $response = new Response($body, $httpCode, $header);
        $this->assertInstanceOf(Response::class, $response);
        $userAgent = $response->getHeader('user-agent');
        $this->assertInternalType('array', $userAgent);
        $this->assertNotEmpty($userAgent);
    }

    public function testCanGetHeaderAsLine()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $header = [
            'Content-Type' => ['text/plain', 'application/json'],
            'User-Agent' => ['Gandung/13.37']
        ];
        $response = new Response($body, $httpCode, $header);
        $this->assertInstanceOf(Response::class, $response);
        $userAgent = $response->getHeaderLine('user-agent');
        $this->assertInternalType('string', $userAgent);
        $this->assertEquals('Gandung/13.37', $userAgent);
    }

    public function testCanImmutablyGetResponseObjectWithDifferentHeader()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $response = (new Response($body, $httpCode))
            ->withHeader('User-Agent', ['Gandung/13.37']);
        $this->assertInstanceOf(Response::class, $response);
        $userAgent = $response->getHeaderLine('user-agent');
        $this->assertInternalType('string', $userAgent);
        $this->assertEquals('Gandung/13.37', $userAgent);
    }

    public function testCanImmutablyGetResponseObjectWithAppendedHeaderValue()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $headers = ['Content-Type' => ['text/plain']];
        $response = (new Response($body, $httpCode, $headers))
            ->withAddedHeader('Content-Type', 'application/json');
        $this->assertInstanceOf(Response::class, $response);
        $contentType = $response->getHeaderLine('content-type');
        $this->assertInternalType('string', $contentType);
        $this->assertEquals('text/plain,application/json', $contentType);
    }

    public function testCanImmutablyGetResponseObjectWithoutSpecificHeader()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $headers = [
            'User-Agent' => ['Gandung/13.37'],
            'Content-Type' => ['text/plain', 'application/json']
        ];
        $response = (new Response($body, $httpCode, $headers))
            ->withoutHeader('content-type');
        $this->assertInstanceOf(Response::class, $response);
        $contentType = $response->getHeaderLine('content-type');
        $this->assertInternalType('string', $contentType);
        $this->assertEmpty($contentType);
    }

    public function testCanGetResponseBodyStream()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $response = new Response($body, $httpCode);
        $this->assertInstanceOf(Response::class, $response);
        $stream = $response->getBody();
        $this->assertInstanceOf(Stream::class, $stream);
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEquals($body, $content);
    }

    public function testCanImmutablyGetResponseObjectWithDifferentBodyStream()
    {
        $body = file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH);
        $httpCode = 200;
        $response = (new Response($body, $httpCode))
            ->withBody(new Stream(fopen('php://temp', 'r+b')));
        $this->assertInstanceOf(Response::class, $response);
        $stream = $response->getBody();
        $this->assertInstanceOf(Stream::class, $stream);
        $stream->write($body);
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEquals($body, $content);
    }
}
