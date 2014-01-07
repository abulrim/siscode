<?php
class BuildController extends AppController {

	public $uses = array();

	public function minify() {
		App::import('StaticLogic.Vendor','YuiCompressor/YuiCompressor');

		$jsDirectory=APP .'webroot' . DS . 'js' . DS;
		$jsFiles=Configure::read('StaticLogic.js');

		$cssDirectory=APP .'webroot' . DS . 'css' . DS;
		$cssFiles=Configure::read('StaticLogic.css');

		foreach($jsFiles as $file){
			$fullPath=$jsDirectory . $file . '.js';
			$fullMinPath=$jsDirectory. $file . '.min.js';

			if(file_exists($fullMinPath)){
				unlink($fullMinPath);
			}
			YuiCompressor::compress($fullPath,$fullMinPath);
		}

		//build less
		$lessFiles = Configure::read('StaticLogic.less');

		if (!empty($lessFiles)) {
			foreach($lessFiles as $file) {
				$fullPath = $cssDirectory . $file . '.less';
				$fullCompiledPath = $cssDirectory . $file . '.css';
				if (file_exists($fullCompiledPath)) {
					unlink($fullCompiledPath);
				}
				if (file_exists($fullPath)) {
					$command='lessc ' . $fullPath . ' > ' . $fullCompiledPath;
					$result = exec($command);
				}

			}
		}


		foreach($cssFiles as $file){
			$fullPath=$cssDirectory . $file . '.css';
			$fullMinPath=$cssDirectory. $file . '.min.css';

			if(file_exists($fullMinPath)){
				unlink($fullMinPath);
			}
			YuiCompressor::compress($fullPath,$fullMinPath);
		}



		exit('done');

	}
}
