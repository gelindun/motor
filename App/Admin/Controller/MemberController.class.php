<?php

namespace Admin\Controller;
use Think\Controller;

class MemberController extends AdminController {

    public function _initialize() {
         parent::_initialize();
         
    }
    
    public function index(){
       $D_User = D('my\Member');
       $_key_word = I('get.keyword');
       if(I('post.action') === 'delete_user'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_data = array(
                   "action" => 'delete_update',
                   "delete" => 1
               );
               $_rst =  $D_User->write($_data,array('id'=>$_id));
               if(is_array($_rst)){
                    $_msg = $_rst['msg'];
                }
               
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
        if($_key_word){
            $_map["uname"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["real_name"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_where['delete'] = 0;
        $_where['parent_id'] = 0;
        $_order = array("time_add"=>'DESC',"id"=>'DESC');
        $resList = $D_User->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['remark'] = msubstr(strip_tags(htmlspecialchars_decode($v['remark'])),0,60);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
        //exit;
    }
    
    public function member_edit(){
        $D_User = D('my\Member');
        if(I('post.action') === 'edit_member'){
            $_data = I('post.','','trim');
            $_data['time_show'] = strtotime($_data['time_show']);
           
            unset($_data['action']);
            $_data['action'] = "edit_profile";
            
            if($_data['id']){
                $_where = array(
                    'id' => $_data['id']
                );
            }
            $_rst =  $D_User->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        

        
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_User->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        
        $this->_showDisplay();
    }
    
    
}
