<?php

namespace Gandung\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class PhpInputStream implements StreamInterface
{
    use StreamTrait;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var boolean
     */
    private $seekable;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var string
     */
    private $cache = '';

    public function __construct()
    {
        $this->stream = @fopen('php://input', 'rb');
        $this->metadata = stream_get_meta_data($this->stream);
        $this->mode = isset($this->metadata['mode'])
            ? $this->metadata['mode']
            : null;
        $this->seekable = isset($this->metadata['seekable'])
            ? $this->metadata['seekable']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ($this->eof()) {
            return $this->cache;
        }

        $buffer = $this->getContents();

        return $buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $content = fread($this->stream, $length);

        if (!$this->eof()) {
            $this->cache .= $content;
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $content = stream_get_contents(
            $this->stream,
            $this->getSize() - strlen($this->cache),
            $this->tell()
        );

        $this->cache .= $content;

        return $content;
    }
}
