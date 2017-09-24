<?php

namespace Gandung\Psr7;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @var array
     */
    private $errors = [
        \UPLOAD_ERR_OK => 'No error, file uploaded successfully.',
        \UPLOAD_ERR_INI_SIZE =>
            'Size of the uploaded file is larger than \'upload_max_filesize\' directive in php.ini',
        \UPLOAD_ERR_FORM_SIZE =>
            'Size of the uploaded file is larger than MAX_FILE_SIZE directive that was specified in HTML form.',
        \UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        \UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        \UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        \UPLOAD_ERR_CANT_WRITE => 'Unable to write contents of the specified file to disk.',
        \UPLOAD_ERR_EXTENSION => 'Unable to upload file due to PHP extension.'
    ];

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $clientFile;

    /**
     * @var boolean
     */
    private $hasMoved;

    /**
     * @var integer
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var string
     */
    private $mediaType;

    /**
     * @var StreamInterface
     */
    private $stream;

    public function __construct(
        $file,
        $clientFile,
        $error = null,
        $size = null,
        $mediaType = 'text/plain'
    ) {
        $this->file = $file;
        $this->clientFile = $clientFile;
        $this->errorCode = $error !== null
            ? $error
            : \UPLOAD_ERR_OK;
        $this->errorMessage = $this->errors[$this->errorCode];
        $this->size = $size !== null
            ? $size
            : $this->getSize();
        $this->mediaType = $mediaType;
        $this->stream = $this->file instanceof StreamInterface
            ? $this->file
            : new FileStream($this->file, 'rb');
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if ($this->errorCode !== \UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to retrieve stream due to upload error. Code: %d, Message: %s',
                    $this->errorCode,
                    $this->errorMessage
                )
            );
        }

        if ($this->hasMoved) {
            throw new \RuntimeException(
                "Unable to retrieve stream. File has been already moved."
            );
        }

        if (!($this->stream instanceof StreamInterface)) {
            $this->stream = new FileStream($this->file, 'r+b');
        }

        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath)
    {
        if ($this->hasMoved) {
            throw new \RuntimeException(
                "File has been already moved."
            );
        }

        if ($this->errorCode !== \UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                sprintf(
                    "Unable to retrieve stream due to upload error. Code: %d, Message: %s",
                    $this->errorCode,
                    $this->errorMessage
                )
            );
        }

        if (!is_string($targetPath) || empty($targetPath)) {
            throw new \InvalidArgumentException(
                sprintf("Parameter 1 of %s require a non-empty string.", __METHOD__)
            );
        }

        $targetDir = dirname($targetPath);

        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            throw new \RuntimeException(
                sprintf(
                    'Directory %s is not exists or not writable.',
                    $targetDir
                )
            );
        }

        $phpSapiEnv = \PHP_SAPI;

        switch (true) {
            case (empty($phpSapiEnv) || strpos($phpSapiEnv, 'cli') === 0 || !$this->file):
                $this->writeIntoFile($targetPath);
                break;
            default:
                if (move_uploaded_file($this->file, $targetPath) === false) {
                    throw new \RuntimeException('An error occurred when uploading file.');
                }
                break;
        }

        $this->hasMoved = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $size = filesize($this->file);

        return is_int($this->size)
            ? $this->size
            : ($size !== false
                ? $size
                : null);
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->clientFile;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->mediaType;
    }

    private function writeIntoFile($path)
    {
        $handler = @fopen($path, 'wb');

        if (!is_resource($handler)) {
            throw new \RuntimeException(
                "Unable to write into defined path."
            );
        }

        $stream = $this->getStream();

        if (!($stream instanceof StreamInterface)) {
            throw new \RuntimeException(
                "Unable to get stream resource."
            );
        }

        $content = $stream->getContents();
        $bytesWritten = fwrite($handler, $content);

        if (!$bytesWritten) {
            throw new \RuntimeException(
                "An error occurred when trying to write into defined path."
            );
        }

        fclose($handler);
    }
}
