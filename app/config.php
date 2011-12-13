<?php

return array(
	'common' => array(
		'env'					=> 'local',
		'base-path'				=> AF_BASE_PATH,
		'enable-autoloader'		=> true,
		'default-timezone'		=> 'America/Los_Angeles',
		'display-errors'		=> 'on',
		'error-reporting'		=> 'all, strict',
		'startup-tasks'	=> array(
			'Appfuel\Db\DbStartup',
		),

		'db' => array(
			'databases' => array(
				'af-unittest' => array(
					'users' => array('me')
				)
			)
		),
	),

	'main' => array(
		'include-path'		=> array(AF_BASE_PATH . '/lib'),	
		'include-path-action'	=> 'replace',
		'error-reporting'	=> 'all, strict',
		'startup-tasks'		=> array(
			'Appfuel\App\AppStartup',
		),
		'intercepting-filters'	=> array(
			'Appfuel\App\Filter\AuthFilter', 
			'Appfuel\App\Filter\OrgFilter', 
			'Appfuel\App\Filter\ThemeFilter',
			'Appfuel\App\Filter\OutputFilter',
		),


	),
	
	'test' => array(
		'include-path-action'	=> 'append',
		'include-path'			=> array(
			'/usr/local/php/share/pear',
			AF_BASE_PATH . '/test',
			AF_BASE_PATH . '/test/lib',
			AF_BASE_PATH . '/test/classes',
			AF_BASE_PATH . '/lib',
		),
		'startup-tasks'	=> array(
			'TestFuel\UnitTestStartup',
		),
		'db'					=> array(
			'databases' => array(
				'af-unittest' => array(
					'dbname'            => 'af_unittest',
					'default-charset'   => 'utf8',
					'default-collate'   => 'utf8_general_ci',
					'users'             => array('af-testuser', 'af-testadmin')
				),
			),
			'privilege-groups' => array(
				'app-user' => array(
					'select',
					'insert',
					'delete',
					'update',
					'execute'
				),
				'app-admin' => array('all'),
			),
			'connectors' => array(
				'af-test' => array(
					'master' => array(
						'class' => 'Appfuel\Db\Mysql\Mysqli\DbConnection',
						'host'	=> 'localhost',
						'name'  => 'af-unittest',
						'user'	=> 'appfuel_user',
						'pass'	=> 'w3b_g33k'
					),
					'slave' => array(
						'class' => 'Appfuel\Db\Mysql\Mysqli\DbConnection',
						'host'	=> 'localhost',
						'name'  => 'af-unittest',
						'user'	=> 'appfuel_user',
						'pass'	=> 'w3b_g33k'
					),				
				)
			 ),
			'app-connector' => 'af-test',
		)
	),
);
