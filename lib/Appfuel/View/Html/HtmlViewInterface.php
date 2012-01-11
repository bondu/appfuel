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
namespace Appfuel\View\Html;

use Appfuel\View\FileViewInterface;

/**
 * Interface to manage the html view
 */
interface HtmlViewInterface extends FileViewInterface
{
	/**
     * @return  string
     */
    public function getLayoutClass();

    /**
     * @param   string  $file
     * @return  HtmlViewTemplate
     */
    public function setLayoutClass($class);
}
