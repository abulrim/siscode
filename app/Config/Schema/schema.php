<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $courses = array(
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
	public $instructors = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'firstname' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'surname' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
	public $subjects = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
	);
}
