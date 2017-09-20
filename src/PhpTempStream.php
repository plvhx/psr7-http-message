<?php

namespace Gandung\Psr7;

use Psr\Http\Message\StreamInterface;

class PhpTempStream implements StreamInterface
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

    public function __construct()
    {
        $this->stream = @fopen('php://temp', 'w+b');

        if (!is_resource($this->stream)) {
            throw new \RuntimeException(
                "Unable to initialize stream resource"
            );
        }

        $this->metadata = stream_get_meta_data($this->stream);
        $this->mode = isset($this->metadata['mode'])
            ? $this->metadata['mode']
            : null;
        $this->seekable = isset($this->metadata['seekable'])
            ? $this->metadata['seekable']
            : null;
    }
}
