<?php
class BuildController extends AppController {
	
	public $uses = array();

	public function minify() {
		App::import('StaticLogic.Vendor','YuiCompressor/YuiCompressor');

		$jsDirectory=APP. DS .'webroot' . DS . 'js' . DS;
		$jsFiles=Configure::read('StaticLogic.js');

		$cssDirectory=APP. DS .'webroot' . DS . 'css' . DS;
		$cssFiles=Configure::read('StaticLogic.css');

		foreach($jsFiles as $file){
			$fullPath=$jsDirectory . $file . '.js';
			$fullMinPath=$jsDirectory. $file . '.min.js';

			if(file_exists($fullMinPath)){
				unlink($fullMinPath);
			}
				YuiCompressor::compress($fullPath,$fullMinPath);
			}

		foreach($cssFiles as $file){
			$fullPath=$cssDirectory . $file . '.css';
			$fullMinPath=$cssDirectory. $file . '.min.css';

			if(file_exists($fullMinPath)){
				unlink($fullMinPath);
			}
			YuiCompressor::compress($fullPath,$fullMinPath);
		}

		//if($exit) {
			echo 'done';exit();
		//}
	}
}