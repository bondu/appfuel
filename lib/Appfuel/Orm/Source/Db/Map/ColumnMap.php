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
namespace Appfuel\Orm\Source\Db\Map;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Source\Db\Map\ColumnMapInterface;

/**
 * The database source handles preparing the sql and executing the database
 * handler and passing back the result
 */
class ColumnMap implements ColumnMapInterface
{
	/**
	 * Holds a mapping of column names to keys. The keys are generally member
	 * names but the don't have to be
	 * @var array
	 */
	protected $map = array();

	/**
	 * @param	array $map
	 * @return	ColumnMap
	 */
	public function __construct(array $map)
	{
		$this->setMap($map);
	}

	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}

	/**
	 * Returns a list of all columns
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		return array_values($this->map);
	}

	/**
	 * @param	string	$member	  domain member to be mapped to column
	 * @return	string | false when not found or invalid
	 */
	public function mapColumn($member)
	{
		if (empty($member) || ! is_string($member)) {
			return false;
		}

		if (! isset($this->map[$member])) {
			return false;
		}

		return $this->map[$member];
	}
	
	/**
	 * Validate that both column and members are valid strings then assign
	 *
	 * @return	null
	 */
	protected function setMap(array $map)
	{
		$err = "must be a non empty string";
		foreach ($map as $column => $member) {
			if (empty($column) || ! is_string($column)) {
				throw new Exception("column ($column) $err");
			}

			if (empty($member) || ! is_string($member)) {
				throw new Exception("domain member $err");
			}
		}

		$this->map = $map;
	}
}
