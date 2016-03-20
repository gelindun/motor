<?php

namespace Admin\Controller;
use Think\Controller;

class PriceController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        
        $this->_showDisplay();
    }


    
    
}
