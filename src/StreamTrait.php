<?php

namespace Gandung\Psr7;

trait StreamTrait
{
    /**
     * fopen() like stream permissions hashmap.
     *
     * @var array
     */
    private $permissions = [
        'readable' => [
            'r',   'rb',  'r+', 'r+b', 'rb+', 'w+',
            'w+b', 'wb+', 'a+', 'a+b', 'ab+', 'x+',
            'x+b', 'xb+', 'c+', 'c+b', 'cb+'
        ],
        'writable' => [
            'w',   'wb',  'r+', 'r+b', 'rb+', 'w+',
            'w+b', 'wb+', 'a',  'ab',  'a+',  'a+b',
            'ab+', 'x',   'xb', 'x+',  'x+b', 'xb+',
            'c',   'cb',  'c+', 'c+b', 'cb+'
        ]
    ];

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
        return in_array($this->mode, $this->permissions['writable'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return in_array($this->mode, $this->permissions['readable'], true);
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
        
        $contents = $this->read($this->getSize());

        return $contents;
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
