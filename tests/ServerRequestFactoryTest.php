<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\ServerRequestFactory;
use Gandung\Psr7\PhpInputStream;
use Gandung\Psr7\PhpTempStream;
use Gandung\Psr7\UploadedFile;
use Gandung\Psr7\ServerRequest;

class ServerRequestFactoryTest extends TestCase
{
	public function serverSapiDataProvider()
	{
		$server = [
			'DOCUMENT_ROOT' => '/Users/rvn.plvhx/learn/php/http-message',
			'REMOTE_ADDR' => '::1',
			'REMOTE_PORT' => '56992',
			'SERVER_SOFTWARE' => 'PHP 5.6.31 Development Server',
			'SERVER_PROTOCOL' => 'HTTP/1.1',
			'SERVER_NAME' => 'localhost',
			'SERVER_PORT' => 1337,
			'REQUEST_URI' => '/tests.php?a=1&b=3',
			'REQUEST_METHOD' => 'GET',
			'SCRIPT_NAME' => '/tests.php',
			'SCRIPT_FILENAME' => '/Users/rvn.plvhx/learn/php/http-message/tests.php',
			'PHP_SELF' => '/tests.php',
			'QUERY_STRING' => 'a=1&b=3',
			'HTTP_HOST' => 'localhost:1337',
			'HTTP_CONNECTION' => 'keep-alive',
			'HTTP_CACHE_CONTROL' => 'max-age=0',
			'HTTP_UPGRADE_INSECURE_REQUESTS' => 1,
			'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
			'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br',
			'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
			'REQUEST_TIME_FLOAT' => 1505736340.9719,
			'REQUEST_TIME' => 1505736340
		];

		return [$server];
	}

	public function cookieDataProvider()
	{
		$digestMethod = openssl_get_md_methods();
		$cookie = [];
		$cookie[] = [
		[
			'raw.cookie.1' => openssl_digest(uniqid(), $digestMethod[rand(0, sizeof($digestMethod) - 1)]),
			'raw.cookie.2' => openssl_digest(uniqid(), $digestMethod[rand(0, sizeof($digestMethod) - 1)]),
			'raw.cookie.3' => openssl_digest(uniqid(), $digestMethod[rand(0, sizeof($digestMethod) - 1)])
		]];

		return $cookie;
	}

	public function phpSapiFileDataProvider()
	{
		$files = [
			'foo' => [
				'name' => [
                	'/tmp/passwd',
                	'/tmp/info.php'
                ],
        		'type' => [
        			'text/plain',
        			'text/plain'
        		],
        		'tmp_name' => [
        			'/etc/passwd',
        			'/Applications/MAMP/htdocs/info.php'
        		],
        		'error' => [
        			\UPLOAD_ERR_OK,
        			\UPLOAD_ERR_OK
        		],
        		'size' => [
        			filesize('/etc/passwd'),
        			filesize('/Applications/MAMP/htdocs/info.php')
        		]
        	]
		];

		return [$files];
	}


	public function requirementsDataProvider()
	{
		$requirement = [
			'server' => $this->serverSapiDataProvider()[0],
			'cookie' => $this->cookieDataProvider()[0],
			'files' => $this->phpSapiFileDataProvider()[0]
		];

		return [$requirement];
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetServerRequestInterfaceInstance($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetServerParams($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$serverParams = $request->getServerParams();
		$this->assertInternalType('array', $serverParams);
		$this->assertEquals($server, $serverParams);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetCookieParams($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$fetchedCookie = $request->getCookieParams();
		$this->assertInternalType('array', $fetchedCookie);
		$this->assertEquals($cookie, $fetchedCookie);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithDifferentCookie($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			[],
			$files
		);
		$request = $request->withCookieParams($cookie);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$fetchedCookie = $request->getCookieParams();
		$this->assertInternalType('array', $fetchedCookie);
		$this->assertEquals($cookie, $fetchedCookie);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetQueryParameters($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$fetchedQuery = $request->getQueryParams();
		$this->assertInternalType('array', $fetchedQuery);
		$this->assertEquals($query, $fetchedQuery);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithDifferentQueryParams(
		$server,
		$cookie,
		$files
	) {
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			null,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$request = $request->withQueryParams($query);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$fetchedQuery = $request->getQueryParams();
		$this->assertInternalType('array', $fetchedQuery);
		$this->assertEquals($query, $fetchedQuery);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetUploadedFiles($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$uploadedFiles = $request->getUploadedFiles();
		$this->assertInternalType('array', $uploadedFiles);
		$this->assertInstanceOf(UploadedFile::class, $uploadedFiles['foo'][0]);
		$this->assertInstanceOf(UploadedFile::class, $uploadedFiles['foo'][1]);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithDifferentUploadedFile(
		$server,
		$cookie,
		$files
	) {
		$query = ['a' => 1, 'b' => '3'];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			null
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$request = $request->withUploadedFiles(
			ServerRequestFactory::normalizeUploadedFile($files)
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$uploadedFiles = $request->getUploadedFiles();
		$this->assertInternalType('array', $uploadedFiles);
		$this->assertInstanceOf(UploadedFile::class, $uploadedFiles['foo'][0]);
		$this->assertInstanceOf(UploadedFile::class, $uploadedFiles['foo'][1]);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetParsedBody($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$body = $request->getParsedBody();
		$this->assertInstanceOf(PhpInputStream::class, $body);
		$content = (string)$body;
		$this->assertInternalType('string', $content);
		$this->assertEmpty($content);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithDifferentBody(
		$server,
		$cookie,
		$files
	) {
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			null,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$request = $request->withParsedBody($stream);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$body = $request->getParsedBody();
		$this->assertInstanceOf(PhpInputStream::class, $body);
		$content = (string)$body;
		$this->assertInternalType('string', $content);
		$this->assertEmpty($content);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetAttributes($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$attributes = $request->getAttributes();
		$this->assertInternalType('array', $attributes);
		$this->assertEmpty($attributes);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanGetAttribute($server, $cookie, $files)
	{
		$query = ['a' => 1, 'b' => 3];
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$attribute = $request->getAttribute('foo');
		$this->assertNull($attribute);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithNewAttribute(
		$server,
		$cookie,
		$files
	) {
		$query = ['a' => 1, 'b' => 3];
		$data = md5(uniqid());
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$request = $request->withAttribute('foo', $data);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$attribute = $request->getAttribute('foo');
		$this->assertInternalType('string', $attribute);
		$this->assertEquals($attribute, $data);
		$stream->close();
	}

	/**
	 * @dataProvider requirementsDataProvider
	 * @param $server
	 * @param $cookie
	 * @param $files
	 */
	public function testCanImmutablyGetServerRequestInstanceWithoutSpecifiedAttribute(
		$server,
		$cookie,
		$files
	) {
		$query = ['a' => 1, 'b' => 3];
		$data = md5(uniqid());
		$stream = new PhpInputStream;
		$this->assertInstanceOf(PhpInputStream::class, $stream);
		$request = ServerRequestFactory::createFromGlobals(
			$server,
			$query,
			$stream,
			$cookie,
			$files
		);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$request = $request->withAttribute('foo', $data);
		$this->assertInstanceOf(ServerRequest::class, $request);
		$foo = $request->getAttribute('foo');
		$this->assertInternalType('string', $foo);
		$this->assertEquals($data, $foo);
		$request = $request->withoutAttribute('foo');
		$this->assertInstanceOf(ServerRequest::class, $request);
		$nonexistentFoo = $request->getAttribute('foo');
		$this->assertNull($nonexistentFoo);
		$stream->close();
	}
}