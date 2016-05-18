<?php
namespace Hansel23\Dictionary;

use JsonSerializable;

/**
 * Class MemberDictionary
 *
 * @package Hansel23\StronglyTyped
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