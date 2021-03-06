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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	Appfuel\Kernel\Mvc\AppInput,
	Appfuel\Kernel\Mvc\RequestUri,
	Appfuel\Kernel\Mvc\MvcContext,
	Appfuel\Kernel\KernelRegistry,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Console\ConsoleViewTemplate,
	Appfuel\Kernel\Mvc\MvcActionDispatcher;

/**
 */
class MvcActionDispatcherTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcActionDispatcher
	 */
	protected $dispatcher = null;

	/**
	 * Keep a backup copy of the route map
	 * @var array
	 */
	protected $backup = null;

	/**
	 * Keep a backup copy of $_GET, $_POST, $_FILES, $_COOKIE, 
	 * and $_SERVER['argv']
	 * @var array
	 */
	protected $bkSuperGlobals = array();

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->backup = KernelRegistry::getRouteMap();
		$this->dispatcher = new MvcActionDispatcher();
		KernelRegistry::clearRouteMap();

		$routeMap = array(
			'my-key'   => 'TestFuel\Fake\Action\TestDispatch\ActionA',
			'my-route' => 'TestFuel\Fake\Action\TestDispatch\ActionA'
		);
		KernelRegistry::setRouteMap($routeMap);
	
		$cli = null;
		if (isset($_SERVER['argv'])) {
			$cli = $_SERVER['argv'];
		}
		$this->bkSuperGlobals = array(
			'get'    => $_GET,
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE, 
			'argv'   => $cli
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		KernelRegistry::setRouteMap($this->backup);
		$this->dispatcher = null;

		$_GET    = $this->bkSuperGlobals['get'];
		$_POST   = $this->bkSuperGlobals['post'];
		$_FILES  = $this->bkSuperGlobals['files'];
		$_COOKIE = $this->bkSuperGlobals['cookie'];
		$cli = $this->bkSuperGlobals['argv'];
		if (null !== $cli) {
			$_SERVER['argv'] = $cli;
		}
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcActionDispatcherInterface',
			$this->dispatcher
		);
	}

	/**
	 * When a route is set and not input is needed then the uri does not need
	 * to be set
	 *
	 * methods used: setRoute, noInputRequired, addAclCodes, buildContext
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_A()
	{

		$codes = array('my-code', 'your-code');
		$context = $this->dispatcher->setStrategy('console')
									->setRoute('my-key')
									->noInputRequired()
									->addAclCodes($codes)
									->buildContext();
		$input    = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array(), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);

		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals($codes, $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('my-key', $route);
		$this->assertEquals('console', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\ConsoleView',
			$view
		);
		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * I am using the route from a RequestUri along with its get paramters
	 * methods used: setUri, useUriForInputSource, addAclCodes, buildContext
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_B()
	{
		$route = 'my-key';
		$uri   = new RequestUri("$route/param1/value1/param2/value2");
		$codes = array('my-code', 'your-code');
		$context = $this->dispatcher->setStrategy('html')
									->setUri($uri)
									->useUriForInputSource()
									->addAclCodes($codes)
									->buildContext();

		$input = $context->getInput();
		$input    = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals($codes, $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('my-key', $route);
		$this->assertEquals('html', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * I am using the uristring not the object. I want the parameters in the
	 * input. For this test we will leave out the acl codes. Note this uri 
	 * string defineds the route key in the query string with the label 
	 * 'routekey'
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_C()
	{
		$uriString = 'param1/value1?routekey=my-key&param2=value2';
		$context   = $this->dispatcher->setStrategy('ajax')
									  ->setUri($uriString)
									  ->useUriForInputSource()
									  ->buildContext();
	
		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);	
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals(array(), $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('my-key', $route);
		$this->assertEquals('ajax', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\AjaxView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * Tell the dispatcher to create the uri from the $_SERVER['REQUEST_URI']
	 * and use that for the input source
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_D()
	{
		$_SERVER['REQUEST_URI'] = 'my-key/param1/value1/param2/value2';
		$context = $this->dispatcher->setStrategy('console')
									->useServerRequestUri()
									->useUriForInputSource()
									->buildContext();
	
		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1'=>'value1','param2'=>'value2'), 
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);	
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals(array(), $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('my-key', $route);
		$this->assertEquals('console', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\ConsoleView',
			$view
		);
		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testSetUri_Failure($uri)
	{
		$context = $this->dispatcher->setUri($uri);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInterface
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @return				null
	 */
	public function testSetRoute_Failure($route)
	{
		$context = $this->dispatcher->setRoute($route);
	}

	/**
	 * We will manual set a uri and define the input
	 *
	 * methods used: setUri, defineInput
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_E()
	{
		$uriString = 'my-key/param5/value5/param6/value6';
		$inputParams = array(
			'get'    => array('paramX' => 'valueX'),
			'post'   => array('param1' => 'value1'),
			'files'  => array('param2' => 'value2'),
			'cookie' => array('param3' => 'value3'),
			'argv'   => array('param4' => 'value4'),
		);

		$context = $this->dispatcher->setStrategy('ajax')
									->setUri($uriString)
									->defineInput('post', $inputParams, true)
									->buildContext();
		
		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = $inputParams;
		$expected['get'] = array(
			'paramX' => 'valueX', 
			'param5' => 'value5',
			'param6' => 'value6'
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals(array(), $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('post', $input->getMethod());
		$this->assertEquals('my-key', $route);
		$this->assertEquals('ajax', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\AjaxView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * We will manual set a route and define the input but will not use the
	 * uri for parameters in the input
	 *
	 * methods used: setRoute, defineInput
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildContext_F()
	{
		$inputParams = array(
			'get'    => array('paramX' => 'valueX'),
			'post'   => array('param1' => 'value1'),
			'files'  => array('param2' => 'value2'),
			'cookie' => array('param3' => 'value3'),
			'argv'   => array('param4' => 'value4'),
		);

		$context = $this->dispatcher->setStrategy('console')
									->setRoute('my-route')
									->defineInput('cli', $inputParams, false)
									->buildContext();
		
		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();

		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertEquals(array(), $context->getAclCodes());
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('cli', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('console', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\ConsoleView',
			$view
		);
		$this->assertEquals($inputParams, $input->getAll());
	}

	/**
	 * When using defineInput and the third parameter $useUri is true then
	 * the uri object must have been set prior or a RunTimeException will
	 * will have been thrown. Note that the default setting is to use the uri
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testDefineInput_Failure()
	{
		$inputParams = array(
			'get'    => array('paramX' => 'valueX'),
			'post'   => array('param1' => 'value1'),
			'files'  => array('param2' => 'value2'),
			'cookie' => array('param3' => 'value3'),
			'argv'   => array('param4' => 'value4'),
		);


		$context = $this->dispatcher->defineInput('get', $inputParams)
									->buildContext();
	}

	/**
	 * When using defineInputFromSuperGlobal all the inputs will be taken from
	 * $_POST, $_FILES, $_COOKIE, and if $useUri is true then the 'get' params
	 * will be taken from the uri object and that uri object will be created
	 * with the string found in $_SERVER['REQUEST_URI']
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsNoUriSet()
	{
		$_POST   = array('param2' => 'value2','param3' => 'value3');
		$_FILES  = array('param4' => 'value4');
		$_COOKIE = array('param5' => 'value5');
		$_SERVER['argv'] = array('param6' => 'value6');
		$_SERVER['REQUEST_METHOD'] = 'post';
		$_SERVER['REQUEST_URI'] = 'my-route/param1/value1';

		$context = $this->dispatcher->setStrategy('ajax')
									->defineInputFromSuperGlobals()
									->buildContext();

		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1' => 'value1'),
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE,
			'argv'   => $_SERVER['argv']
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('post', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('ajax', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\AjaxView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * If you use defineInputFromSuperGlobals and you do not set the uri in
	 * any way and the uri string in not found at $_SERVER['REQUEST_URI'] then
	 * a RunTimeException will be thrown
	 *
	 * @expectedException	RunTimeException
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsNo_REQUEST_URI()
	{
		$_POST   = array('param2' => 'value2','param3' => 'value3');
		$_FILES  = array('param4' => 'value4');
		$_COOKIE = array('param5' => 'value5');
		$_SERVER['argv'] = array('param6' => 'value6');
		$_SERVER['REQUEST_METHOD'] = 'post';
		unset($_SERVER['REQUEST_URI']);

		$context = $this->dispatcher->defineInputFromSuperGlobals()
									->buildContext();

	}

	/**
	 * If $_SERVER['REQUEST_URI'] is not a string RunTimeException will be 
	 * thrown. No php handles this itself so it should not occur. But we
	 * can fake it to demonstrate the effect
	 *
	 * @expectedException	RunTimeException
	 * @dataProvider		provideInvalidStringsIncludeNull
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsBad_REQUEST_URI($uri)
	{
		$_POST   = array('param2' => 'value2','param3' => 'value3');
		$_FILES  = array('param4' => 'value4');
		$_COOKIE = array('param5' => 'value5');
		$_SERVER['argv'] = array('param6' => 'value6');
		$_SERVER['REQUEST_METHOD'] = 'post';
		$_SERVER['REQUEST_URI'] = $uri;

		$context = $this->dispatcher->defineInputFromSuperGlobals()
									->buildContext();

	}

	/**
	 * When using defineInputFromSuperGlobal all the inputs will be taken from
	 * $_POST, $_FILES, $_COOKIE, and if $useUri is true then the 'get' params
	 * will be taken from the uri object
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsUriSet()
	{
		$_POST   = array('param2' => 'value2','param3' => 'value3');
		$_FILES  = array('param4' => 'value4');
		$_COOKIE = array('param5' => 'value5');
		$_SERVER['argv'] = array('param6' => 'value6');
		$_SERVER['REQUEST_METHOD'] = 'post';
		$uriString = 'my-route/param1/value1';

		$context = $this->dispatcher->setStrategy('html')
									->setUri($uriString)
									->defineInputFromSuperGlobals()
									->buildContext();

		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1' => 'value1'),
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE,
			'argv'   => $_SERVER['argv']
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('post', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('html', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * When you want $_GET as your parameters and not the uri use false as
	 * an arguement to defineInputFromSuperGlobals
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefineInputFromSuperGlobalsNoUriWanted()
	{
		$_GET    = array('param1' => 'value1');
		$_POST   = array('param2' => 'value2','param3' => 'value3');
		$_FILES  = array('param4' => 'value4');
		$_COOKIE = array('param5' => 'value5');
		$_SERVER['argv'] = array('param6' => 'value6');
		$_SERVER['REQUEST_METHOD'] = 'get';

		$context = $this->dispatcher->setStrategy('html')
									->setRoute('my-route')
									->defineInputFromSuperGlobals(false)
									->buildContext();

		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => $_GET,
			'post'   => $_POST,
			'files'  => $_FILES,
			'cookie' => $_COOKIE,
			'argv'   => $_SERVER['argv']
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('html', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);
		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * When everthing in the request is found in the uri then you can use
	 * this method which will define the request method a get and use only
	 * parameters found in the uri object
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testUseUriForInput()
	{
		$uriString = 'my-route/param1/value1';
		$_GET    = array();
		$_POST   = array();
		$_FILES  = array();
		$_COOKIE = array();
		$_SERVER['argv'] = array();
		$_SERVER['REQUEST_METHOD'] = '';

		$context = $this->dispatcher->setStrategy('html')
									->setUri($uriString)
									->useUriForInputSource()
									->buildContext();

		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array('param1' => 'value1'),
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('html', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * It is a RunTimeException to try an use a uri before its been set
	 *
	 * @expectedException	RunTimeException
	 * @depends				testInterface
	 * @return				null
	 */
	public function testUseUriForInputWhenUriIsNotSet()
	{
		$context = $this->dispatcher->useUriForInputSource()
									->buildContext();
	}

	/**
	 * When you need to dispatch to a mvc action but no input is required 
	 * use this method so you don't cause a runtime exception with the 
	 * input. This will build an empty input so you don't have to. Uri does
	 * not have to be present
	 *
	 * @depends	testInterface
	 */
	public function testNoInputRequired()
	{
		$context = $this->dispatcher->setStrategy('html')
									->setRoute('my-route')
									->noInputRequired()
									->buildContext();
	
		$input = $context->getInput();
		$view     = $context->getView();
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$expected = array(
			'get'    => array(),
			'post'   => array(),
			'files'  => array(),
			'cookie' => array(),
			'argv'   => array()
		);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\MvcContext', $context);
		$this->assertInstanceOf('Appfuel\Kernel\Mvc\AppInput', $input);
		$this->assertEquals('get', $input->getMethod());
		$this->assertEquals('my-route', $route);
		$this->assertEquals('html', $strategy);
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);

		$this->assertEquals($expected, $input->getAll());
	}

	/**
	 * @depends	testInterface
	 */
	public function testDispatchAjax()
	{
		$context = $this->dispatcher->setRoute('my-key')
									->setStrategy('ajax')
									->noInputRequired()
									->buildContext();

		$this->dispatcher->dispatch($context);
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContextInterface',
			$context
		);

		$view = $context->getView();
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\AjaxView',
			$view
		);
		$this->assertEquals('bar',     $view->get('ajax-foo'));
		$this->assertEquals('value-a', $view->get('common-a'));
		$this->assertEquals('value-b', $view->get('common-b'));
		$this->assertEquals('my-key', $route);
		$this->assertEquals('ajax', $strategy);
	}

	/**
	 * @depends	testInterface
	 */
	public function testDispatchHtml()
	{
		$context = $this->dispatcher->setRoute('my-key')
								 ->setStrategy('html')
								 ->noInputRequired()
								 ->buildContext();
	
		$this->dispatcher->dispatch($context);
		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContextInterface',
			$context
		);

		$view = $context->getView();
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\HtmlView',
			$view
		);
		$this->assertEquals('bar', $view->get('html-foo'));
		$this->assertEquals('value-a', $view->get('common-a'));
		$this->assertEquals('value-b', $view->get('common-b'));
		$this->assertEquals('my-key', $route);
		$this->assertEquals('html', $strategy);
	}

	/**
	 * @depends	testInterface
	 */
	public function testDispatchConsole()
	{
		$context = $this->dispatcher->setRoute('my-key')
									->setStrategy('console')
									->noInputRequired()
									->buildContext();

		$this->dispatcher->dispatch($context);

		$route    = $context->getRoute();
		$strategy = $context->getStrategy();
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcContextInterface',
			$context
		);

		$view = $context->getView();
		$this->assertInstanceOf(
			'TestFuel\Fake\Action\TestDispatch\ActionA\ConsoleView',
			$view
		);

		$this->assertEquals('bar',     $view->get('console-foo'));
		$this->assertEquals('value-a', $view->get('common-a'));
		$this->assertEquals('value-b', $view->get('common-b'));
		$this->assertEquals('my-key',  $route);
		$this->assertEquals('console', $strategy);
	}
}
