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
		
		$courses = array();
		
		$daysConditions = array();
		$daysConditions = $this->_getDaysConditions($this->request->data['days']);
		
		$inputs = $this->request->data['course'];
		
		foreach($inputs as $input) {
			if (empty($input['crn'])) {
				$conditions = array(
					'Course.subject_id' => $input['subject_id'],
					'Course.number' => $input['number']
				);
			} else {
				$conditions = array(
					'Course.crn' => $input['crn']
				);
			}
			$this->Course->contain('CourseSlot');
			$fetchedCourse = $this->Course->find('all', array(
				'conditions' => array_merge($daysConditions, $conditions)
			));
			if (!empty($fetchedCourse)) {
				$courses[] = $fetchedCourse;
			}
		}
		
		$combinations = $this->_getCombinations($courses);
		$count = count($combinations);
		$page = $this->request->data['page'];
		
		if ($page > $count) {
			$courses = array();
		} else {
			$courses = $combinations[$page - 1];
		}
		
		$content = array(
			'status' => 'success',
			'content' => $courses,
			'pagination' => array(
				'total' => $count,
				'page' => $page
			)
		);
		$this->set(compact('content'));
		$this->set('_serialize', 'content');
	}
	
	protected function _getCombinations($inputs) {
		$combinations = array();
		
		foreach($inputs as $input) {
			$combinations = $this->_checkConflict($combinations, $input);
		}
		return $combinations;
	}
	
	//returns all the combinations between $combinations and $input
	protected function _checkConflict($combinations, $input) {
		$newCombination = array();
		
		if (empty($combinations)) {
			foreach ($input as $key => $el) {
				$input[$key] = array($el);
			}
			return $input;
		}
		foreach($input as $course) {
			foreach($combinations as $combination) {
				if (!$this->_isTimeConflict($combination, $course)) {
					$combination[] = $course;
					$newCombination[] = $combination;
				}
			}
		}
		return $newCombination;
	}
	
	//returns if time conflict occurs
	protected function _isTimeConflict($combination, $course) {
		foreach($combination as $el) {
			foreach($el['CourseSlot'] as $slot) {
				foreach($course['CourseSlot'] as $courseSlot) {
					if ($courseSlot['day'] == $slot['day']) {
						$firstStartTime = $courseSlot['start_time'];
						$firstEndTime = $courseSlot['end_time'];
						$secondStartTime = $slot['start_time'];
						$secondEndTime = $slot['end_time'];
						
						if (($firstStartTime <= $secondEndTime) && ($firstStartTime >= $secondStartTime)) {
							return true;
						} elseif (($firstEndTime <= $secondEndTime) && ($firstEndTime >= $secondStartTime)) {
							return true;
						} elseif (($firstEndTime >= $secondEndTime) && ($firstStartTime <= $secondStartTime)) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	protected function _getDaysConditions($requestedDays) {
		$days = array('m', 't', 'w', 'r', 'f', 's');
		$daysConditions = array();

		for ($i=1; $i <= 6; $i++) {
			if (!in_array($i, $requestedDays)) {
				$daysConditions['Course.' . $days[$i-1]] = 0;
			}
		}
		return $daysConditions;
	}
}