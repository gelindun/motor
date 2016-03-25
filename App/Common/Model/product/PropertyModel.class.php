<?php

namespace Common\Model\product;

/**
 * 产品属性
 */
class PropertyModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_property';
    
    /*
    *   发动机清洗【产品】属性
    */
    public function clean_form($key = ""){
    	$_arr = array(
    			"store" => array(
                        "key" => "st",
                        "title" => '门店'
                    ),
                "cylinder" => array(
                        "key" => "cl",
                        "title" => '发动机缸数'
                    )
    		);
        if($key && $_arr[$key]){
            return $_arr[$key];
        }
    	return $_arr;

    }
    
    
}