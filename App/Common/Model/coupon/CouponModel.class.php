<?php

namespace Common\Model\coupon;

/**
 * useage
 * D('coupon\Coupon')->write_logs();
 * 优惠券
 */
class CouponModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_coupon';
    
    public function write_logs($_data){
        
    }

    public function coupon_type(){
    	return array(
    			"discount" => array(
    					"title" => "折扣券"
    				),
    			"reduce" => array(
    					"title" => "现金券"
    				)
    		);
    }
    
    public function add_coupon($_data){
    	return $this->addData($data);
    }

    
}