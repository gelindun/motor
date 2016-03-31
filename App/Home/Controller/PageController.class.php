<?php

namespace Home\Controller;
use Think\Controller;
//s_type about,company,product,help
class PageController extends HomeController {
   

    public function _initialize() {
         parent::_initialize();
         
    }

    public function index(){
        $D_Page = D('site\Page');
        $this->_arr['keyArr'] = $D_Page->pgKeys();
        
        $_action = I('get.act');
        
        $_key = I('get.key');
        if($this->_arr['keyArr'][$_key]){
            $_where = array('s_key'=>$_key);
            $_resPage = $D_Page->where($_where)->find();
            if($_resPage){
                $this->_arr['resPage'] = $_resPage;
                $this->_arr['resPage']['content'] = htmlspecialchars_decode($this->_arr['resPage']['content']);
            }
        }else{
            exit;
        }
        $this->_arr['pageTitle'] = $this->_arr['keyArr'][$_key]['title'];
        $this->_arr['seo_keywords'] = $this->_arr['pageTitle'];
        $this->_arr['seo_description'] = msubstr(strip_tags($this->_arr['resPage']['content']),0,100);
        $this->_arr['s_key'] = $_key;
        $this->_showDisplay();
    }

    
    
   
    
}
