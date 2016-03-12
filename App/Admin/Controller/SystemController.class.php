<?php

namespace Admin\Controller;
use Think\Controller;

class SystemController extends AdminController {

    public function _initialize() {
         parent::_initialize();
    }
    //login
    public function index(){
        if(safeGetCookie(self::ADMIN_UID)){
            $this->redirect('/');
        }
        if(I('post.action' == 'admin_login')){
            $D_AdminUser = D('site\AdminUser');
            $_data = I('post.');
            unset($_data['action']);
            if(!$_data['uname'] || !$_data['upwd']){
                pushError('请填写用户名,密码');
            }
            $_where = array(
                'uname' => $_data['uname'],
                'upwd'  => md5(md5($_data['upwd']))
            );
            $_rst = $D_AdminUser->where($_where)->find();
            if($_rst){
                if($_data['remember'])
                    safeGetCookie(self::ADMIN_UID,$_rst['id'],array('expire'=>3600*24*7));
                else
                    safeGetCookie(self::ADMIN_UID,$_rst['id']);
                pushJson('登录成功',array('redirect'=>'/'));
            }else{
                pushError('用户名或密码错误');
            }
        }
        $this->_showDisplay();
    }
    
    public function logout(){
        safeGetCookie(self::ADMIN_UID,null);
        $this->redirect('/System');
        exit;
    }
    
    public function register(){
        if($this->_arr['admin_info']['role'] != 1){
            exit;
        }
        $D_AdminUser = D('site\AdminUser');
        if(I('post.action') === 'delete_admin'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id && $_id!=(int)$this->_arr['admin_uid']){
               $_rst =  $D_AdminUser->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }else if(I('post.action') === 'add_admin'){
            $_data = I('post.','','trim');
            unset($_data['action']);
        
            if($_data['id']){
                $_where = array(
                    'id' => $_data['id']
                );
            }
            $_rst =  $D_AdminUser->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_AdminUser->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        $this->_arr['resPage']['roleArr'] = $D_AdminUser->ROLE;
        $_where = array(
            'id' => array('neq',(int)$this->_arr['admin_uid'])
        );
        $_resColumn = $D_AdminUser->where($_where)->select();
        
        $this->_arr['resColumn'] = $_resColumn;
        
        
        $this->_showDisplay();
    }
    
    public function profile(){
        $D_AdminUser = D('site\AdminUser');
        if(I('post.action') === 'edit_admin'){
            $_data = I('post.','','trim');
            unset($_data['action']);
        
            if($_data['id']){
                $_where = array(
                    'id' => $_data['id']
                );
            }
            $_rst =  $D_AdminUser->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        
        $_id = (int)$this->_arr['admin_uid'];
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_AdminUser->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        
        $this->_showDisplay();
    }
    
}
