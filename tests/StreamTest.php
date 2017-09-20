<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Stream;

class StreamTest extends TestCase
{
	public function testCanGetInstance()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
	}

	public function testCanCastInstanceToString()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write('this is a foo.');
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals('this is a foo.', $content);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanCloseStreamAndThrowExceptionWhenReused()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write('this is a foo.');
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals('this is a foo.', $content);
		$stream->close();
		$content = $stream->read(100);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanDetachStreamAndThrowExceptionWhenReused()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write('this is a foo.');
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals('this is a foo.', $content);
		$stream->detach();
		$content = $stream->read(100);
	}

	public function testCanGetSizeOfCurrentStream()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write('this is a foo.');
		$size = $stream->getSize();
		$this->assertInternalType('integer', $size);
		$this->assertEquals(14, $size);
		$stream->close();
	}

	public function testCanTellCurrentStreamPosition()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH));
		$stream->rewind();
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(0, $pos);
		$stream->seek(100);
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(100, $pos);
		$stream->close();
	}

	public function testCanTellCurrentStreamWasReachedEOF()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH));
		$stream->rewind();
		$stream->seek(filesize('/etc/passwd'));
		$stream->read(1);
		$isEOF = $stream->eof();
		$this->assertInternalType('boolean', $isEOF);
		$this->assertTrue($isEOF);
		$stream->close();
	}

	public function testIfCurrentStreamIsSeekable()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$isSeekable = $stream->isSeekable();
		$this->assertInternalType('boolean', $isSeekable);
		$this->assertTrue($isSeekable);
		$stream->close();
	}

	public function testCanSeekIntoAnotherOffset()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$stream->write(file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH));
		$stream->rewind();
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(0, $pos);
		$stream->seek(100);
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(100, $pos);
		$stream->close();
	}

	public function testIfCurrentStreamIsWritable()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$isWritable = $stream->isWritable();
		$this->assertInternalType('boolean', $isWritable);
		$this->assertTrue($isWritable);
	}

	public function testIfCurrentStreamIsReadable()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$isReadable = $stream->isReadable();
		$this->assertInternalType('boolean', $isReadable);
		$this->assertTrue($isReadable);
	}

	public function testIfCanGetCurrentStreamMetadata()
	{
		$stream = new Stream(
			fopen('php://temp', 'r+')
		);
		$this->assertInstanceOf(Stream::class, $stream);
		$metadata = $stream->getMetadata();
		$this->assertInternalType('array', $metadata);
	}
}