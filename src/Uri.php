<?php

namespace Gandung\Psr7;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    use UriTrait;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * @var array
     */
    private $matched = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'telnet' => 23,
        'ssh' => 22,
        'smtp' => 25
    ];

    /**
     * Per RFC 3986(Scheme): scheme = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
     */
    const SCHEME_REGEX_PATTERN = '/^(?:[a-z]+)(?:(?:[\+\.\-]*)(?:[a-z0-9]*))$/';
    
    /**
     * Per RFC 3986(Host): host = reg-name
     */
    const HOST_REGEX_PATTERN = '(?:[\d\w\-\_]+)(?:(?:(?:\.)[\d\w\-\_]+)*)(?:(?:(?:\.)[a-z]+)*)';

    /**
     * Per RFC 3986(Host): host = IPv4address
     */
    const IPV4_REGEX_PATTERN = '(?:\d{1,3})\.(?:\d{1,3})\.(?:\d{1,3})\.(?:\d{1,3})';

    /**
     * Per RFC 3986(Port): port = *DIGIT
     */
    const PORT_REGEX_PATTERN = '/^(?:\d+)$/';

    public function __construct($uri = '')
    {
        $parsed = parse_url($uri);

        $this->scheme     = isset($parsed['scheme']) ? $parsed['scheme'] : null;
        $this->host       = isset($parsed['host']) ? $parsed['host'] : null;
        $this->port       = isset($parsed['port']) ? (int)$parsed['port'] : null;
        $this->user       = isset($parsed['user']) ? $parsed['user'] : null;
        $this->password   = isset($parsed['pass']) ? $parsed['pass'] : null;
        $this->path       = isset($parsed['path']) ? $parsed['path'] : null;
        $this->query      = isset($parsed['query']) ? $parsed['query'] : null;
        $this->fragment   = isset($parsed['fragment']) ? $parsed['fragment'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return is_null($this->scheme) ? '' : strtolower($this->scheme);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $authority = '';
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $userInfo .= empty($userInfo) || is_null($host)
            ? ''
            : '@';
        $authority .= is_null($host)
            ? ''
            : $userInfo . $host;
        $port = $this->getPort();
        $scheme = $this->getScheme();
        $authority .= !is_null($port)
            ? ':' . $port
            : '';

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        $userInfo = '';
        $userInfo .= is_null($this->user)
            ? ''
            : $this->user;
        $userInfo .= is_null($this->password)
            ? ''
            : ':' . $this->password;

        return $userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return is_null($this->host) ? '' : $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        if (is_null($this->scheme) && is_null($this->port)) {
            return null;
        }

        return (int)$this->matched[$this->scheme] === (int)$this->port
            ? null
            : (is_null($this->port)
                ? null
                : $this->port);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return is_null($this->path)
            ? '/'
            : $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return is_null($this->query)
            ? ''
            : $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return is_null($this->fragment)
            ? ''
            : $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        if (!$this->validateScheme($scheme)) {
            throw \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a valid URI scheme.", __METHOD__)
            );
        }

        $q = clone $this;
        $q->scheme = $scheme;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $q = clone $this;
        $q->user = $user;
        $q->password = is_null($password)
            ? ''
            : $password;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        if (!$this->validateHost($host)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a valid URI host.", __METHOD__)
            );
        }

        $q = clone $this;
        $q->host = $host;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port = null)
    {
        if (!$this->validatePort($port)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a valid URI port.", __METHOD__)
            );
        }

        $q = clone $this;
        $q->port = $port;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        if (!$this->validatePath($path)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a valid URI path.", __METHOD__)
            );
        }

        $q = clone $this;
        $q->path = $path;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        if (empty($query)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a valid URI query string.", __METHOD__)
            );
        }

        $q = clone $this;
        $q->query = $query;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        $q = clone $this;
        $q->fragment = $fragment;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $fullUri = '';
        $scheme = $this->getScheme();
        $fullUri .= empty($scheme) || is_null($scheme)
            ? ''
            : $this->scheme . ':';
        $authority = $this->getAuthority();
        $fullUri .= empty($authority) || is_null($authority)
            ? ''
            : '//' . $authority;
        $path = $this->getPath();
        $fullUri .= empty($path) || is_null($path)
            ? ''
            : '/' . ltrim($path, '/');
        $query = $this->getQuery();
        $fullUri .= empty($query) || is_null($query)
            ? ''
            : '?' . $query;
        $fragment = $this->getFragment();
        $fullUri .= empty($fragment) || is_null($fragment)
            ? ''
            : '#' . $fragment;

        return $fullUri;
    }
}
