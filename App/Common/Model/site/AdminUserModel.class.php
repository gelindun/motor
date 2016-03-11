<?php

namespace Common\Model\site;

/**
 * useage
 * D('site\AdminUser')->;
 * 系统用户
 */
class AdminUserModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_admin_user';
    public $ROLE = array(
        '1' => '管理员',
        '2' => '编辑'
    );
    
    
    
    public function write($_data,$_where = array()){
        $rules = $this->vRule();
        if($_where){
          
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
            
        }else{
            $rules = array_merge($rules,array('upwd','require','请填写密码'));
 
            if($this->validate($rules)->create($_data)){
                $_data['upwd'] = md5(md5($_data['upwd']));
                $rst = $this->data($_data)->add();
            }else{
                $_msg = $this->getError();
            }
        }
       
        return $_msg?array("msg"=>$_msg):$rst;
    }
    
    public function vRule(){
        return array(
            array('uname','require','请填写用户名！',1),
            array('uname','','用户名已经存在！',0,'unique',1),
            
        );
    }
    
}