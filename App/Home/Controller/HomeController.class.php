<?php

namespace Home\Controller;
use Think\Controller;

class HomeController extends \Common\Controller\CommonController {

    public function _initialize() {
        parent::_initialize();
        
        if((int)$this->_arr[self::FRONT_UID]){
            $D_User = D('my\Member');
            $_rst = $D_User->loginInfo($this->_arr[self::FRONT_UID]);
            $this->_arr['my_info'] = $_rst;
        }
    }
    
}