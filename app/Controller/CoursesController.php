<?php
class CoursesController extends AppController {
	public function index() {
		$subjects = $this->Course->Subject->find('all', array(
			'order' => array(
				'Subject.name' => 'ASC'
			)
		));
		$this->set(compact('subjects'));
	}
}