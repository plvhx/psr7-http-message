<?php

namespace Gandung\Psr7;

use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class FileStream implements StreamInterface
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

    public function __construct($filename, $mode)
    {
        if (!is_file($filename) || !file_exists($filename)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a exist filename.", __METHOD__)
            );
        }

        $this->mode = $mode;
        $this->stream = @fopen($filename, $this->mode);

        if (!is_resource($this->stream)) {
            throw new \RuntimeException(
                sprintf("Unable to open stream in '%s' mode", $this->mode)
            );
        }

        $this->metadata = stream_get_meta_data($this->stream);
        $this->seekable = isset($this->metadata['seekable'])
            ? $this->metadata['seekable']
            : null;
    }
}
