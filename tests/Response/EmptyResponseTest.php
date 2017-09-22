<?php

namespace Gandung\Psr7\Tests\Response;

use PHPUnit\Framework\TestCase;
use Gandung\Psr7\Response\EmptyResponse;

class EmptyResponseTest extends TestCase
{
	public function testCanGetInstance()
	{
		$response = new EmptyResponse();
		$this->assertInstanceOf(EmptyResponse::class, $response);
	}
}