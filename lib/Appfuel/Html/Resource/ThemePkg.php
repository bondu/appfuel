<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use DomainException,
	InvalidArgumentException;

/**
 * A value object used to describe the manifest.json in the pkg directory
 */
class ThemePkg extends Pkg implements ThemePkgInterface
{
	/**
	 * Used to validate that the type of package is the expected one
	 * @var string
	 */
	protected $validType = 'theme';

	/**
	 * @return	bool
	 */
	public function isRequiredPackages()
	{
		return false;
	}

	/**
	 * @return	array
	 */
	public function getRequiredPackages()
	{
		return array();
	}
}
