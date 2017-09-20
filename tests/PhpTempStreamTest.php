<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\PhpTempStream;

class PhpTempStreamTest extends TestCase
{
    public function testCanGetInstance()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
    }

    public function testCanCastStreamInstanceToString()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write('this is a text.');
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEquals('this is a text.', $content);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanCloseStreamInstance()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write('this is a text.');
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEquals('this is a text.', $content);
        $stream->close();
        $stream->read(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanDetachStreamInstance()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write('this is a text.');
        $content = (string)$stream;
        $this->assertInternalType('string', $content);
        $this->assertEquals('this is a text.', $content);
        $stream->detach();
        $stream->read(1);
    }

    public function testCanGetSizeOfStreamInstance()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $size = $stream->getSize();
        $this->assertInternalType('integer', $size);
        $this->assertEquals(strlen($buffer), $size);
        $stream->close();
    }

    public function testCanTellCurrentStreamPosition()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $pos = $stream->tell();
        $this->assertInternalType('integer', $pos);
        $this->assertEquals(0, $pos);
        $stream->close();
    }

    public function testIfCurrentStreamCanReachEOF()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $stream->seek(strlen($buffer));
        $stream->read(1);
        $isEOF = $stream->eof();
        $this->assertInternalType('boolean', $isEOF);
        $this->assertTrue($isEOF);
        $stream->close();
    }

    public function testIfCurrentStreamIsSeekable()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $isSeekable = $stream->isSeekable();
        $this->assertInternalType('boolean', $isSeekable);
        $this->assertTrue($isSeekable);
        $stream->close();
    }

    public function testCanSeekToAnotherPosition()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $stream->rewind();
        $pos = $stream->tell();
        $this->assertInternalType('integer', $pos);
        $this->assertEquals(0, $pos);
        $newPos = rand(0, strlen($buffer) - 1);
        $stream->seek($newPos);
        $pos = $stream->tell();
        $this->assertInternalType('integer', $pos);
        $this->assertEquals($newPos, $pos);
        $stream->close();
    }

    public function testCanRewindCurrentStream()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $pos = $stream->tell();
        $this->assertInternalType('integer', $pos);
        $this->assertEquals(strlen($buffer), $pos);
        $stream->rewind();
        $pos = $stream->tell();
        $this->assertInternalType('integer', $pos);
        $this->assertEquals(0, $pos);
        $stream->close();
    }

    public function testIfCurrentStreamIsWritable()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $isWritable = $stream->isWritable();
        $this->assertInternalType('boolean', $isWritable);
        $this->assertTrue($isWritable);
        $stream->close();
    }

    public function testIfCurrentStreamIsReadable()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $isReadable = $stream->isReadable();
        $this->assertInternalType('boolean', $isReadable);
        $this->assertTrue($isReadable);
        $stream->close();
    }

    public function testCanWriteDataToStream()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $size = $stream->getSize();
        $this->assertInternalType('integer', $size);
        $this->assertEquals(strlen($buffer), $size);
        $stream->close();
    }

    public function testCanReadDataFromStream()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $stream->rewind();
        $content = $stream->read(strlen($buffer));
        $this->assertInternalType('string', $content);
        $this->assertEquals($buffer, $content);
        $stream->close();
    }

    public function testCanGetContentsFromStream()
    {
        $buffer = 'this is a text.';
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $stream->write($buffer);
        $content = $stream->getContents();
        $this->assertInternalType('string', $content);
        $this->assertEquals($buffer, $content);
        $stream->close();
    }

    public function testCanGetMetadataFromStream()
    {
        $stream = new PhpTempStream();
        $this->assertInstanceOf(PhpTempStream::class, $stream);
        $metadata = $stream->getMetadata();
        $this->assertInternalType('array', $metadata);
        $this->assertNotEmpty($metadata);
        $stream->close();
    }
}
