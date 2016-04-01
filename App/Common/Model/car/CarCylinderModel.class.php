<?php

namespace Common\Model\car;

/**
 * useage
 * D('car\CarCylinder')->;
 * 车型
 */
class CarCylinderModel {
    
    public function cylinderList($key = ""){
    	$_arr = array(
    			"4" => array(
    					"title" => "四缸",
    					"key" => 4,
                        "price" => 100
    				),
    			"6" => array(
    					"title" => "六缸",
    					"key" => 6,
                        "price" => 120
    				),
    			"8" => array(
    					"title" => "八缸",
    					"key" => 8,
                        "price" => 140
    				),
    			"12" => array(
    					"title" => "十二",
    					"key" => 12,
                        "price" => 160
    				)
    		);
        if($_arr[$key]){
            return $_arr[$key];
        }
    	return $_arr;

    }
    
    
}