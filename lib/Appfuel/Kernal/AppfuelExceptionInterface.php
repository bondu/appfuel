<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernal;

/**
 * Adds namespace and tags to the exception
 */
interface AppfuelExceptionInterface
{
	/**
	 * @return	string
	 */
	public function getNamespace();

	/**
	 * @return	string
	 */
	public function getTags();
}