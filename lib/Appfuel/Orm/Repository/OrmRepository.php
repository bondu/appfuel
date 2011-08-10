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
namespace Appfuel\Orm\Repository;

use BadMethodCallException,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\OrmFactoryInterface,
	Appfuel\Framework\Orm\Domain\DomainStateInterface,
	Appfuel\Framework\Orm\Domain\DomainModelInterface,
	Appfuel\Framework\Orm\Repository\AssemblerInterface,
	Appfuel\Framework\Orm\Repository\OrmRespositoryInterface;

/**
 * The repository is facade for the orm systems. Developers use the repo to 
 * create, modify, delete or find domains in the database. 
 */
abstract class OrmRepository implements OrmRepositoryInterface
{
	/**
	 * Criteria stores options in the form of key/value pair and named 
	 * expression lists which are generally used by the data source to
	 * construct things like sql.
	 *
	 * @var Criteria
	 */
	protected $criteria = null;

	/**
	 * Adapter that performs concrete operations for database, flat files,
	 * rest services or key value stores
	 * @var	 AssemblerInterface
	 */
	protected $_asm = null;

	/**
	 * @param	OrmFactoryInterface $factory
	 * @return	OrmRepository
	 */
	public function __construct(OrmFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = $this->createOrmFactory();
		}

		$this->setAssembler($factory->createAssembler());
	}

	/**
	 * @return	OrmFactoryInterface
	 */
	abstract protected function creeateOrmFactory();

	/**
	 * @return	AssemblerInterface
	 */
	protected function getAssembler()
	{
		return $this->_asm;
	}

	/**
	 * @param	AssmeblerInterface $asm
	 * @return	null
	 */
	protected function setAssembler(AssemblerInterface $asm)
	{
		$this->asm = $asm;
	}
}
