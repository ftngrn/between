<?php
App::uses('Map', 'Model');

/**
 * Map Test Case
 *
 */
class MapTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.map',
		'app.sender',
		'app.user_email',
		'app.user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Map = ClassRegistry::init('Map');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Map);

		parent::tearDown();
	}

}
