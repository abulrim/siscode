<?php
App::uses('Instructor', 'Model');

/**
 * Instructor Test Case
 *
 */
class InstructorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.instructor'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Instructor = ClassRegistry::init('Instructor');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Instructor);

		parent::tearDown();
	}

}
