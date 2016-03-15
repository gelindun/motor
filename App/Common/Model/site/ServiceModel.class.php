<?php

namespace Common\Model\site;

/**
 * 系统售后备注
 */
class ServiceModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_service';
    
    public function write($_data,$_where = array()){
       
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }
    
    public function vRule(){
        return array(
            array('title','require','请输入标题！')
        );
    }
    
}