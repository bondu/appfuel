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
namespace Appfuel\Framework;

/**
 * The route is used by the dispatcher in order to build an controller
 * to execute
 */
interface RouteInterface
{
	public function getRouteString();
	public function getNamespace();
	public function getAccessPolicy();
	public function getReturnType();
}
