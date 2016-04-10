<?php

namespace Common\Model\site;

/**
 * useage
 * D('my\Member')->;
 * front_uid
 */
class PicPosModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_pic_pos';
    
    public function write($_data,$_where = array()){
        
        
        if($_data['action'] == "add_user"){
            $_data['status'] = 1;
        }else{
            //$_data['status'] = (int)$_data['status']?1:0;
        }
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "delete_pic"){
                $rst = $this->where($_where)->data($_data)->save();
                if(!$rst){
                    $_msg = "删除失败";
                }
            }else{//注册
                if($this->validate($rules)->create($_data)){
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }
            
        }else{
            if($this->validate($rules)->create($_data)){
                $rst = $this->data($_data)->add();
            }else{
                $_msg = $this->getError();
            }
        }
       
        return $_msg?array("msg"=>$_msg):$rst;
    }
    //
    public function vRule(){
        return array(
            array('title','require','请填写标题！')
        );
    }
    
}