<?php

namespace Home\Controller;
use Think\Controller;

class IndexController extends HomeController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        
       
        
        $this->_showDisplay();
    }
   
}
