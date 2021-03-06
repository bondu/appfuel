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
namespace Appfuel\App\Resource;

/**
 * File list that only accepts javascript files
 */
class JsList extends FileList
{
	/**
	 * @return	FileList
	 */
	public function __construct() 
	{
		parent::__construct('js', array('js'));
	}
}
