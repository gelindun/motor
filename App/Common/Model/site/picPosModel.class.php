<?php

namespace Common\Model\site;

/**
 * useage
 * D('my\Member')->;
 * front_uid
 */
class PicPosModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_pic_pos';

    public function rtnKeyArr(){
        return array(
                "index" => array(
                        "key" => "index",
                        "title" => "首页幻灯片"
                    ),
                "myLogo" => array(
                        "key" => "myLogo",
                        "title" => "个人中心头图"
                    )
            );
    }
    
    public function write($_data,$_where = array()){
        
      
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "delete_pic"){
                $rst = $this->where($_where)->data($_data)->save();
                if(!$rst){
                    $_msg = "删除失败";
                }
            }else{
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