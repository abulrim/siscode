<?php
class M002AddedBasicDatabaseStructure extends CakeMigration {

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
				'courses' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'term' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'crn' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'subject_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'course_number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
					'section' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
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
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'),
				),
				'instructors' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'firstname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'surname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'),
				),
				'subjects' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'courses', 'instructors', 'subjects'
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
