<?php
class CoursesController extends AppController {
	public $helpers = array('Cache');

	public function index() {
		if (Configure::read('debug') == 0) {
			$this->cacheAction = '+1 day';
		}
		$this->Course->Subject->Institution->contain(array(
			'Subject' => array(
				'order' => array(
					'Subject.name' => 'ASC'
				)
			),
			'Subject.Course' => array(
				'fields' => array('DISTINCT Course.number')
			)
		));
		$institutions = $this->Course->Subject->Institution->find('all');

		$cleanedArrays = array();
		foreach($institutions as $institution) {
			$cleanedArray = $institution['Institution'];
			$subjects = $institution['Subject'];

			$cleanSubjects = array();
			foreach ($subjects as $subject) {
				$subject['numbers'] = Hash::extract($subject, 'Course.{n}.number');
				unset($subject['Course']);
				$cleanSubjects[] = $subject;
			}

			$cleanedArray['subjects'] = $cleanSubjects;
			$cleanedArrays[] = $cleanedArray;
		}
		$this->set('institutions', $cleanedArrays);
	}

	public function fetch($data) {
		$courses = array();
		$inputs = array();
		$urlData = explode('_', $data);
		$institution = $urlData[0];
		$page = $urlData[1];
		$days = explode('-', $urlData[2]);
		$urlData = array_slice($urlData, 3);
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
		$cacheName = $institution . '_' . $implodedDays . '_' . implode('_', $combinedInputs);
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
							'Subject.institution_id' => $institution,
							'Course.crn' => $input['crn']
						);
					}

					$this->Course->contain(array(
						'Subject',
						'CourseSlot' => array(
							'Instructor'
						)
					));
					$fetchedCourse = $this->Course->find('all', array(
						'conditions' => array_merge($daysConditions, $conditions)
					));
					if (!empty($fetchedCourse)) {
						$courses[] = $fetchedCourse;
					} else {
						$courses = array();
						break;
					}
				}
				$combinationsCourseIds = array();
				if (!empty($courses)) {
					$combinations = $this->_getCombinations($courses, $combinations);
					foreach($combinations as $combination) {
						$combinationsCourseIds[] = Hash::extract($combination, '{n}.Course.id');
					}
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
					$this->Course->contain(array(
						'Subject',
						'CourseSlot' => array(
							'Instructor'
						)
					));
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
		if (empty($courses)) {
			$count = $page = 0;
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
			if (empty($combinations)) {
				return $combinations;
			}
		}
		return $combinations;
	}

	//returns all the combinations between $combinations and $input
	protected function _checkConflict($combinations, $input) {
		$newCombination = array();

		//First run fill the combinations array with the first input
		if (empty($combinations)) {
			foreach ($input as $key => $el) {
				$input[$key] = array($el);
			}
			return $input;
		}
		//If not first run loop through the rest of the inputs
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

	public function clear_cache($key) {

		if ($key != Configure::read('CacheKey')) {
			exit('Not done!');
		}

		//delete cached json courses
		$mydir = APP . 'webroot' . DS . 'courses' . DS . 'fetch' . DS;
		$this->_deleteFiles($mydir);

		//delete cached cake models
		$mydir = APP . 'tmp' . DS . 'cache' . DS . 'models' . DS;
		$this->_deleteFiles($mydir);

		//delete cached files in persistent
		$mydir = APP . 'tmp' . DS . 'cache' . DS . 'persistent' . DS;
		$this->_deleteFiles($mydir);

		//delete cached views
		$mydir = APP . 'tmp' . DS . 'cache' . DS . 'views' . DS;
		$this->_deleteFiles($mydir);


		Cache::clear();
		clearCache();
		exit('Done!');
	}

	protected function _deleteFiles($dir) {
		foreach(glob($dir.'*') as $entry) {
			$fileName = explode('/', $entry);
			$fileName = $fileName[count($fileName)-1];
			if ($fileName !== 'empty') {
				unlink($entry);
			}
		}
	}
}
