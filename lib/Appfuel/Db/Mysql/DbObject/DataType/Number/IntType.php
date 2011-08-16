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
namespace Appfuel\Db\Mysql\DbObject\DataType\Number;

/**
 * Define the parameters for a mysql int
 */
class IntType extends AbstractIntType
{
	/**
	 * @param	bool	$isUnsigned		determines if this type is unsigned
	 * @return	SmallIntType
	 */
	public function __construct($isUnsigned = false)
	{
		parent::__construct(
			'int', 
			4294967295, 
			-2147483648, 
			2147483647, 
			(bool) $isUnsigned
		);
	}
}
