<?php
namespace Hansel23\Dictionary\Tests\Unit\Fixtures;

/**
 * Class TestType
 *
 * @package Hansel23\Dictionary\Tests\Unit\Fixtures
 */
class TestType implements Testable
{
	protected $name;

	public function __construct( $name )
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}
}