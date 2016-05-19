<?php
namespace Hansel23\Dictionaries;

/**
 * Class KeyValuePair
 *
 * @package Hansel23\Dictionaries
 */
final class KeyValuePair 
{
	public $key;
	public $value;

	/**
	 * @param $key
	 * @param $value
	 */
	public function __construct( $key, $value )
	{
		$this->key = $key;

		$this->value = $value;
	}

	/**
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
} 