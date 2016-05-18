<?php
namespace Hansel23\Dictionary\Tests\Unit;

use Hansel23\Dictionary\StringDictionary;
use Hansel23\Dictionary\Tests\Unit\Fixtures\Stringable;

class StringDictionaryTest extends \Codeception\TestCase\Test
{
	public function InvalidTypeProvider()
	{
		return [
			[ 123, false, true, new \stdClass(), [ ], new Stringable(), 2.3, -1, -1.0 ],
		];
	}

	/**
	 * @dataProvider InvalidTypeProvider
	 *
	 * @expectedException \Hansel23\Dictionary\Exceptions\InvalidTypeException
	 */
	public function testIfInvalidKeyThrowsException( $type )
	{
		$dictionary = new StringDictionary();
		$dictionary->add( $type, 'string' );
	}

	/**
	 * @dataProvider InvalidTypeProvider
	 *
	 * @expectedException \Hansel23\Dictionary\Exceptions\InvalidTypeException
	 */
	public function testIfInvalidValueThrowsException( $type )
	{
		$dictionary = new StringDictionary();
		$dictionary->add( 'string', $type );
	}

	public function testJsonSerializeability()
	{
		$arr = [
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
		];

		$dictionary = new StringDictionary();

		foreach ( $arr as $key => $value )
		{
			$dictionary->add( $key, $value );
		}

		$this->assertEquals( json_encode( $arr ), json_encode( $dictionary ) );
	}
}