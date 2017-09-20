<?php

namespace Gandung\Psr7;

use Psr\Http\Message\ServerRequestInterface;

class ServerRequest implements ServerRequestInterface
{
	use MessageTrait, RequestTrait;

	/**
	 * @var array
	 */
	private $serverParams;

	/**
	 * @var array
	 */
	private $cookieParams;

	/**
	 * @var array
	 */
	private $queryParams;

	/**
	 * @var array
	 */
	private $uploadedFiles;

	/**
	 * @var null|array|object
	 */
	private $parsedBody;

	/**
	 * @var array
	 */
	private $attributes = [];

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var StreamInterface
	 */
	private $stream;

	/**
	 * @var UriInterface
	 */
	private $uri;

	public function __construct(
		$uri,
		$serverParams = [],
		$cookieParams = [],
		$queryParams = [],
		$uploadedFiles = [],
		$parsedBody = null,
		$method = null,
		$headers = [],
		$body = 'php://temp'
	) {
		$this->uri = $uri;
		$this->serverParams = $serverParams;
		$this->cookieParams = $cookieParams;
		$this->queryParams = $queryParams;
		$this->uploadedFiles = $uploadedFiles;
		$this->parsedBody = $parsedBody;
		$this->method = $method;
		$this->headers = $headers;
		$this->body = $body === 'php://temp'
			? new PhpTempStream
			: ($body === 'php://input'
				? new PhpInputStream
				: $body);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getServerParams()
	{
		return $this->serverParams;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCookieParams()
	{
		return $this->cookieParams;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withCookieParams(array $cookies)
	{
		$q = clone $this;
		$q->cookieParams = $cookies;

		return $q;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryParams()
	{
		return $this->queryParams;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withQueryParams(array $query)
	{
		$q = clone $this;
		$q->queryParams = $query;

		return $q;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUploadedFiles()
	{
		return $this->uploadedFiles;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withUploadedFiles(array $uploadedFiles)
	{
		$q = clone $this;
		$q->uploadedFiles = $uploadedFiles;

		return $q;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParsedBody()
	{
		return $this->parsedBody;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withParsedBody($data)
	{
		$q = clone $this;
		$q->parsedBody = $data;

		return $q;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttribute($name, $default = null)
	{
		if (!array_key_exists($name, $this->attributes)) {
			return $default;
		}

		return $this->attributes[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function withAttribute($name, $value)
	{
		$q = clone $this;
		$q->attributes[$name] = $value;

		return $q;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withoutAttribute($name)
	{
		$q = clone $this;
		unset($q->attributes[$name]);

		return $q;
	}
}