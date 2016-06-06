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
                    
                    if($k == 0){

                        if((int)I('post._id')){
                            $D_MaterialNews->where(
                                array('parent_id' => I('post._id'))
                            )->delete();
                            $_data_temp['action'] = 'edit_news';
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
                        $_rst_temp = $D_MaterialNews->write($_data_temp);
                    }
                    usleep(100000);
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
        }else if($do_action == 'sub_menu'){
            //发布菜单
            $_where = $_order = array();
            $_where['parent_id'] = 0;
            $_order = array('order_id' => 'ASC', 'id' => 'DESC');
            $_resColumn = $D_WxMenu->where($_where)->order($_order)->select();
            $_menu['button'] = array();
            $D_MaterialLink = D('wx\MaterialLink');
            foreach($_resColumn as $k => $v){
                if ($v['type'] != 2) {
                    $_arr = array(
                        'type' => 'click', 'name' => $v['title'], 'key' => $v['id']
                    );
                } else {

                    $_link = $D_MaterialLink->where(
                            array(
                                    'id' => $v['rid']
                                )
                        )->getField('url');
                    $preg = '|^http|';
                    if(!preg_match($preg,$url)) {
                         $_link = uDomain('www').$_link;
                    }

                    $_arr = array(
                        'type' => 'view', 'name' => $v['title'], 'url' => '' . $_link
                    );
                }

                $_where_p = array(
                        "parent_id" => $v['id']
                    );
                $_resChild = $D_WxMenu->where($_where_p)->order($_order)->select();
                if(count($_resChild) > 0){
                    $_arr['sub_button'] = array();
                    unset($_arr['type']);
                    if($_arr['key'])
                        unset($_arr['key']);
                    if($_arr['url'])
                        unset($_arr['url']);
                    foreach($_resChild as $kk => $vv){
                        if ($vv['type'] != 2) {
                            $_arr_c = array(
                                'type' => 'click', 'name' => $vv['title'], 'key' => $vv['id']
                            );
                        } else {
                           
                            $_link = $D_MaterialLink->where(
                                    array(
                                            'id' => $vv['rid']
                                        )
                                )->getField('url');
                            $preg = '|^http|';
                            if(!preg_match($preg,$url)) {
                                 $_link = uDomain('www').$_link;
                            }
                            $_arr_c = array(
                                'type' => 'view', 'name' => $vv['title'], 'url' => '' . $_link
                            );
                        }

                        array_push($_arr['sub_button'],$_arr_c);
                    }
                }
                array_push($_menu['button'],$_arr);

            }

            $result = $this->wxMenu($_menu);
            $_return = array();
            if ($result == true) {
                $_return['status'] = 1;
                $_return['msg'] = '菜单发布成功';
            } else if(empty($this->_arr['WX_BASE']['wx_appid']) 
                || empty($this->_arr['WX_BASE']['wx_appsecret'])) {
                $_return['status'] = 0;
                $_return['msg'] = "菜单发布失败,微信授权配置的AppId和AppSecret不能为空！";
            } else {
                $_return['status'] = 0;
                $_return['msg'] = "菜单发布失败,请检查菜单中为'链接网址'的是否正确(不允许带空格，必须是http开头，并且链接地址不能为空！)";
            }
            if($_return['status'] >0){
                pushJson($_return['msg']);
            }else{
                pushError($_return['msg']);
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

    /**
    *   添加删除菜单
    */

    protected function wxMenu($_menu = '') {

        //验证token
        vendor('Weixin.wechat', '', '.class.php');
        $options = array(
            'token' => $this->_arr['WX_BASE']['wx_token'],
            'appid' => $this->_arr['WX_BASE']['wx_appid'],
            'appsecret' => $this->_arr['WX_BASE']['wx_appsecret'],
        );

        $wechatObj = new \Wechat($options);
        //验证token end
        if ($_menu) {
            $result = $wechatObj->createMenu($_menu);
        } else {
            $result = $wechatObj->deleteMenu();
        }
        return $result;
    }
    
}
