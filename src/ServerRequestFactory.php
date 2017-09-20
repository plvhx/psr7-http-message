<?php

namespace Gandung\Psr7;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UploadedFileInterface;
use Gandung\Psr7\Uri;

class ServerRequestFactory
{
	public static function createFromGlobals(
		$server = null,
		$query = null,
		$body = null,
		$cookies = null,
		$files = null
	) {
		$header = self::aggregateHeaderFromServer($server ?: $_SERVER);
		$uri = self::aggregateUriFromServer($server ?: $_SERVER);
		$files = self::normalizeUploadedFile($files ?: $_FILES);

		return new ServerRequest(
			$uri,
			$server ?: $_SERVER,
			$cookies ?: $_COOKIE,
			$query ?: $_GET,
			$files,
			$body ?: $_POST,
			$server ? $server['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'],
			$header
		);
	}

	/**
	 * Aggregate request header information from server variables.
	 *
	 * @param $server The server variables
	 * @return array
	 */
	private static function aggregateHeaderFromServer($server = [])
	{
		$header = [];

		if (isset($server['HTTP_HOST'])) {
			$header['Host'] = $server['HTTP_HOST'];
		}

		if (isset($server['HTTP_USER_AGENT'])) {
			$header['User-Agent'] = $server['HTTP_USER_AGENT'];
		}

		if (isset($server['HTTP_CONNECTION'])) {
			$header['Connection'] = $server['HTTP_CONNECTION'];
		}

		if (isset($server['HTTP_ACCEPT_ENCODING'])) {
			$header['Accept-Encoding'] = $server['HTTP_ACCEPT_ENCODING'];
		}

		if (isset($server['HTTP_ACCEPT_LANGUAGE'])) {
			$header['Accept-Language'] = $server['HTTP_ACCEPT_LANGUAGE'];
		}

		if (isset($server['HTTP_CACHE_CONTROL'])) {
			$header['Cache-Control'] = $server['HTTP_CACHE_CONTROL'];
		}

		if (isset($server['HTTP_ACCEPT'])) {
			$header['Accept'] = $server['HTTP_ACCEPT'];
		}

		return $header;
	}

	/**
	 * Get HTTP protocol version from server variables.
	 *
	 * @param $server The server variables.
	 * @return string
	 */
	private static function aggregateHttpProtocolVersionFromServer($server)
	{
		if (!isset($server['SERVER_PROTOCOL'])) {
			return null;
		}

		$pattern = '/^(?P<scheme>HTTP)\/(?P<protocol_version>1\.(0|1))$/';

		if (!preg_match($pattern, $server['SERVER_PROTOCOL'], $q)) {
			return null;
		}

		return $q['protocol_version'];
	}

	/**
	 * Aggregate URI component from server variables.
	 *
	 * @param $server The server variables.
	 * @return UriInterface
	 */
	private static function aggregateUriFromServer($server)
	{
		$uri = new Uri();

		$scheme = 'http';
		$uri = $uri->withScheme($scheme);

		if (isset($server['SERVER_NAME'])) {
			$uri = $uri->withHost($server['SERVER_NAME']);
		}

		if (isset($server['SERVER_PORT'])) {
			$uri = $uri->withPort((int)$server['SERVER_PORT']);
		}

		if (isset($server['REQUEST_URI'])) {
			$q = explode('?', $server['REQUEST_URI']);

			if (isset($q[0])) {
				$uri = $uri->withPath($q[0]);
			}
		}

		if (isset($server['QUERY_STRING'])) {
			$uri = $uri->withQuery($server['QUERY_STRING']);
		}

		return $uri;
	}

	public static function normalizeUploadedFile($files)
	{
		$normalized = [];

		foreach ($files as $key => $value) {
			if ($value instanceof UploadedFileInterface) {
				$normalized[$key] = $value;
				continue;
			}

			if (is_array($value) && isset($value['tmp_name'])) {
				$normalized[$key] = self::createNestedUploadedFile($value);
				continue;
			}

			if (is_array($value)) {
				$normalized[$key] = self::normalizeUploadedFile($value);
				continue;
			}
		}

		return $normalized;
	}

	private static function createNestedUploadedFile($files)
	{
		$grouped = [];

		foreach (array_keys($files['tmp_name']) as $key) {
			$grouped[] = new UploadedFile(
				$files['tmp_name'][$key],
				$files['name'][$key],
				$files['error'][$key],
				$files['size'][$key],
				$files['type'][$key]
			);
		}

		return $grouped;
	}
}