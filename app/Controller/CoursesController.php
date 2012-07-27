<?php
class CoursesController extends AppController {
	public function index() {
		$this->Course->Subject->contain(array(
			'Course' => array(
				'fields' => array('DISTINCT Course.number')
			)
		));
		$subjects = $this->Course->Subject->find('all', array(
			'order' => array(
				'Subject.name' => 'ASC'
			)
		));
		$cleanedSubjects = array();
		foreach($subjects as $subject) {
			$cleanedSubject = array(
				'id' => $subject['Subject']['id'],
				'name' => $subject['Subject']['name'],
				'numbers' => array()
			);
			foreach($subject['Course'] as $courseNumber) {
				$cleanedSubject['numbers'][] = $courseNumber['number'];
			}
			$cleanedSubjects[] = $cleanedSubject;
		}
		$subjects = $cleanedSubjects;
		$this->set(compact('subjects'));
	}
	
	public function fetch($data) {
		sleep(2);
		$courses = array();
		$inputs = array();
		$urlData = explode('_', $data);
		$page = $urlData[0];
		$days = explode('-', $urlData[1]);
		$urlData = array_slice($urlData, 2);
		foreach($urlData as $key => $course) {
			$exploded = explode('-', $course);
			$inputs[$key]['subject_id'] = $exploded[0];
			$inputs[$key]['number'] = $exploded[1];
			$inputs[$key]['crn'] = $exploded[2];
		}
		$implodedDays = implode('-', $days);
		$daysConditions = array();
		$daysConditions = $this->_getDaysConditions($days);
		
		//Check if course combinations cached
		$unsortedCombinedInputs = array();
		
		foreach($inputs as $key => $input) {
			if (!empty($input['crn'])) {
				$input['subject_id'] = $input['number'] = '';
				$inputs[$key] = $input;
			} 
			if (empty($input['crn']) && empty($input['subject_id']) && empty($input['number'])) {
				unset($inputs[$key]);
			} else {
				$unsortedCombinedInputs[] = implode('-', $input);
			}
		}
		
		$combinedInputs = Hash::sort($unsortedCombinedInputs, '{n}', 'asc');
		$cacheName = $implodedDays . '_' . implode('_', $combinedInputs);
		$cache = Cache::read($cacheName);
		
		if (!empty($inputs) && count($inputs) <= 7) {
			if (!$cache) {
				$popedInputs = 0;
				$combinations = array();

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
				$combinations = $this->_getCombinations($courses, $combinations);
				$combinationsCourseIds = array();
				foreach($combinations as $combination) {
					$combinationsCourseIds[] = Hash::extract($combination, '{n}.Course.id');
				}
				//Cache only course ids combinations
				Cache::write($cacheName, $combinationsCourseIds);

				$count = count($combinations);

				if ($page > $count) {
					$courses = array();
				} else {
					$courses = $combinations[$page - 1];
				}
			} else {
				$count = count($cache);
				if ($page > $count) {
					$courses = array();
				} else {

					$courseIds = $cache[$page - 1];
					$this->Course->contain('CourseSlot');
					$courses = $this->Course->find('all', array(
						'conditions' => array(
							'Course.id' => $courseIds
						)
					));
					$coursesById = array();
					foreach($courses as $course) {
						$coursesById[$course['Course']['id']] = $course;
					}
					$courses = array();
					foreach($courseIds as $courseId) {
						$courses[] = $coursesById[$courseId];
					}
				}
			}
		}
		
		$content = array(
			'status' => 'success',
			'content' => $courses,
			'pagination' => array(
				'total' => $count,
				'page' => $page
			)
		);
		
		//server caching
		$fileName = APP . 'webroot' . DS . 'courses' . DS . 'fetch' . DS . $data . '.json';
		if (file_exists($fileName)) {
			unlink($fileName);
		}
		$openedFile = fopen($fileName, 'w');
		fwrite($openedFile, json_encode($content));
		fclose($openedFile);
		
		$this->set(compact('content'));
		$this->set('_serialize', 'content');
	}
	
	protected function _getCombinations($inputs, $combinations = array()) {
		
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
	
	public function clear_cache() {
		$mydir = APP . 'webroot' . DS . 'courses' . DS . 'fetch' . DS;
		foreach(glob($mydir.'*.*') as $entry) {
			unlink($entry);
		}
		Cache::clear();
		exit('Done!');
	}
}