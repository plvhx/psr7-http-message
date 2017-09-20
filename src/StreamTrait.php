<?php

namespace Gandung\Psr7;

trait StreamTrait
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $buffer = $this->getContents();

        return $buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        fclose($this->stream);
        $this->detach();
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->stream   = $this->metadata = null;
        $this->seekable = $this->mode     = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $stat = fstat($this->stream);

        return isset($stat['size'])
            ? $stat['size']
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        $pos = ftell($this->stream);

        if ($pos === false) {
            throw new \RuntimeException(
                "Unable to get current stream position."
            );
        }

        return $pos;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException(
                "Stream is not seekable."
            );
        }

        $q = fseek($this->stream, $offset, $whence);

        if ($q === -1) {
            throw new \RuntimeException(
                sprintf(
                    "Unable to seek on current stream. Desired position: %d, Flag: %d",
                    $offset,
                    $whence
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return !preg_match('/^(r\+|w|w\+|a|a\+)(b)?$/', $this->mode)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return !preg_match('/^(r|r\+|w\+|a\+)(b)?$/', $this->mode)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        if (is_null($this->stream)) {
            throw new \RuntimeException(
                "Stream is not initialized."
            );
        }

        $q = fwrite($this->stream, $string);

        if ($q == false) {
            throw new \RuntimeException(
                "Unable to write data to current stream."
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (is_null($this->stream)) {
            throw new \RuntimeException(
                "Stream is not initialized."
            );
        }

        $content = @fread($this->stream, $length);

        if (false === $content) {
            throw new \RuntimeException(
                "Unable to read data from current stream."
            );
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->tell() > 0) {
            $this->rewind();
        }
        
        return $this->read($this->getSize());
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return is_null($key)
            ? $this->metadata
            : (isset($this->metadata[$key])
                ? $this->metadata[$key]
                : null);
    }
}
