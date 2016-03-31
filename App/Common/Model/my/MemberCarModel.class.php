<?php

namespace Common\Model\my;

/**
 * useage
 * D('my\Member')->;
 * front_uid
 */
class MemberCarModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_member_car';
    
    public function write($_data,$_where = array()){
        
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "delete_car"){
                $rst = $this->where($_where)->data($_data)->save();
                if(!$rst){
                    $_msg = "删除失败";
                }
            }else if($_data['action'] == "post_car"){
                
                if($this->validate($rules)->create($_data)){
                    
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }
            
        }else{
            $_data['time_add'] = time();
            if($this->validate($rules)->create($_data)){
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
            array('plate_num','require','请填写车牌号码！'),
            array('text_series','require','请选择您的车型！')
        );
    }

    
}