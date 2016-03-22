<?php

namespace Common\Model\product;

/**
 * 产品
 */
class ProductModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_product';
    /**
    * 发动机清洗
    **/
    public function clean_form(){
        return array(
            'key' => C("CLEAN_FORM"),
            'type' => C("CLEAN_FORM"),
            'pid' => 0,
            'title' => '发动机清洗'
         );
    }
    
    
}