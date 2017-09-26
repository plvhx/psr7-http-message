# PSR-7 HTTP Message

[![Build Status](https://travis-ci.org/plvhx/psr7-http-message.svg?branch=master)](https://travis-ci.org/plvhx/psr7-http-message)

This is [PSR-7 HTTP message](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md) implementation in PHP.

# Response

## Initiate a response

### With string as body parameter

```
use Gandung\Psr7\Response;

$content = 'this is a text.';
$response = new Response($content, 200, ['Content-Type' => 'text/plain']);

echo sprintf("%s\n", $response);
```

### With object which implements ```Psr\Http\Message\StreamInterface```

```
use Gandung\Psr7\PhpTempStream;
use Gandung\Psr7\Response;

$content = 'this is a text.';
$stream = new PhpTempStream;
$stream->write($content);
$response = new Response($stream, 200, ['Content-Type' => 'text/plain']);

echo sprintf("%s\n", $response);
```

### With local (fopen() like function) or remote (fsockopen() like function) stream resource

```
use Gandung\Psr7\Response;

$handler = fopen('php://temp', 'r+b');
fseek($handler, 0);
fwrite($handler, 'this is a text.');
$response = new Response($handler, 200, ['Content-Type' => 'text/plain']);

echo sprintf("%s\n", $response);
```

# Redirect Response

## Initiating HTTP redirect response

### With URI string

```
use Gandung\Psr7\Response\RedirectResponse;

$response = new RedirectResponse('http://example.com/a/b/c?api_version=1.0');
```

### With URI object

```
use Gandung\Psr7\Uri;
use Gandung\Psr7\Response\RedirectResponse;

$uri = (new Uri())
	->withScheme('http')
	->withHost('example.com')
	->withPath('/a/b/c')
	->withQuery('api_version=1.0');
$response = new RedirectResponse($uri);
```

# Empty Response

## Initiating HTTP empty response

```
use Gandung\Psr7\Response\EmptyResponse;

$response = new EmptyResponse;
```

# Request

## Initiating a request

### With URI string

```
use Gandung\Psr7\Request;

$request = new Request('GET', 'http://example.com/a/b/c?api_version=1.0');
```

### With URI object

```
use Gandung\Psr7\Request;
use Gandung\Psr7\Uri;

$uri = (new Uri())
	->withScheme('http')
	->withHost('example.com')
	->withPath('/a/b/c')
	->withQuery('api_version=1.0');
$request = new Request('GET', $uri);
```
