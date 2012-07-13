<?php
App::uses('AppModel', 'Model');

class Course extends AppModel {

	public $displayField = 'title';
	public $belongsTo = array('Subject');
}
