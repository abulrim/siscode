<?php
class M005ChangedInstructorFixedDayFilter extends CakeMigration {

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
			'create_field' => array(
				'courses' => array(
					'm' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'title'),
					't' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'm'),
					'w' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 't'),
					'r' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'w'),
					'f' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'r'),
					's' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'f'),
				),
				'instructors' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'after' => 'id'),
				),
			),
			'drop_field' => array(
				'instructors' => array('firstname', 'surname',),
			),
		),
		'down' => array(
			'drop_field' => array(
				'courses' => array('m', 't', 'w', 'r', 'f', 's',),
				'instructors' => array('name',),
			),
			'create_field' => array(
				'instructors' => array(
					'firstname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'surname' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
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
