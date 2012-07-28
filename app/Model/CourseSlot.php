<?php
App::uses('AppModel', 'Model');

class CourseSlot extends AppModel {
	public $belongsTo = 'Instructor';
}
