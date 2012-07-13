<?php
class M004AddedCourseSlots extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'course_slots' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'start_time' => array('type' => 'time', 'null' => false, 'default' => NULL, 'after' => 'id'),
					'end_time' => array('type' => 'time', 'null' => false, 'default' => NULL, 'after' => 'start_time'),
					'building' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'after' => 'end_time'),
					'room' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'after' => 'building'),
					'instructor_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'room'),
					'day' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'instructor_id'),
					'course_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'after' => 'day'),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'after' => 'course_id'),
					'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'after' => 'created'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'courses' => array(
					'number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1', 'after' => 'subject_id'),
				),
			),
			'drop_field' => array(
				'courses' => array('course_number', 'begin_time_1', 'end_time_1', 'building_1', 'room_1', 'm_1', 't_1', 'w_1', 'r_1', 'f_1', 'sat_1', 'sun_1', 'begin_time_2', 'end_time_2', 'building_2', 'room_2', 'm_2', 't_2', 'w_2', 'r_2', 'f_2', 'sat_2', 'sun_2', 'instructor_id',),
			),
		),
		'down' => array(
			'drop_table' => array(
				'course_slots'
			),
			'drop_field' => array(
				'courses' => array('number',),
			),
			'create_field' => array(
				'courses' => array(
					'course_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'begin_time_1' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'end_time_1' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'building_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'room_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'm_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					't_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'w_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'r_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'f_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'sat_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'sun_1' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'begin_time_2' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'end_time_2' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'building_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'room_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'm_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					't_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'w_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'r_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'f_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'sat_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'sun_2' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'instructor_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
