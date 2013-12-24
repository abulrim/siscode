<?php
App::uses('AppModel', 'Model');

class Institution extends AppModel {

  public $hasMany = array('Subject');
}
