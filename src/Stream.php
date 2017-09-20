<?php

namespace Gandung\Psr7;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
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

	public function __construct($stream)
	{
		$this->stream   = $stream;
		$this->metadata = stream_get_meta_data($stream);
		$this->seekable = isset($this->metadata['seekable'])
			? $this->metadata['seekable']
			: null;
		$this->mode     = isset($this->metadata['mode'])
			? $this->metadata['mode']
			: null;
	}
}