<?php

namespace Admin\Controller;
use Think\Controller;

class AreaController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        
        $this->_showDisplay();
    }

    public function loadArea(){
    	$_pid = (int)I('get.pid');
    	$D_Area = D('site\Area');
    	$_where = array(
    		"pid" => $_pid 
    	);
    	$_order = array(
    			"id" => "ASC"
    		);
    	$_resList = $D_Area->getAllPagesize($_where,$_order);
    	pushJson('ok',array(
    			"lists" => $_resList['lists'],
    			"trace" => M()->_sql()
    		));
    }


    
    
}
