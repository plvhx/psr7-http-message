<?php

namespace Gandung\Psr7\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\FileStream;

class FileStreamTest extends TestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testCanRaiseExceptionWhenFileIsNotExists()
	{
		$stream = new FileStream(
			sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nonexistent_crap',
			'r+'
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanRaiseExceptionWhenOpeningStreamInWrongMode()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'r+b'
		);
	}

	public function testCanGetInstance()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
	}

	public function testCanCastStreamInstanceToString()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals(
			file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
			$content
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanCloseExistingStreamResource()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals(
			file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
			$content
		);
		$stream->close();
		$stream->read(1);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanDetachExistingStreamResource()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$content = (string)$stream;
		$this->assertInternalType('string', $content);
		$this->assertEquals(
			file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
			$content
		);
		$stream->detach();
		$stream->read(1);
	}

	public function testCanGetSizeOfExistingStreamResource()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$size = $stream->getSize();
		$this->assertInternalType('integer', $size);
		$this->assertEquals(filesize('/etc/passwd'), $size);
		$stream->close();
	}

	public function testCanGetCurrentStreamPosition()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$pos = $stream->tell();
		$this->assertInstanceOf(FileStream::class, $stream);
		$this->assertEquals(0, $pos);
		$stream->close();
	}

	public function testIfCurrentStreamCanReachEOF()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$stream->seek(filesize('/etc/passwd'));
		$stream->read(1);
		$isEOF = $stream->eof();
		$this->assertInternalType('boolean', $isEOF);
		$this->assertTrue($isEOF);
		$stream->close();
	}

	public function testIfCurrentStreamIsSeekable()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$isSeekable = $stream->isSeekable();
		$this->assertInternalType('boolean', $isSeekable);
		$this->assertTrue($isSeekable);
		$stream->close();
	}

	public function testCanSeekToAnotherOffset()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(0, $pos);
		$stream->seek(100);
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(100, $pos);
		$stream->close();
	}

	public function testCanRewindCurrentStream()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$stream->seek(200);
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(200, $pos);
		$stream->rewind();
		$pos = $stream->tell();
		$this->assertInternalType('integer', $pos);
		$this->assertEquals(0, $pos);
		$stream->close();
	}

	public function testIfCurrentStreamIsWritable()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$isWritable = $stream->isWritable();
		$this->assertInternalType('boolean', $isWritable);
		$this->assertFalse($isWritable);
		$stream->close();
	}

	public function testIfCurrentStreamIsReadable()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$isReadable = $stream->isReadable();
		$this->assertInternalType('boolean', $isReadable);
		$this->assertTrue($isReadable);
		$stream->close();
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanRaiseExceptionWhenWritingToNullStream()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$stream->close();
		$stream->write('trying to write to nonexistent stream.');
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanRaiseExceptionWhenWritingToNonWritableStream()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$stream->write('trying to write to non-writable stream.');
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCanRaiseExceptionWhenReadingFromNullStream()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$stream->close();
		$stream->read(100);
	}

	public function testCanGetOverallContentsFromCurrentStream()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$content = $stream->getContents();
		$this->assertInternalType('string', $content);
		$this->assertEquals(
			file_get_contents('/etc/passwd', FILE_USE_INCLUDE_PATH),
			$content
		);
		$stream->close();
	}

	public function testCanGetCurrentStreamMetadata()
	{
		$stream = new FileStream(
			'/etc/passwd',
			'rb'
		);
		$this->assertInstanceOf(FileStream::class, $stream);
		$metadata = $stream->getMetadata();
		$this->assertInternalType('array', $metadata);
		$this->assertNotEmpty($metadata);
	}
}