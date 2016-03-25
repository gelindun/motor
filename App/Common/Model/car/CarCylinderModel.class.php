<?php

namespace Common\Model\car;

/**
 * useage
 * D('car\CarCylinder')->;
 * 车型
 */
class CarCylinderModel {
    
    public function cylinderList(){
    	$_arr = array(
    			"4" => array(
    					"title" => "四缸",
    					"key" => 4
    				),
                "5" => array(
                        "title" => "五缸",
                        "key" => 5
                    ),
    			"6" => array(
    					"title" => "六缸",
    					"key" => 6
    				),
    			"8" => array(
    					"title" => "八缸",
    					"key" => 8
    				),
    			"12" => array(
    					"title" => "十二",
    					"key" => 12
    				)
    		);
    	return $_arr;

    }
    
    
}