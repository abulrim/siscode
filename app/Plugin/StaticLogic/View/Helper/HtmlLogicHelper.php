<?php
class HtmlLogicHelper extends AppHelper{

	var $helpers=array('Html');
        
        protected $_settings=array();
        
        public $cssTags='';
        
        public $scriptTags='';
        
        
        protected $_later='';
	
	
        function __construct(View $view, $settings = array()) {
            parent::__construct($view, $settings);
        }
        
        public function less($path, $lessScript = null) {
		$absolutePath = APP . 'webroot' . DS . 'css' . DS;
		
		$string = '<link rel="stylesheet/less" type="text/css" href="' . $this->webroot . 'css/' . $path . '.less" />';
		if (Configure::read('debug') == 0 && file_exists($absolutePath . $path . '.css')) {
			if(file_exists($absolutePath . $path . '.min.css')) {
				$file = $path . '.min';
			} else {
				$file = $path;
			}
			$string = $this->Html->css($file);
		} elseif ($lessScript) {
			$string .= $this->Html->script($lessScript);
		}
		
		return $string;
	}
	
	
	
        public function css($paths,$rel=null,$options=array()){
		$options = Hash::merge(array(
			'inline' => true,
			'combined' => false
		), $options);

		if (!is_array($paths)) {
			$paths = array($paths);
		}
		$stringToReturn = '';
		$fileNames = array();
		$currentOptions = Hash::merge($options, array(
				'inline' => true
		));
		unset($currentOptions['combined']);
		
		foreach($paths as $path) {
			$fileName = $path;
			if (!preg_match("/http|min|www/", $path)) {
				$path = str_replace('.css', '', $path);
				$fullMinPath = APP . DS . 'webroot' . DS .'css' . DS . str_replace('/', DS, $path) . '.min.css';
			
				if (Configure::read('debug') == 0 && file_exists($fullMinPath)) {
					$fileName = $path . '.min';
				}
			}
			$fileNames[] = $fileName;
			
			$stringToReturn .= $this->Html->css($fileName, null, $currentOptions);

		}
		
		if (Configure::read('debug') == 0 && $options['combined']) {
			$combined = sha1(implode($fileNames, '__'));
			$path = APP . 'webroot' . DS . 'css' . DS;
			$combinedFileName = $path . $combined . '.css';
			$createFile = true;
			$keep = false;
			if (file_exists($combinedFileName)) {
				$keep = true;
				$lastModified = filemtime($combinedFileName);
				foreach($fileNames as $fileName) {
					$completeFileName = $path . $fileName . '.css';
					$currentModified = filemtime($currentModified);
					if ($currentModified > $lastModified) {
						$keep = false;
						break;
					}
				}
				if (!$keep) {
					unlink($combinedFileName);
				}
			} 
			if (!$keep) {
				FireCake::log('generate');
				$handle = fopen($combinedFileName, 'w');
				fwrite($fhandle, '');
				fclose($handle);
				$handle = fopen($combinedFileName, 'a');
				foreach($fileNames as $fileName){
					$completeFileName = $path . $fileName . '.css';
					$currentHandle = fopen($completeFileName, 'r');
					$content = fread($currentHandle, filesize($completeFileName));
					fclose($currentHandle);
					fwrite($handle, $content);
				}
				fclose($handle);
			}
			

			$stringToReturn = $this->Html->css($combined, null, $currentOptions);
		}
		
		if (!$options['inline']) {
			$this->scriptTags .= $stringToReturn;
			$stringToReturn = '';
		}
		
		return $stringToReturn;
		
//            $fileName=$path;
//            
//            if(!preg_match("/http|min|www/",$path)){
//                $path=str_replace('.css','',$path);
//                $fullMinPath=APP . DS . 'webroot' . DS .'css' . DS . str_replace('/',DS,$path). '.min.css';
//                
//                /*
//                if(Configure::read('debug')>0){
//                     $fullPath=APP . DS . 'webroot'. DS .'css' . DS . str_replace('/',DS,$path). '.css';
//                     //YuiCompressor::compress($fullPath,$fullMinPath);
//                }
//                 */
//                if(Configure::read('debug')== 0 && file_exists($fullMinPath)){
//                    $fileName=$path.'.min';
//                }
//            }
//            if (isset($options['inline']) && !$options['inline']) {
//                    $options['inline']=true;
//                    $this->cssTags.=$this->Html->css($fileName,$rel,$options);
//                    return '';
//            } else {
//                    return $this->Html->css($fileName,$rel,$options);
//            }
            
        }
        
        public function script($paths, $options=array()){
		$options = Hash::merge(array(
			'inline' => true,
			'combined' => false
		), $options);

		if (!is_array($paths)) {
			$paths = array($paths);
		}
		$stringToReturn = '';
		$fileNames = array();
		$currentOptions = Hash::merge($options, array(
				'inline' => true
		));
		unset($currentOptions['combined']);
		
		foreach($paths as $path) {
			$fileName = $path;
			if (!preg_match("/http|min|www/", $path)) {
				$path = str_replace('.js', '', $path);
				$fullMinPath = APP . DS . 'webroot' . DS .'js' . DS . str_replace('/', DS, $path) . '.min.js';
			
				if (Configure::read('debug') == 0 && file_exists($fullMinPath)) {
					$fileName = $path . '.min';
				}
			}
			$fileNames[] = $fileName;
			
			
			$stringToReturn .= $this->Html->script($fileName,$currentOptions);

		}
		if (Configure::read('debug') == 0 && $options['combined']) {
			$combined = sha1(implode($fileNames, '__'));
			$path = APP . 'webroot' . DS . 'js' . DS;
			$combinedFileName = $path . $combined . '.js';
			$createFile = true;
			$keep = false;
			if (file_exists($combinedFileName)) {
				$keep = true;
				$lastModified = filemtime($combinedFileName);
				foreach($fileNames as $fileName) {
					$completeFileName = $path . $fileName . '.js';
					$currentModified = filemtime($currentModified);
					if ($currentModified > $lastModified) {
						$keep = false;
						break;
					}
				}
				if (!$keep) {
					unlink($combinedFileName);
				}
			} 
			if (!$keep) {
				FireCake::log('generate');
				$handle = fopen($combinedFileName, 'w');
				fwrite($fhandle, '');
				fclose($handle);
				$handle = fopen($combinedFileName, 'a');
				foreach($fileNames as $fileName){
					$completeFileName = $path . $fileName . '.js';
					$currentHandle = fopen($completeFileName, 'r');
					$content = fread($currentHandle, filesize($completeFileName));
					fclose($currentHandle);
					fwrite($handle, $content);
				}
				fclose($handle);
			}
			

			$stringToReturn = $this->Html->script($combined, $currentOptions);
		}
		
		if (!$options['inline']) {
			$this->scriptTags .= $stringToReturn;
			$stringToReturn = '';
		}
		
		return $stringToReturn;
        }
        
        public function later($string=null) {
                if ($string === null) {
                        return $this->_later;
                } else {
                        $this->_later.=$string;
                }
        }
        
	
	public function startTemplate($options = array()) {
		$string = '<script type="text/template" ';
		if (isset($options['id'])) {
			$string .= 'id="' . $options['id'] . '" ';
		}
		$string .= '>';
		return $string;
	}
	
	public function endTemplate() {
		return '</script>';
	}
}
