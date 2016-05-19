<?php
namespace Hansel23\Dictionaries\Tests\Unit;

use Hansel23\Dictionaries\Dictionary;
use Hansel23\Dictionaries\Tests\Unit\Fixtures\TestDictionary;
use Hansel23\Dictionaries\Tests\Unit\Fixtures\TestType;

class DictionaryTest extends \Codeception\TestCase\Test
{
	public function KeyProvider()
	{
		return [
			[ gettype('teststring'), 'teststring' ] ,
			[ gettype(123), 123 ],
			[ gettype(22.3), 22.3 ],
			[ gettype(false), false ],
			[ gettype(true), true ],
			[ TestType::class, new TestType( 'test' ) ],
			[ gettype([]), [ 'test' ] ],
		    [ gettype(null), null ],	    
		];
	}

	/**
	 * @dataProvider KeyProvider
	 *
	 * @expectedException \Hansel23\Dictionaries\Exceptions\ArgumentException
	 */
	public function testIfAddingDuplicateKeyThrowsArgumentException( $typeName, $key )
	{
		$dictionary = new Dictionary( $typeName, gettype( 'string' ) );
		$dictionary->add( $key, 'abc' );
		$dictionary->add( $key, 'def' );
	}

	/**
	 * @dataProvider KeyProvider
	 */
	public function testIfAddedEntryExists(  $typeName, $key )
	{
		$dictionary = new Dictionary( $typeName, gettype( 'value' ) );
		$dictionary->add( $key, 'abc' );

		$this->assertTrue( $dictionary->offsetExists( $key ) );
	}

	public function InvalidTypeProvider()
	{
		return [
			[ gettype( true ), new TestType('') ],
			[ gettype( true ),0 ],
			[ gettype( true ), 1.0],
			[ gettype( true ), ''],
			[ gettype( true ), null ],
			[ gettype( true ), [] ],
		    [ gettype(0), new TestType('') ],
			[ gettype(0), 1.0 ],
			[ gettype(0), '' ],
			[ gettype(0), true ],
			[ gettype(0), null ],
			[ gettype(0), [] ],
			[ gettype( 1.0 ), new TestType('') ],
			[ gettype( 1.0 ), 0 ],
			[ gettype( 1.0 ), true ],
			[ gettype( 1.0 ), '' ],
			[ gettype( 1.0 ), null ],
			[ gettype( 1.0 ), [] ],
			[ gettype( '' ), new TestType('') ],
			[ gettype( '' ), 0 ],
			[ gettype( '' ), true ],
			[ gettype( '' ), null ],
			[ gettype( '' ), [] ],
			[ gettype(null), new TestType('') ],
			[ gettype(null), 1.0 ],
			[ gettype(null), 0 ],
			[ gettype(null), true ],
			[ gettype(null), '' ],
			[ gettype(null), [] ],
			[ TestType::class, 0 ],
			[ TestType::class, 1.0 ],
			[ TestType::class, true ],
			[ TestType::class, '' ],
			[ TestType::class, null ],
			[ TestType::class, [] ],
			[ gettype( [] ), new TestType('') ],
			[ gettype( [] ), 1.0 ],
			[ gettype( [] ), 0 ],
			[ gettype( [] ), true ],
			[ gettype( [] ), '' ],
			[ gettype( [] ), null ],
			[ get_class( function(){} ), function(){} ],
		];
	}

	/**
	 * @dataProvider InvalidTypeProvider
	 *
	 * @expectedException \Hansel23\Dictionaries\Exceptions\InvalidTypeException
	 */
	public function testIfInvalidKeyTypesThrowsException( $typeName, $invalidKey )
	{
		$dictionary = new Dictionary( $typeName, gettype( 'value' ) );

		$dictionary->add( $invalidKey, 'abc' );
	}

	public function testOverwritingExistingKeysWithSetOffset()
	{
		$sameKey = 'key';

		$dictionary             = new Dictionary( gettype( 'teststring' ), TestType::class );
		$dictionary[ $sameKey ] = new TestType( 'value' );

		$newValue = new TestType( 'new-value' );
		$dictionary->offsetSet( $sameKey, $newValue );

		$this->assertEquals( $newValue, $dictionary[ $sameKey ] );
		$this->assertEquals( 1, $dictionary->count() );
	}

	public function testUnsettingExistingEntry()
	{
		$dictionary = new Dictionary( TestType::class, TestType::class );

		$testKey = new TestType( 'abc' );
		$dictionary->add( $testKey, new TestType( 'def' ) );

		$dictionary->offsetUnset( $testKey );

		$this->assertFalse( $dictionary->offsetExists( $testKey ) );
	}

	/**
	 * @expectedException \Hansel23\Dictionaries\Exceptions\InvalidKeyException
	 */
	public function testIfGettingEntryByInvalidKeyThrowsInvalidKeyException()
	{
		$dictionary = new Dictionary( gettype( 'teststring' ), TestType::class );

		$dictionary->add( 'abc', new TestType( 'def' ) );
		$dictionary->offsetGet( 'def' );
	}

	public function testIteratingDictionaryAndCount()
	{
		$keyValues = [
			'abc' => new TestType( 'def' ),
			'ghi' => new TestType( 'jkl' ),
			'mno' => new TestType( 'pqr' ),
		];

		$dictionary = new Dictionary( gettype( 'teststring' ), TestType::class );

		foreach ( $keyValues as $key => $value )
		{
			$dictionary->add( $key, $value );
		}

		foreach ( $dictionary as $key => $value )
		{
			$this->assertEquals( $keyValues[ $key ], $value );
		}

		$this->assertFalse( $dictionary->current() );

		$dictionary->rewind();

		$this->assertEquals( $dictionary->current(), $keyValues[ array_keys( $keyValues )[0] ] );
		$this->assertEquals( count( $keyValues ), count( $dictionary ) );
	}

	public function testMergingDictionaries()
	{
		$keyValues = [
			[ 'key' => 'abc', 'value' => new TestType( 'def' ) ],
			[ 'key' => 'ghi', 'value' => new TestType( 'jkl' ) ],
			[ 'key' => 'mno', 'value' => new TestType( 'pqr' ) ],
			[ 'key' => 'stv', 'value' => new TestType( 'xyz' ) ],
		];

		$dictionary = new Dictionary( gettype( 'teststring' ), TestType::class );

		foreach ( $keyValues as $keyValue )
		{
			$dictionary->add( $keyValue['key'], $keyValue['value'] );
		}

		$keyValues = [
			[ 'key' => 'abc', 'value' => new TestType( 'abc' ) ],
			[ 'key' => 'jkl', 'value' => new TestType( 'ghi' ) ],
			[ 'key' => 'mno', 'value' => new TestType( 'mno' ) ],
			[ 'key' => 'xyz', 'value' => new TestType( 'stv' ) ],
		];

		$anotherDictionary = new Dictionary( gettype( 'teststring' ), TestType::class );

		foreach ( $keyValues as $keyValue )
		{
			$anotherDictionary->add( $keyValue['key'], $keyValue['value'] );
		}

		$dictionary->merge( $anotherDictionary );

		$this->assertEquals( 6, $dictionary->count() );
		$this->assertEquals( $dictionary['abc'], $keyValues[0]['value'] );
		$this->assertEquals( $dictionary['mno'], $keyValues[2]['value'] );
	}

	/**
	 * @expectedException \Hansel23\Dictionaries\Exceptions\InvalidDictionaryException
	 */
	public function testIfMergingDifferentDictionariesThrowsException()
	{
		$dictionary        = new Dictionary( gettype( 'teststring' ), TestType::class );
		$anotherDictionary = new TestDictionary( gettype( 'teststring' ), TestType::class );

		$dictionary->merge( $anotherDictionary );
	}
}