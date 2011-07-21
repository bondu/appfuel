<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Orm\Domain;

use StdClass,
	Test\AfTestCase as ParentTestCase,
	Appfuel\Orm\Domain\DbIdentity;

/**
 * Db Identity maps database properties like table, columns, primary keys to
 * to domain members
 */
class DbIdentityTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var DbIdentity
	 */
	protected $identity = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->identity = new DbIdentity();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->identity);
	}

	/**
	 * @return null
	 */
	public function testImplementedInterfaces()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\Orm\Domain\DbDomainIdentityInterface',
			$this->identity
		);
	}

	/**
	 * The map is an associative array of domain members to database columns.
	 * It is expected that every domain member has a valid database columm
	 *
	 * @return null
	 */
	public function testGetSetMap()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => 'last_name',
			'email'		=> 'system_email'
		);

		/* default value */
		$this->assertEquals(array(), $this->identity->getMap());

		$this->assertSame(
			$this->identity,
			$this->identity->setMap($map)
		);
		$this->assertSame($map, $this->identity->getMap());
	}

	/**
	 * @return null
	 */
	public function testMapToMemberIsColumnGetAllColumns()
	{
		$this->assertEquals(array(), $this->identity->getAllColumns());
		
		$map = array(
			'user_id'		=> 'id',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName',
			'system_email'	=> 'email'
		);
		$this->identity->setMap($map);
		$this->assertEquals(
			array_values($map), 
			$this->identity->getAllColumns()
		);

		$this->assertTrue($this->identity->isColumn('user_id'));
		$this->assertEquals('id', $this->identity->mapToMember('user_id'));
		
		$this->assertTrue($this->identity->isColumn('first_name'));
		$this->assertEquals(
			'firstName', 
			$this->identity->mapToMember('first_name')
		);
		
		$this->assertTrue($this->identity->isColumn('last_name'));
		$this->assertEquals(
			'lastName', 
			$this->identity->mapToMember('last_name')
		);
		
		$this->assertTrue($this->identity->isColumn('system_email'));
		$this->assertEquals(
			'email', 
			$this->identity->mapToMember('system_email')
		);
		
		/* try columns we know don't exist */
		$this->assertFalse($this->identity->isColumn('no_column'));
		$this->assertFalse($this->identity->mapToMember('no_column'));
		
		/* invalid strings  */
		$this->assertFalse($this->identity->isColumn(''));
		$this->assertFalse($this->identity->mapToMember(''));
		
		$this->assertFalse($this->identity->isColumn(true));
		$this->assertFalse($this->identity->mapToMember(true));

		$this->assertFalse($this->identity->isColumn(12345));
		$this->assertFalse($this->identity->mapToMember(12345));

		$this->assertFalse($this->identity->isColumn(array(12345)));
		$this->assertFalse($this->identity->mapToMember(array(12345)));

		$this->assertFalse($this->identity->isColumn(new StdClass()));
		$this->assertFalse($this->identity->mapToMember(new StdClass()));	
	}

	/**
	 * @return null
	 */
	public function testMapToColumnIsMemberGetAllMembers()
	{
		$this->assertEquals(array(), $this->identity->getAllMembers());
		
		$map = array(
			'user_id'		=> 'id',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName',
			'system_email'	=> 'email'
		);
		$this->identity->setMap($map);
		$this->assertEquals(
			array_keys($map), 
			$this->identity->getAllMembers()
		);

		$this->assertTrue($this->identity->isMember('id'));
		$this->assertEquals('user_id', $this->identity->mapToColumn('id'));
		
		$this->assertTrue($this->identity->isMember('firstName'));
		$this->assertEquals(
			'first_name', 
			$this->identity->mapToColumn('firstName')
		);
		
		$this->assertTrue($this->identity->isMember('lastName'));
		$this->assertEquals(
			'last_name', 
			$this->identity->mapToColumn('lastName')
		);
		
		$this->assertTrue($this->identity->isMember('email'));
		$this->assertEquals(
			'system_email', 
			$this->identity->mapToColumn('email')
		);

		/* try columns we know don't exist */
		$this->assertFalse($this->identity->isMember('noMember'));
		$this->assertFalse($this->identity->mapToColumn('noMember'));
		
		/* invalid strings  */
		$this->assertFalse($this->identity->isMember(''));
		$this->assertFalse($this->identity->mapToColumn(''));
		
		$this->assertFalse($this->identity->isMember(true));
		$this->assertFalse($this->identity->mapToColumn(true));

		$this->assertFalse($this->identity->isMember(12345));
		$this->assertFalse($this->identity->mapToColumn(12345));

		$this->assertFalse($this->identity->isMember(array(12345)));
		$this->assertFalse($this->identity->mapToColumn(array(12345)));

		$this->assertFalse($this->identity->isMember(new StdClass()));
		$this->assertFalse($this->identity->mapToColumn(new StdClass()));	
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapEmptyMap()
	{
		$this->identity->setMap(array());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapEmptyColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => '',
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapNumericColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => 999,
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapArrayColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => array(1,2,3),
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetMapObjectColumn()
	{
		$map = array(
			'id'		=> 'user_id',
			'fistName'	=> 'first_name',
			'lastName'  => new StdClass(),
			'email'		=> 'system_email'
		);
		$this->identity->setMap($map);
	}

	/**
	 * Currently the only info an identity needs about a database table is 
	 * the table name
	 *
	 * @return null
	 */
	public function testGetSetTable()
	{
		$this->assertNull($this->identity->getTable());

		$table = 'users';
		$this->assertSame(
			$this->identity,
			$this->identity->setTable($table)
		);
		$this->assertEquals($table, $this->identity->getTable());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableEmptyString()
	{
		$this->identity->setTable('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableNumberic()
	{
		$this->identity->setTable(9999);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableArray()
	{
		$this->identity->setTable(array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetTableObject()
	{
		$this->identity->setTable(new StdClass());
	}

	/**
	 * @return null
	 */
	public function testGetSetLabel()
	{
		$this->assertNull($this->identity->getLabel());

		$label = 'user';
		$this->assertSame(
			$this->identity,
			$this->identity->setLabel($label)
		);
		$this->assertEquals($label, $this->identity->getLabel());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelEmptyString()
	{
		$this->identity->setLabel('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelNumberic()
	{
		$this->identity->setLabel(9999);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelArray()
	{
		$this->identity->setLabel(array(1,2,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetLabelObject()
	{
		$this->identity->setLabel(new StdClass());
	}

	/**
	 * @return null
	 */
	public function testGetSetPrimaryKeySingleKey()
	{
		$map = array(
			'user_id'		=> 'id',
			'system_name'	=> 'userName',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName'
		);
		$this->identity->setMap($map);

		$key = array('user_id');
		$this->assertEquals(array(), $this->identity->getPrimaryKey());
		$this->assertSame(
			$this->identity,
			$this->identity->setPrimarykey($key)
		);
		$this->assertEquals($key, $this->identity->getPrimaryKey());
	}

	/**
	 * @return null
	 */
	public function testGetSetPrimaryKeyCompoundKey()
	{
		$map = array(
			'user_id'		=> 'id',
			'customer_id'   => 'customerId',
			'system_name'	=> 'userName',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName'
		);
		$this->identity->setMap($map);

		$key = array('user_id', 'customer_id');
		$this->assertEquals(array(), $this->identity->getPrimaryKey());
		$this->assertSame(
			$this->identity,
			$this->identity->setPrimarykey($key)
		);
		$this->assertEquals($key, $this->identity->getPrimaryKey());
	}

	/**
	 * Primary members map the key columns into domain members
	 *
	 * @return null
	 */
	public function xtestGetPrimaryMembers()
	{
		$map = array(
			'user_id'		=> 'id',
			'customer_id'   => 'customerId',
			'system_name'	=> 'userName',
			'first_name'	=> 'firstName',
			'last_name'		=> 'lastName'
		);
		$this->identity->setMap($map);

		$keys = array('user_id', 'customer_id');
		$this->identity->setPrimaryKey($keys);

		$members = array('id', 'customerId');
		echo "\n", print_r($this->identity->getPrimaryMembers(),1), "\n";exit;
		$this->assertEquals($members, $this->identity->getPrimaryMembers());
	}


	/**
	 * All columns in the key must be mapped prior to setting the key
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetPrimaryKeyNotMapped()
	{
		$this->assertEquals(array(), $this->identity->getMap());
		$this->identity->setPrimaryKey(array('user_id'));
	}

	/**
	 * Since setMap guards against invalid strings and setPrimaryKey 
	 * requires a mapped string all invalid strings will fail because 
	 * they can not be mapped
	 *
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetPrimaryKeyEmptyString()
	{
		$this->assertEquals(array(), $this->identity->getMap());
		$this->identity->setPrimaryKey(array(''));
	}

	/**
	 * The dependecy list maps what domains a domain has access to and what 
	 * their relationship is to that dependant domain. The setter all labels 
	 * in the associative array are non empty strings and that the 'type' and 
	 * 'relation' keys also exist for each dependency
	 *
	 * @return null
	 */
	public function testGetSetDependencies()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 'many-many'
			)
		);

		/* default value is an empty array */
		$this->assertEquals(array(), $this->identity->getDependencies());

		$this->assertSame(
			$this->identity,
			$this->identity->setDependencies($map),
			'must expose a fluent interface'
		);

		$this->assertEquals($map, $this->identity->getDependencies());
	}

	/**
	 * Empty arrays are allowed since they indicate that the domain has no
	 * dependencies. 
	 * 
	 * @return null
	 */
	public function testGetSetDependenciesEmptyArray()
	{
		/* default value is an empty array */
		$this->assertEquals(array(), $this->identity->getDependencies());
		$this->assertSame(
			$this->identity,
			$this->identity->setDependencies(array()),
			'must expose a fluent interface'
		);
		$this->assertEquals(array(), $this->identity->getDependencies());
	}
	
	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesAssocKeyIsEmpty()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'' => array(
				'type'		=> 'root',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesAssocKeyIsInt()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			99 => array(
				'type'		=> 'root',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeIsMissing()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationIsMissing()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeIsNotAString()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 99,
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeNull()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> null,
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeEmpty()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> '',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeArray()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> array(1,2,3),
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeObject()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> new StdClass(),
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeIsNotRootOrChild()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'value-not-in-list',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeNotLowerCase()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'CHILD',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesTypeProperCase()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'Child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 'many-many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationUpperCase()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 'MANY-MANY'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationProperCase()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 'Many-Many'
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationNull()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> null
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationEmpty()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> ''
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationArray()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> array('many-many')
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationObj()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> new StdClass()
			)
		);

		$this->identity->setDependencies($map);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetDependenciesRelationInt()
	{
		$map = array(
			'user-email' => array(
				'type'		=> 'child',
				'relation'	=> 'one-many'
			),
			'roles' => array(
				'type'		=> 'root',
				'relation'	=> 99
			)
		);

		$this->identity->setDependencies($map);
	}
}
