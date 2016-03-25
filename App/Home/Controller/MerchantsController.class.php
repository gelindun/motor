<?php

namespace Home\Controller;
use Think\Controller;

class MerchantsController extends HomeController {
    private $D_AdminArticle;
    private $D_AdminArticleType;

    public function _initialize() {
        parent::_initialize();
        $this->D_Merchant =D('site\Merchant');
    }
    
    public function index(){
        $_type = I('get.tid');
        $_where = array();
        $_order = array(
            'id' => "DESC"
        );
        $_resList = $this->D_Merchant->getAllPagesize($_where,$_order);
        
        foreach($_resList['lists'] as $k=>$v){
            $_resList['lists'][$k]['content'] = htmlspecialchars_decode($v['content']);
            $_resList['lists'][$k]['content'] = strip_tags($_resList['lists'][$k]['content']);
        }
        $this->_arr['resList'] = $_resList;
        
        $this->_showDisplay();
    }
    
    public function detail(){
        $_id = (int)I('get.id');
        
        $_where = array(
            'id' => $_id
        );
        $_rst = $this->D_Merchant->where($_where)->find();
        if(!$_rst){
            $this->redirect('/');
        }
        $_rst['content'] = htmlspecialchars_decode($_rst['content']);
        $this->_arr['resPage'] = $_rst;
        $this->D_AdminArticle->where($_where)->setInc('view_count');
        
        $this->_arr['seo_title'] = $this->_arr['seo_keywords'] = $_rst['title'];
        $this->_arr['seo_description'] = msubstr(strip_tags($_rst['content']),0,100);
        $this->_showDisplay();
    }
}
