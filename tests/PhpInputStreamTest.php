<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\PhpInputStream;

class PhpInputStreamTest extends TestCase
{
    public function testCanGetInstance()
    {
        $stream = new PhpInputStream();
        $this->assertInstanceOf(PhpInputStream::class, $stream);
        $stream->close();
    }

    public function testCanCastStreamInstanceToString()
    {
        $stream = new PhpInputStream();
        $this->assertInstanceOf(PhpInputStream::class, $stream);
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEmpty($content);
        $stream->close();
    }

    public function testIfCurrentStreamIsReadable()
    {
        $stream = new PhpInputStream();
        $this->assertInstanceOf(PhpInputStream::class, $stream);
        $isReadable = $stream->isReadable();
        $this->assertInternalType('boolean', $isReadable);
        $this->assertTrue($isReadable);
        $stream->close();
    }

    public function testIfCurrentStreamIsWritable()
    {
        $stream = new PhpInputStream();
        $this->assertInstanceOf(PhpInputStream::class, $stream);
        $isWritable = $stream->isWritable();
        $this->assertInternalType('boolean', $isWritable);
        $this->assertFalse($isWritable);
        $stream->close();
    }

    public function testCanReadFromCurrentStream()
    {
        $stream = new PhpInputStream();
        $this->assertInstanceOf(PhpInputStream::class, $stream);
        $content = $stream->read(100);
        $this->assertInternalType('string', $content);
        $this->assertEmpty($content);
        $stream->close();
    }
}
