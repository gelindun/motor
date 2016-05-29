<?php

namespace Common\Model\coupon;

/**
 * useage
 * D('coupon\CouponUser')->write_logs();
 * 优惠券
 */
class CouponUserModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_coupon_user';
    
    public function write_logs($_data){
        
    }
    /**
    * 下单时匹配优惠券
    */
    public function match_coupon(){

    }
}