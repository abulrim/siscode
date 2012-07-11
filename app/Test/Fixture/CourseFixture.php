<?php
/**
 * CourseFixture
 *
 */
class CourseFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'term' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'crn' => array('type' => 'integer', 'null' => true, 'default' => null),
		'subject_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'course_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'section' => array('type' => 'integer', 'null' => true, 'default' => null),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'begin_time_1' => array('type' => 'integer', 'null' => true, 'default' => null),
		'end_time_1' => array('type' => 'integer', 'null' => true, 'default' => null),
		'building_1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'room_1' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'm_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		't_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'w_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'r_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'f_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'sat_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'sun_1' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'begin_time_2' => array('type' => 'integer', 'null' => true, 'default' => null),
		'end_time_2' => array('type' => 'integer', 'null' => true, 'default' => null),
		'building_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'room_2' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'm_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		't_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'w_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'r_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'f_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'sat_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'sun_2' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'instructor_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'term' => 'Lorem ipsum dolor ',
			'crn' => 1,
			'subject_id' => 1,
			'course_number' => 'Lorem ipsum dolor ',
			'section' => 1,
			'title' => 'Lorem ipsum dolor sit amet',
			'begin_time_1' => 1,
			'end_time_1' => 1,
			'building_1' => 'Lorem ipsum dolor ',
			'room_1' => 'Lorem ipsum dolor ',
			'm_1' => 1,
			't_1' => 1,
			'w_1' => 1,
			'r_1' => 1,
			'f_1' => 1,
			'sat_1' => 1,
			'sun_1' => 1,
			'begin_time_2' => 1,
			'end_time_2' => 1,
			'building_2' => 'Lorem ipsum dolor ',
			'room_2' => 'Lorem ipsum dolor ',
			'm_2' => 1,
			't_2' => 1,
			'w_2' => 1,
			'r_2' => 1,
			'f_2' => 1,
			'sat_2' => 1,
			'sun_2' => 1,
			'instructor_id' => 1
		),
	);

}
