<?php

namespace Common\Model\site;

/**
 * useage
 * D('site\Merchant')->;
 * 地区
 */
class MerchantModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_merchant';

    public function write($_data,$_where = array()){
       
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $_data['time_add'] = time();
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