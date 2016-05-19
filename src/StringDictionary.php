<?php
namespace Hansel23\Dictionaries;

use JsonSerializable;

/**
 * Class StringDictionary
 *
 * @package Hansel23\Dictionaries
 */
final class StringDictionary extends Dictionary implements JsonSerializable
{
	public function __construct()
	{
		$stringTypeName = gettype( '' );

		parent::__construct( $stringTypeName, $stringTypeName );
	}

	public function jsonSerialize()
	{
		return iterator_to_array( $this );
	}
}