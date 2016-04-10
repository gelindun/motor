<?php

namespace Admin\Controller;
use Think\Controller;
//s_type about,company,product,help
class PageController extends AdminController {
   

    public function _initialize() {
         parent::_initialize();
         
    }

    public function index(){
        $D_Page = D('site\Page');
        $this->_arr['keyArr'] = $D_Page->pgKeys();
        $this->note_rules = $D_Page->vRule();
        if(I('post.action') === 'edit_page'){
            $_data = I('post.');
            $_data['hide_logo'] = $_data['hide_logo']?1:0;
            $_data['time_show'] = time();
            unset($_data['action']);
            $rules = $this->note_rules;
            if ($D_Page->validate($rules)->create($_data)){
                $_where = array();
                $_where_s = array(
                        's_key' => $_data['s_key']
                    );
                if($D_Page->where($_where_s)->find()){
                    $_where = $_where_s;
                }
                $D_Page->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_Page->getError());
            }
        }
        $_action = I('get.act');
        if($_action === 'edit'){
            $_tem_str = 'about';
            
            $_key = I('get.key');
            if($this->_arr['keyArr'][$_key]){
                $_where = array('s_key'=>$_key);
                $_resPage = $D_Page->where($_where)->find();
                if($_resPage){
                    $this->_arr['resPage'] = $_resPage;
                    $this->_arr['resPage']['content'] = htmlspecialchars_decode($this->_arr['resPage']['content']);
                }
            }
            $this->_arr['pageTitle'] = $this->_arr['keyArr'][$_key]['title'];
            $this->_arr['s_key'] = $_key;
            $this->_showDisplay($_tem_str);
            exit;
        }
        $this->_showDisplay();
    }

    
    
   
    
}
