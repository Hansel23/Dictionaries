<?php
namespace Hansel23\Dictionary\Interfaces;

use ArrayAccess, Iterator, Countable;

/**
 * Interface RegistersItems
 *
 * @package Hansel23\Dictionary\Interfaces
 */
interface RegistersItems extends ArrayAccess, Iterator, Countable
{
	/**
	 * @param $key
	 * @param $value
	 */
	public function add( $key, $value );

	/**
	 * @param RegistersItems $registersItems
	 */
	public function merge( RegistersItems $registersItems );
}