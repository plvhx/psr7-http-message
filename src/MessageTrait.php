<?php

namespace Gandung\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait MessageTrait
{
    /**
     * @var string
     */
    protected $protocolVersion = '1.0';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        if (!$this->validateProtocolVersion($version)) {
            throw new \InvalidArgumentException(
                'Supply a valid HTTP protocol version (1.0 or 1.1)'
            );
        }

        $q = clone $this;
        $q->protocolVersion = $version;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        $headers = array_keys($this->headers);

        return in_array(
            strtolower($name),
            array_map(function ($q) {
                return strtolower($q);
            }, $headers),
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        $name = $this->normalizeHeader($name);

        return $this->headers[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        $header = $this->getHeader($name);

        return empty($header)
            ? ''
            : implode(',', $header);
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $q = clone $this;
        $q->headers[$name] = $value;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $name = $this->normalizeHeader($name);
        $q = clone $this;

        if (!isset($q->headers[$name])) {
            $q->headers[$name] = is_array($value)
                ? $value
                : [$value];
        } else {
            $q->headers[$name] = array_merge($q->headers[$name], is_array($value)
                ? $value
                : [$value]
            );
        }

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $q = clone $this;
        $name = $this->normalizeHeader($name);
        unset($q->headers[$name]);

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $q = clone $this;
        $q->body = $body;

        return $q;
    }

    protected function validateProtocolVersion($version)
    {
        return empty($version) || !preg_match('/^(1\.0|1\.1)$/', $version)
            ? false
            : true;
    }

    protected function normalizeHeader($name)
    {
        $parts = array_map(function ($q) {
            return ucfirst(strtolower($q));
        }, explode('-', $name));

        return implode('-', $parts);
    }
}
