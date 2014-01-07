<?php
App::uses('AppModel', 'Model');
/**
 * Subject Model
 *
 */
class Subject extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

  public $belongsTo = array('Institution');
	public $hasMany = array('Course');

}
