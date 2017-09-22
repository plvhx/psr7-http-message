<?php

namespace Gandung\Psr7;

use Psr\Http\Message\UriInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait RequestTrait
{
    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        if (!is_null($this->requestTarget)) {
            return $this->requestTarget;
        }

        $composed = '';
        $path = $this->uri->getPath();

        if ($path !== '') {
            $composed .= $path;
        }

        $query = $this->uri->getQuery();

        if ($query !== '') {
            $composed .= $query;
        }

        return $composed !== ''
            ? $composed
            : '/';
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $q = clone $this;
        $q->requestTarget = $requestTarget;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $q = clone $this;
        $q->method = $method;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $q = clone $this;
        $q->uri = $uri;

        $q->rearrangeHostHeaderKey();

        if ($preserveHost) {
            if (!isset($q->headers['Host'])) {
                $parts = [
                    'host' => $q->uri->getHost(),
                    'port' => $q->uri->getPort()
                ];
                $host  = $parts['host'] === ''
                    ? ''
                    : $parts['host'];
                $host .= is_null($parts['port'])
                    ? ''
                    : (string)$parts['port'];
                $q->headers['Host'] = [$host];
            }
        } else {
            $q->changeHostFromUri();
        }

        return $q;
    }

    /**
     * Rearrange 'Host' value of current HTTP header into first entry.
     */
    private function rearrangeHostHeaderKey()
    {
        if (!isset($this->headers['Host'])) {
            return;
        }

        $tmp = $this->headers['Host'];
        $keys = array_keys($this->headers);
        $hostIndex = array_search('Host', $keys, true);

        if (false === $hostIndex) {
            return;
        }

        array_splice($this->headers, $hostIndex, 1);

        $this->headers = ['Host' => $tmp] + $this->headers;
    }

    /**
     * If this object is considered immutable and original host is not preserved,
     * better change it to the new one.
     */
    private function changeHostFromUri()
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        $this->headers['Host'] = [$host];

        $this->rearrangeHostHeaderKey();
    }
}
