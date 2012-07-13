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
        
        
        public function css($path,$rel=null,$options=array()){
            $fileName=$path;
            
            if(!preg_match("/http|min|www/",$path)){
                $path=str_replace('.css','',$path);
                $fullMinPath=APP . DS . 'webroot' . DS .'css' . DS . str_replace('/',DS,$path). '.min.css';
                
                /*
                if(Configure::read('debug')>0){
                     $fullPath=APP . DS . 'webroot'. DS .'css' . DS . str_replace('/',DS,$path). '.css';
                     //YuiCompressor::compress($fullPath,$fullMinPath);
                }
                 */
                if(Configure::read('debug')== 0 && file_exists($fullMinPath)){
                    $fileName=$path.'.min';
                }
            }
            if (isset($options['inline']) && !$options['inline']) {
                    $options['inline']=true;
                    $this->cssTags.=$this->Html->css($fileName,$rel,$options);
                    return '';
            } else {
                    return $this->Html->css($fileName,$rel,$options);
            }
            
        }
        
        public function script($path, $options=array()){
            $fileName=$path;
            
             if(!preg_match("/http|min|www/",$path)){
                $path=str_replace('.js','',$path);
                $fullMinPath=APP . DS . 'webroot' . DS .'js' . DS . str_replace('/',DS,$path). '.min.js';
               /*
                if(Configure::read('debug')>0){
                
                     $fullPath=APP . DS . 'webroot'. DS .'js' . DS . str_replace('/',DS,$path). '.js';
                     //YuiCompressor::compress($fullPath,$fullMinPath);
                }
                
                */
                if(Configure::read('debug')== 0 && file_exists($fullMinPath)){
                    $fileName=$path.'.min';
                }
            }
            
            if (isset($options['inline']) && !$options['inline']) {
                    $options['inline']=true;
                    $this->scriptTags.=$this->Html->script($fileName,$options);
                    return '';
            } else {
                    return $this->Html->script($fileName,$options);
            }
            
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
