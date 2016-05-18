<?php
namespace Hansel23\Dictionary\Tests\Unit\Fixtures;

/**
 * Class Stringable
 *
 * @package Hansel23\Dictionary\Tests\Unit\Fixtures
 */
class Stringable
{
	public function __toString()
	{
		return 'Not a real string';
	}
}