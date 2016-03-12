<?php

namespace Admin\Controller;
use Think\Controller;

class IndexController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        
        $this->_showDisplay();
        
    }
    
    
}
