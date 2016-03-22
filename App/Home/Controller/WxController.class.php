<?php

namespace Home\Controller;


class WxController extends HomeController {

    public function _initialize() {
       parent::_initialize();
    }
    
    public function index(){
        import("Vendor.Weixin.wechat");
        $_options = array(
            'token'=> $this->_arr['WX_BASE']['wx_token'],
            'appid'=> $this->_arr['WX_BASE']['wx_appid'],
            'appsecret'=> $this->_arr['WX_BASE']['wx_appsecret']
        );
        $weObj = new \Wechat($_options);
        $weObj->valid();
        
    }
}
