<?php

namespace Common\Model\my;

/**
 * useage
 * D('my\Member')->;
 * front_uid
 */
class MemberModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_member';
    
    public function write($_data,$_where = array()){
        if($_data['head_img']){
            $_data['head_img'] = str_replace(C('DATA_UPLOADS'), '', $_data['head_img']);
            $_data['head_img'] = str_replace('//', '/', $_data['head_img']);
        }
        if($_data['date_birth']){
            $_data['date_birth'] = strtotime($_data['date_birth']);
        }
        
        if($_data['action'] == "add_user"){
            $_data['status'] = 1;
        }else{
            //$_data['status'] = (int)$_data['status']?1:0;
        }
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "delete_update"){
                $rst = $this->where($_where)->data($_data)->save();
                if(!$rst){
                    $_msg = "删除失败";
                }
            }else if($_data['action'] == "edit_passwd"){
                $rule_pass = $this->passRule(); 
                if($this->validate($rule_pass)->create($_data)){
                    $_where_pass = $_where;
                    $_data['upwd'] = md5(md5($_data['upwd']));
                    $_where_pass['upwd'] = $_data['upwd'];
                    $_r = $this->where($_where_pass)->find();
                    if(!$_r){
                        $_msg = '您输入的原始密码错误';
                    }else{
                        $_data['upwd'] = md5(md5($_data['newpasswd']));
                        $rst = $this->where($_where)->data($_data)->save();
                    }
                }else{
                    $_msg = $this->getError();
                }
                
            }else if($_data['action'] == "edit_profile"){
                if($this->validate($rules)->create($_data)){
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }else{//注册
                if($this->validate($rules)->create($_data)){
                    if($_data['upwd']){
                        $_data['upwd'] = md5(md5($_data['upwd']));
                    }else{
                        unset($_data['upwd']);
                    }
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }
            
        }else{
            $rules = array_merge($rules,array('upwd','require','请填写密码'));
            $_data['time_add'] = time();
            if($this->validate($rules)->create($_data)){
                $_data['upwd'] = md5(md5($_data['upwd']));
                $rst = $this->data($_data)->add();
            }else{
                $_msg = $this->getError();
            }
        }
       
        return $_msg?array("msg"=>$_msg):$rst;
    }
    //1新增数据时候验证,2编辑数据时候验证,3全部情况下验证
    public function vRule(){
        return array(
            array('uname','require','请填写用户名！'),
            array('uname','','用户名已经存在！',0,'unique',3),
            array('uname','/^[a-zA-Z][a-zA-Z0-9_]{2,19}$/','用户名不合法！'),
            array('email','','Email已被绑定！',0,'unique',3),
            array('mobile','','手机号码已被绑定！',0,'unique',2)
        );
    }
    
    public function passRule(){
        return array(
          array('upwd','require','请填写原始密码！'),
          array('newpasswd','require','请填写新密码！'),
          array('repassword','newpasswd','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
          array('newpasswd','/^[a-zA-Z\d_]{5,20}$/','密码格式不正确',0,'regex'), // 自定义函数验证密码格式  
        );
    }
    
    public function gender(){
        return array(
            0 => "保密",
            1 => "男",
            2 => "女"
        );
    }
    
    public function loginInfo($front_uid){
        $_where = array(
            'id' => $front_uid
        );
        $_rst = $this->where($_where)->field('uname,real_name,head_img')->find();
        if($_rst){
            $_rst['real_name'] = $_rst['real_name']?$_rst['real_name']:$_rst['uname'];
        }
        return $_rst;
    }
    
}