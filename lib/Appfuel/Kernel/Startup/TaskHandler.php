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
namespace Appfuel\Kernel\Startup;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 * Runs a list tasks or an individual tasks. Task list and parameters are all
 * accessed from the configuration registry. 
 */
class TaskHandler implements TaskHandlerInterface
{
	/**
	 * Holds a list of status messages each message keyed by its task class
	 * @var array
	 */
	static $list = array();

	/**
	 * Used by the startup system to execute tasks from a list of class names
	 * stored in the configuration registry. The route and context is passed 
	 * into each task as way of injecting framework information into each task
	 * allowing the task to make decisions based on routing information or 
	 * user input (found in the context).
	 *
	 * @param	MvcRouteDetailInterface $route
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function kernelRunTasks(MvcRouteDetailInterface $route, 
								   MvcContextInterface $context)
	{
		$tasks = $this->getTasksFromRegistry();
		if (! is_array($tasks)) {
			$err = 'tasks defined in the config registry must be in an array';
			throw new RunTimeException($err);
		}

		foreach ($tasks as $index => $class) {
			if (! is_string($class) || empty($class)) {
				$err = "task must be a non empty string at index -($index)";
				throw new RunTimeException($err);
			}

			$task = new $class();
			if (! $task instanceof StartupTaskInterface) {
				$ns   = __NAMESPACE__;
				$err  = "-($class) must implement $ns\StartupTaskInterface";
				throw new RunTimeException($err);
			}
			$data = null;
			$keys = $task->getDataKeys();
			if (! empty($keys)) {
				$data = $this->collectFromRegistry($keys);
			}

			$task->kernelExecute($data, $route, $context);
			$this->addTaskStatus($class, $task->getStatus());
		}
	}

	/**
	 * Collects data keys out of the configuration registry and uses them to
	 * execute the task. This is a way to run the task without needing to have
	 * access to the route or context.
	 *
	 * @param	StartupTaskInterface $task
	 * @return	null
	 */
	public function runTask(StartupTaskInterface $task)
	{
		$data = null;
		$keys = $task->getDataKeys();
		if (! empty($keys)) {
			$data = $this->collectFromRegistry($keys);
		}

		$task->execute($data);
		$this->addTaskStatus(get_class($task), $task->getStatus());
	}

	/**
	 * @return	array
	 */
	public function getTasksFromRegistry()
	{
		return ConfigRegistry::get('startup-tasks', array());
	}

	/**
	 * @param	array	$list
	 * @return	array
	 */
	public function collectFromRegistry(array $list)
	{
		return ConfigRegistry::collect($list);
	}

	/**
	 * @return	array
	 */
	static public function getStatusList()
	{
		return self::$list;
	}

	/**
	 * @return	null
	 */
	static public function clearStatusList()
	{
		self::$list = array();
	}

	/**
	 * @param	string	$className
	 * @return	string | false
	 */
	static public function getStatus($key)
	{
		if (! is_string($key) || ! array_key_exists($key, self::$list)) {
			return false;
		}

		return self::$list[$key];
	}

	/**
	 * @param	string	$key
	 * @param	string	$msg
	 * @return	null
	 */
	static public function addStatus($key, $msg)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'status key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (! is_string($msg)) {
			$err = 'string message must be a string';
			throw new InvalidArgumentException($err);
		}

		self::$list[$key] = $msg;
	}

	protected function addTaskStatus($class, $msg = null)
	{
		$status = 'task run but no status given';
		if (null !== $msg) {
			$status = $msg;
		}
		self::addStatus($class, $status);
	}
}
