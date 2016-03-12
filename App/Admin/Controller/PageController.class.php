<?php

namespace Admin\Controller;
use Think\Controller;
//s_type about 关于我们 solution 解决方案
class PageController extends AdminController {
   

    public function _initialize() {
         parent::_initialize();
         
    }
    
    public function about(){
        $D_Page = D('site\Page');
        $_where = array(
            "s_type" => "about"
        );
        $_page = $D_Page->where($_where)->find();
        if($_page){
            $_where_p = $_where;
        }
        $_page['s_type'] = "about";
        if(I('post.action') === 'edit_page'){
            $_data = I('post.');
            unset($_data['action']);
            $_data['time_show'] = strtotime($_data['time_show']);
            if($D_Page->write($_data,$_where_p)){
                pushJson('更新成功');
            }else{
                pushError ('更新失败');
            }
        }
        $_page['content'] = htmlspecialchars_decode($_page['content']);
        $this->_arr['resPage'] = $_page;
        $this->_showDisplay();
    }
    
    public function solution(){
        $D_Page = D('site\Page');
        $_where = array(
            "s_type" => "solution"
        );
        $_page = $D_Page->where($_where)->find();
        if($_page){
            $_where_p = $_where;
        }
        $_page['s_type'] = "solution";
        if(I('post.action') === 'edit_page'){
            $_data = I('post.');
            unset($_data['action']);
            $_data['time_show'] = strtotime($_data['time_show']);
            if($D_Page->write($_data,$_where_p)){
                pushJson('更新成功');
            }else{
                pushError ('更新失败');
            }
        }
        $_page['content'] = htmlspecialchars_decode($_page['content']);
        $this->_arr['resPage'] = $_page;
        $this->_showDisplay();
    }

    
}
