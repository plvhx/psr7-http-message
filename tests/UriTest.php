<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Uri;

class UriTest extends TestCase
{
    /**
     * @var string
     */
    private $uri = 'http://gandung:gandung123@localhost:1337/a/b/c?key=value#fragment';

    public function testCanGetInstance()
    {
        $uri = new Uri;
        $this->assertInstanceOf(Uri::class, $uri);
    }

    public function testCanGetScheme()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $scheme = $uri->getScheme();
        $this->assertInternalType('string', $scheme);
        $this->assertEquals('http', $scheme);
    }

    public function testCanGetAuthority()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $authority = $uri->getAuthority();
        $this->assertInternalType('string', $authority);
        $this->assertEquals('gandung:gandung123@localhost:1337', $authority);
    }

    public function testCanGetUserInfo()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $userInfo = $uri->getUserInfo();
        $this->assertInternalType('string', $userInfo);
        $this->assertEquals('gandung:gandung123', $userInfo);
    }

    public function testCanGetHost()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $host = $uri->getHost();
        $this->assertInternalType('string', $host);
        $this->assertEquals('localhost', $host);
    }

    public function testCanGetPort()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $port = $uri->getPort();
        $this->assertInternalType('integer', $port);
        $this->assertEquals(1337, $port);
    }

    public function testCanGetPath()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $path = $uri->getPath();
        $this->assertInternalType('string', $path);
        $this->assertEquals('/a/b/c', $path);
    }

    public function testCanGetQuery()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $query = $uri->getQuery();
        $this->assertInternalType('string', $query);
        $this->assertEquals('key=value', $query);
    }

    public function testCanGetFragment()
    {
        $uri = new Uri($this->uri);
        $this->assertInstanceOf(Uri::class, $uri);
        $fragment = $uri->getFragment();
        $this->assertInternalType('string', $fragment);
        $this->assertEquals('fragment', $fragment);
    }

    public function testCanImmutablyBuildingUri()
    {
        $uri = (new Uri())
            ->withScheme('http')
            ->withUserInfo('gandung', 'gandung123')
            ->withHost('localhost')
            ->withPort(1337)
            ->withPath('/a/b/c')
            ->withQuery('key=value')
            ->withFragment('fragment');
        $this->assertInstanceOf(Uri::class, $uri);
        $newUri = (string)$uri;
        $this->assertInternalType('string', $newUri);
        $this->assertEquals($this->uri, $newUri);
    }

    public function testCanImmutablyBuildingUriWithoutPort()
    {
        $expected = 'http://gandung:gandung123@localhost/a/b/c?key=value#fragment';
        $uri = (new Uri())
            ->withScheme('http')
            ->withUserInfo('gandung', 'gandung123')
            ->withHost('localhost')
            ->withPath('/a/b/c')
            ->withQuery('key=value')
            ->withFragment('fragment');
        $this->assertInstanceOf(Uri::class, $uri);
        $newUri = (string)$uri;
        $this->assertInternalType('string', $newUri);
        $this->assertEquals($expected, $newUri);
    }

    public function testCanImmutablyBuildingUriWithoutFragment()
    {
        $expected = 'http://gandung:gandung123@localhost:1337/a/b/c?key=value';
        $uri = (new Uri())
            ->withScheme('http')
            ->withUserInfo('gandung', 'gandung123')
            ->withHost('localhost')
            ->withPort(1337)
            ->withPath('/a/b/c')
            ->withQuery('key=value');
        $this->assertInstanceOf(Uri::class, $uri);
        $newUri = (string)$uri;
        $this->assertInternalType('string', $newUri);
        $this->assertEquals($expected, $newUri);
    }

    public function testCanImmutablyBuildingUriWithoutQueryString()
    {
        $expected = 'http://gandung:gandung123@localhost:1337/a/b/c#fragment';
        $uri = (new Uri())
            ->withScheme('http')
            ->withUserInfo('gandung', 'gandung123')
            ->withHost('localhost')
            ->withPort(1337)
            ->withPath('/a/b/c')
            ->withFragment('fragment');
        $this->assertInstanceOf(Uri::class, $uri);
        $newUri = (string)$uri;
        $this->assertInternalType('string', $newUri);
        $this->assertEquals($expected, $newUri);
    }

    public function testCanImmutablyBuildingUriWithoutUserInfo()
    {
        $expected = 'http://localhost:1337/a/b/c?key=value#fragment';
        $uri = (new Uri())
            ->withScheme('http')
            ->withHost('localhost')
            ->withPort(1337)
            ->withPath('/a/b/c')
            ->withQuery('key=value')
            ->withFragment('fragment');
        $this->assertInstanceOf(Uri::class, $uri);
        $newUri = (string)$uri;
        $this->assertInternalType('string', $newUri);
        $this->assertEquals($expected, $newUri);
    }
}
