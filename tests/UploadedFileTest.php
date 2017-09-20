<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\UploadedFile;

class UploadedFileTest extends TestCase
{
    public function testCanGetInstance()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanRaiseExceptionWhileGettingStreamInstanceWhenFileNotUploaded()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_NO_FILE,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $stream = $uploadedFile->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanRaiseExceptionWhileGettingStreamInstanceWhenFileHasBeenMoved()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $uploadedFile->moveTo('/tmp/passwd');
        $stream = $uploadedFile->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanRaiseExceptionWhileDoSubsequentCallMoveToMethod()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $uploadedFile->moveTo('/tmp/passwd');
        $uploadedFile->moveTo('/tmp/passwd');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanRaiseExceptionWhenCallMoveToMethodFileNotUploaded()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_NO_FILE,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $uploadedFile->moveTo('/tmp/passwd');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanRaiseExceptionWhenCallMoveToMethodInvalidTargetPath()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $uploadedFile->moveTo(null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanRaiseExceptionWhenCallMoveToMethodNonexistentDirOrNotWritable()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $uploadedFile->moveTo('/nonexistent/path/passwd');
    }

    public function testCanGetSize()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $size = $uploadedFile->getSize();
        $this->assertInternalType('integer', $size);
        $this->assertEquals(filesize('/etc/passwd'), $size);
    }

    public function testCanGetErrorCode()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/temp/nonexistent',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $errorCode = $uploadedFile->getError();
        $this->assertInternalType('integer', $errorCode);
        $this->assertEquals(0, $errorCode);
    }

    public function testCanGetClientFilename()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $clientFilename = $uploadedFile->getClientFilename();
        $this->assertInternalType('string', $clientFilename);
        $this->assertEquals('/tmp/passwd', $clientFilename);
    }

    public function testCanGetClientMediaType()
    {
        $uploadedFile = new UploadedFile(
            '/etc/passwd',
            '/tmp/passwd',
            \UPLOAD_ERR_OK,
            filesize('/etc/passwd')
        );
        $this->assertInstanceOf(UploadedFile::class, $uploadedFile);
        $clientMediaType = $uploadedFile->getClientMediaType();
        $this->assertInternalType('string', $clientMediaType);
        $this->assertEquals('text/plain', $clientMediaType);
    }
}
