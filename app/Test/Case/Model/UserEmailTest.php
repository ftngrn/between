<?php
App::uses('UserEmail', 'Model');

/**
 * UserEmail Test Case
 *
 */
class UserEmailTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
		$this->UserEmail = ClassRegistry::init('UserEmail');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserEmail);

		parent::tearDown();
	}

}
