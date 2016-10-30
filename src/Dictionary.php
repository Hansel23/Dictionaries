<?php
namespace Hansel23\Dictionaries;

use Hansel23\Dictionaries\Interfaces\RegistersItems;

/**
 * Class NewDictionary
 *
 * @package Hansel23\Dictionaries
 */
class Dictionary extends AbstractDictionary
{
	/**
	 * @var string
	 */
	protected $validKeyTypeName;

	/**
	 * @var string
	 */
	protected $validValueTypeName;

	/**
	 * @param string $keyClassName
	 * @param string $valueClassName
	 */
	public function __construct( $keyClassName, $valueClassName )
	{
		$this->validKeyTypeName   = $keyClassName;
		$this->validValueTypeName = $valueClassName;

		parent::__construct();
	}

	/**
	 * @return string
	 */
	protected function getNameOfKeyType()
	{
		return $this->validKeyTypeName;
	}

	/**
	 * @return string
	 */
	protected function getNameOfValueType()
	{
		return $this->validValueTypeName;
	}
}