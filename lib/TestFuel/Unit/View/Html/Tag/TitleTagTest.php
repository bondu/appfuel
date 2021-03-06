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
namespace TestFuel\Unit\View\Html\Tag;

use StdClass,
	SplFileInfo,
	Appfuel\View\Html\Tag\TitleTag,
	TestFuel\TestCase\BaseTestCase;

/**
 * The html element tag is used to automate the rendering of the html element
 * and provide a simpler interface to add data to the element
 */
class TitleTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $title = null;

	/**
	 * Passed into the constructor to be added to the title content
	 * @var string
	 */
	protected $content = null;

	/**
	 * Second parameter used in the constructor for content separator
	 * @var string
	 */
	protected $separator = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->content   = 'This is a title';
		$this->separator = ':';
        $this->title = new TitleTag($this->content, $this->separator);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->title);
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->title
		);

		/*
		 * constructor should have added the content because it was a string 
		 */
		$this->assertFalse($this->title->isEmpty());
		$this->assertEquals($this->content, $this->title->getContentString());
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = "<title>{$this->content}</title>";
		$this->assertEquals($expected, $this->title->build());

		$this->title->addContent('more data');
		$content = $this->content . $this->separator . 'more data';
		$expected = "<title>{$content}</title>";
		$this->assertEquals($expected, $this->title->build());
	}
}
