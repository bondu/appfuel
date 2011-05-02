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
namespace Appfuel\Framework\View;

/**
 * Interface needed by the framework to use view templates
 */
interface ManagerInterface
{
	/**
	 * @return	DocumentInterface
	 */
	public function getDoc();

	/**
	 * @param	DocumentInteface	$doc
	 * @return	ManagerInterface
	 */
	public function setDoc(DocumentInterface $doc);

    /**
     * Assign key value pair into scope
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  File
     */
    public function assign($name, $value);
}
