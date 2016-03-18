<?php

namespace Common\Model\car;

/**
 * useage
 * D('car\CarCylinder')->;
 * 车型
 */
class CarCylinderModel extends \Common\Model\BaseModel{
    
    public function cylinderList(){
    	$_arr = array(
    			"4" => array(
    					"title" => "四缸发动机",
    					"key" => 4
    				),
    			"6" => array(
    					"title" => "六缸发动机",
    					"key" => 6
    				),
    			"8" => array(
    					"title" => "八缸发动机",
    					"key" => 8
    				),
    			"12" => array(
    					"title" => "十二缸发动机",
    					"key" => 12
    				)
    		);
    	return $_arr;

    }
    
    
}