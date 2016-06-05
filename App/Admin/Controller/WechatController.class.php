<?php

namespace Admin\Controller;
use Think\Controller;

class WechatController extends AdminController {

    public function _initialize() {
         parent::_initialize();
    }
    
    public function news(){
        $do_action = I('get.action');
        $D_MaterialNews = D('wx\MaterialNews');
        $temp = 'news';
        if($do_action == 'edit'){

            if(I('post.do_action') == 'sub_news'){
                $_data = I('post.data');
                foreach ($_data as $key => $value) {
                    $name[$key] = $value['order_id'];
                }
                array_multisort($name,SORT_ASC, $_data); 
                foreach($_data as $k => $v){
                    $_data_temp = $v;
                    $_data_temp['action'] = "edit_news";
                    $_data_temp['time_add'] = time();
                    $_data_temp['parent_id'] = 0;
                    if((int)I('post._id')){
                        $D_MaterialNews->where(
                                array('parent_id' => I('post._id'))
                            )->delete();
                    }
                    if($k == 0){
                        if((int)I('post._id')){
                            $_where_temp = array(
                                "id" => (int)I('post._id')
                            );
                        }
                        $_pid = $D_MaterialNews->write($_data_temp,$_where_temp);
                        $_pid = (int)I('post._id')?(int)I('post._id'):$_pid;
                    }else{
                        if($_pid){
                            $_data_temp['parent_id'] = $_pid;
                        }
                        $D_MaterialNews->write($_data_temp);
                    }
                }
                pushJson('ok');
            }
            if(I('get.id')){
                $this->_arr['resId'] = (int)I('get.id');
                $_where_e = array(
                    "id" => (int)I('get.id')
                );
                $_res = $D_MaterialNews->where($_where_e)->find();
                if($_res){
                    $_arr = array();
                    array_push($_arr,$_res);
                    $_where_c = array(
                            "parent_id" => $_res['id']
                        );
                    $_rst_c = $D_MaterialNews->where($_where_c)->select();
                    if(count($_rst_c)){
                        foreach($_rst_c as $k=>$v){
                            array_push($_arr,$v);
                        }
                    }
                    $this->_arr['resList'] = $_resList = $_arr;
                }

            }
            $temp = 'news_edit';
        }else{
            if(I('post.do_action') == 'delete_news'){
                $_id = (int)I('post.id');
                $_msg = '删除成功';
                if($_id){
                   $_rst =  $D_MaterialNews->where('id='.$_id)->delete();
                   $_rst =  $D_MaterialNews->where('parent_id='.$_id)->delete();
                   if(!$_rst){
                       $_msg = '删除失败';
                   }else{
                       pushJson($_msg);
                   }
                }else{
                    $_msg = '无效参数';
                }
                pushError($_msg);
            }
            $_where_li = array(
                "parent_id" => 0
            );
            $_key_word = I('get.keyword');
            if($_key_word){
                $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
                $_map["description"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
                $_map['_logic'] = 'or';
                $_where_li['_complex'] = $_map;
            }

            
            $_rst = $D_MaterialNews->getPagesize($_where_li);
            foreach($_rst['lists'] as $k=>$v){
                $_where_c = array(
                    "parent_id" => $v['id']
                    );
                $_rst_c = $D_MaterialNews->where($_where_c)->select();
                $_rst['lists'][$k]['child'] = $_rst_c;
            }
            $this->_arr['resList'] = $_rst;
        }
        
        $this->_showDisplay($temp);
    }
    /**
    *   菜单
    */
    public function menu(){
        $do_action = I('post.action');
        $D_WxMenu = D('wx\WxMenu');
        $this->rules = $D_WxMenu->vRule();
        if($do_action == 'edit_wx_menu') {
            
            $_data = I('post.','','trim');
            unset($_data['action']);
            $rules = $this->rules;
            if ($D_WxMenu->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $_data['action'] = "edit";
                $D_WxMenu->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_WxMenu->getError());
            }
            
        }else if($do_action == 'delete'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_WxMenu->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }else if($do_action == 'order'){
            $sort_order = I('post.sort_order');
            if($sort_order) {
                $sort_order_arr = $sort_order;
                foreach($sort_order_arr as $k => $v) {
                    $_where = $_data = array();
                    $_data['order_id'] = $k + 1;
                    $_where['id'] = $v;
                    $_data['action'] = "edit";
                    $D_WxMenu->write($_data, $_where);
                }
                pushJson('更新成功');
            }
        }
        
        //取出所有分类
        $_where = $_order = array();
        $_where['parent_id'] = 0;
        $_order = array('order_id' => 'ASC', 'id' => 'DESC');
        $_resColumn = $D_WxMenu->where($_where)->order($_order)->select();
        foreach($_resColumn as $k => $v){
            $_where_p = array(
                    "parent_id" => $v['id']
                );
            $_resColumn[$k]['_child'] = $D_WxMenu->where($_where_p)->order($_order)->select();
        }
        $this->_arr['resColumn'] = list_to_tree($_resColumn);
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_WxMenu->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        $D_MaterialLink = D('wx\MaterialLink');
        $D_MaterialText = D('wx\MaterialText');
        $D_MaterialNews = D('wx\MaterialNews');
        $_where_news = array(
                'parent_id' => 0
            );
        $_resNews = $D_MaterialNews->where($_where_news)->field('id,title')->select();
        $_resLink = $D_MaterialLink->field('id,title')->select();
        $_resText = $D_MaterialText->field('id,title')->select();
        $this->_arr['resNews'] = $_resNews;
        $this->_arr['resLink'] = $_resLink;
        $this->_arr['resText'] = $_resText;

        $this->_showDisplay();
    }

    public function link(){
        $do_action = I('post.action');
        $D_MaterialLink = D('wx\MaterialLink');
        $this->rules = $D_MaterialLink->vRule();
        if($do_action == 'edit') {
            
            $_data = I('post.','','trim');
            $rules = $this->rules;
            if ($D_MaterialLink->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_MaterialLink->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError($D_MaterialLink->getError());
            }
            
        }else if($do_action == 'delete'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_MaterialLink->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }else if($do_action == 'order'){
            $sort_order = I('post.sort_order');
            if($sort_order) {
                $sort_order_arr = $sort_order;
                foreach($sort_order_arr as $k => $v) {
                    $_where = $_data = array();
                    $_data['order_id'] = $k + 1;
                    $_where['id'] = $v;
                    $_data['action'] = "edit";
                    $D_MaterialLink->write($_data, $_where);
                }
                pushJson('更新成功');
            }
        }
        
        //取出所有分类
        $_where = $_order = array();
        $_order = array('order_id' => 'ASC', 'id' => 'DESC');
        $_resColumn = $D_MaterialLink->where($_where)->order($_order)->select();
        $this->_arr['resColumn'] = list_to_tree($_resColumn);
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_MaterialLink->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        $this->_showDisplay();
    }

    public function text(){
        $do_action = I('post.action');
        $D_MaterialText = D('wx\MaterialText');
        $this->rules = $D_MaterialText->vRule();
        if($do_action == 'edit') {
            
            $_data = I('post.','','trim');
            $rules = $this->rules;
            if ($D_MaterialText->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $_data['action'] = "edit";
                $D_MaterialText->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError($D_MaterialText->getError());
            }
            
        }else if($do_action == 'delete'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_MaterialText->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }else if($do_action == 'order'){
            $sort_order = I('post.sort_order');
            if($sort_order) {
                $sort_order_arr = $sort_order;
                foreach($sort_order_arr as $k => $v) {
                    $_where = $_data = array();
                    $_data['order_id'] = $k + 1;
                    $_where['id'] = $v;
                    $_data['action'] = "edit";
                    $D_MaterialText->write($_data, $_where);
                }
                pushJson('更新成功');
            }
        }
        
        //取出所有分类
        $_where = $_order = array();
        $_order = array('order_id' => 'ASC', 'id' => 'DESC');
        $_resColumn = $D_MaterialText->where($_where)->order($_order)->select();
        $this->_arr['resColumn'] = list_to_tree($_resColumn);
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_MaterialText->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        $this->_showDisplay();
    }
    
}
