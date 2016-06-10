<?php
namespace Hansel23\Dictionaries;

use Hansel23\Dictionaries\Exceptions\ArgumentException;
use Hansel23\Dictionaries\Exceptions\InvalidDictionaryException;
use Hansel23\Dictionaries\Exceptions\InvalidKeyException;
use Hansel23\Dictionaries\Exceptions\InvalidTypeException;
use Hansel23\Dictionaries\Interfaces\RegistersItems;

/**
 * Class AbstractDictionary
 *
 * @package Hansel23\Dictionaries
 */
abstract class AbstractDictionary implements RegistersItems
{
	/**
	 * @var string
	 */
	protected $dictionaryType;

	/**
	 * @var array
	 */
	protected $keyMap = [ ];

	/**
	 * @var array
	 */
	protected $keyValuePairs = [ ];

	/**
	 * @var int
	 */
	protected $currentPosition = 0;

	/**
	 * @var int
	 */
	protected $itemCount = 0;

	/**
	 * AbstractDictionary constructor.
	 */
	public function __construct()
	{
		$this->dictionaryType = get_class( $this );
	}

	/**
	 * @return string
	 */
	abstract protected function getNameOfKeyType();

	/**
	 * @return string
	 */
	abstract protected function getNameOfValueType();

	/**
	 * @param $key
	 * @param $value
	 *
	 * @throws ArgumentException
	 */
	public function add( $key, $value )
	{
		if ( $this->offsetExists( $key ) )
		{
			throw new ArgumentException( 'Duplicate key: ' . $this->stringifyKey( $key ) );
		}

		$this->offsetSet( $key, $value );
	}

	/**
	 * @param RegistersItems $registersItems
	 *
	 * @throws InvalidDictionaryException
	 */
	public function merge( RegistersItems $registersItems )
	{
		$dictionaryTypeToMergeWith = get_class( $registersItems );

		if ( $dictionaryTypeToMergeWith != $this->dictionaryType )
		{
			throw new InvalidDictionaryException(
				sprintf(
					'Invalid dictionary-type: %s. Expected: %s', $dictionaryTypeToMergeWith, $this->dictionaryType
				)
			);
		}

		foreach ( $registersItems as $key => $value )
		{
			$this->offsetSet( $key, $value );
		}
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 * */
	public function offsetExists( $offset )
	{
		return $this->existsStringifiedKey( $this->stringifyKey( $offset ) );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 * @throws InvalidKeyException
	 * @throws InvalidTypeException
	 */
	public function offsetGet( $offset )
	{
		$stringifiedKey = $this->stringifyKey( $offset );

		$this->ensureStringifiedKeyDoesExist( $stringifiedKey );

		return $this->keyValuePairs[ $this->keyMap[ $stringifiedKey ] ]->getValue();
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 *
	 * @throws ArgumentException
	 */
	public function offsetSet( $offset, $value )
	{
		$this->validateKeyType( $offset );
		$this->validateValueType( $value );

		$stringifiedKey = $this->stringifyKey( $offset );

		if ( !array_key_exists( $stringifiedKey, $this->keyMap ) )
		{
			$this->keyMap[ $stringifiedKey ] = $this->itemCount++;
		}

		$this->keyValuePairs[ $this->keyMap[ $stringifiedKey ] ] = new KeyValuePair( $offset, $value );
	}

	/**
	 * @param mixed $offset
	 *
	 * @throws InvalidKeyException
	 */
	public function offsetUnset( $offset )
	{
		$stringifiedKey = $this->stringifyKey( $offset );

		$this->ensureStringifiedKeyDoesExist( $stringifiedKey );

		unset($this->keyValuePairs[ $this->keyMap[ $stringifiedKey ] ]);
		unset($this->keyMap[ $stringifiedKey ]);

		$this->itemCount--;
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return $this->itemCount;
	}

	/**
	 * @return bool|mixed
	 */
	public function current()
	{
		if ( isset($this->keyValuePairs[ $this->currentPosition ]) )
		{
			return $this->keyValuePairs[ $this->currentPosition ]->getValue();
		}

		return false;
	}

	/**
	 * @return void
	 */
	public function next()
	{
		$this->currentPosition++;
	}

	/**
	 * @return mixed
	 */
	public function key()
	{
		return $this->keyValuePairs[ $this->currentPosition ]->getKey();
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return isset($this->keyValuePairs[ $this->currentPosition ]);
	}

	/**
	 * @return void
	 */
	public function rewind()
	{
		$this->keyMap          = array_flip( array_keys( $this->keyMap ) );
		$this->keyValuePairs   = array_values( $this->keyValuePairs );
		$this->currentPosition = 0;
	}

	/**
	 * @param $key
	 *
	 * @throws InvalidTypeException
	 */
	private function validateKeyType( $key )
	{
		$this->validateType( $key, $this->getNameOfKeyType() );
	}

	/**
	 * @param $value
	 *
	 * @throws InvalidTypeException
	 */
	private function validateValueType( $value )
	{
		$this->validateType( $value, $this->getNameOfValueType() );
	}

	/**
	 * @param mixed  $typeToValidate
	 * @param string $validTypeName
	 *
	 * @throws InvalidTypeException
	 */
	private function validateType( $typeToValidate, $validTypeName )
	{
		if ( is_object( $typeToValidate ) )
		{
			$typeName = get_class( $typeToValidate );
		}
		else
		{
			$typeName = gettype( $typeToValidate );
		}

		if ( $typeName != $validTypeName && !($typeToValidate instanceof $validTypeName) )
		{
			throw new InvalidTypeException(
				sprintf( 'Invalid element of type %s, expected: %s', $typeName, $validTypeName )
			);
		}
	}

	/**
	 * @param $key
	 *
	 * @return string
	 * @throws InvalidTypeException
	 */
	private function stringifyKey( $key )
	{
		if ( is_scalar( $key ) )
		{
			return md5( $key );
		}
		else
		{
			try
			{
				return md5( serialize( $key ) );
			}
			catch ( \Exception $ex )
			{
				throw new InvalidTypeException( 'Unserializeable key given' );
			}
		}
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	private function existsStringifiedKey( $key )
	{
		return array_key_exists( $key, $this->keyMap );
	}

	/**
	 * @param $key
	 *
	 * @throws InvalidKeyException
	 */
	private function ensureStringifiedKeyDoesExist( $key )
	{
		if ( !$this->existsStringifiedKey( $key ) )
		{
			throw new InvalidKeyException( $key );
		}
	}
}