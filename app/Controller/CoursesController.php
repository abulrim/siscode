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
	
	public function fetch() {
		
		$courses = $this->Course->find('all', array(
			'limit' => 5,
			'order' => 'RAND()'
		));
		
		$content = array(
			'status' => 'success',
			'content' => $courses
		);
		$this->set(compact('content'));
		$this->set('_serialize', 'content');
	}
}