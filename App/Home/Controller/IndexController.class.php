<?php

namespace Home\Controller;
use Think\Controller;

class IndexController extends HomeController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        $this->D_AdminArticle =D('site\AdminArticle');
       	$_where = array();
       	$_order = array(
            'time_show' => "DESC"
        );
        $_resList = $this->D_AdminArticle->rtnList($_where,10,$_order);
        $this->_arr['rstArticle'] = $_resList;
        $this->_showDisplay('Index:v3');
    }

    public function v2(){
        
        $this->_showDisplay();
    }

    public function v3(){
        
        $this->_showDisplay();
    }

    public function v4(){
        $this->_showDisplay();
    }
   
}
