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
    public function match_coupon($_amountCart,$front_uid){
    	$_time = time();
        $_where_c = array(
            "front_uid" => $front_uid,
            "c_status" => array('lt',1),
            "time_begin" => array(
                    "elt",$_time
                ),
            "time_end" => array(
                    "egt",$_time
                ),
            'reduce' => array(
                    "elt",$_amountCart
                ),
            'least_cost' => array(
                    "elt",$_amountCart
                )

        );
        $_order_c = array(
                "reduce" => "DESC",
                "id" => "ASC"
            );
        $_rst_c = $this->where($_where_c)->order($_order_c)->find();

        $_reduce = $_rst_c['reduce'];
        $_where_used = array(
                "id" => $_rst_c['id']
            );
        $_data_used = array(
                "c_status" => 1,
                "time_use" => time()
            );
        $this->saveData($_data_used,$_where_used);

        return array(
        		"coupon_id" => $_rst_c['id'],
        		"reduce" => $_reduce
        	);
    }
}