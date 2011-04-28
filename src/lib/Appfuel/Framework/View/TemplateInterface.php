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
interface TemplateInterface extends DocumentInterface
{
	public function fileExists($key);
	public function addFile($key, $path);
	public function getFile($key);
	public function buildFile($key);
}
