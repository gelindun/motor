<?php

namespace Common\Model\product;

/**
 * 系统单页面
 */
class PropertyPriceModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_property_price';

    
    
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
            array('pro_str','require','请选择产品类型！')
        );
    }
    
}