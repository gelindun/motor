<?php
// +----------------------------------------------------------------------
// 发放优惠券
namespace Behavior;
/**
 * 检查是否有可发放的优惠券， 若有
 * 输出优惠券提示语及优惠券发放URL
 */
class ShareSuccessBehavior {

    public function run(&$params) {
        $D_Coupon = D('coupon\Coupon');
        $_time = time();
        $_where_limit = array(
            'amount' => 0,
            'sended' => array(
                    "elt","`amount`"
                ),
            '_logic' => 'or'
         );
        $_where = array(
                "delete" => 0,
                "status" => 1,
                "time_start" => array(
                        "elt",$_time
                    ),
                "time_end" => array(
                        "egt",$_time
                    ),
                '_complex' => $_where_limit

            );
        $_rst = $D_Coupon->where($_where)->select();
        
        $_arr = array(
                "msg" => "一大波优惠券袭来，请注意查收",
                "url" => "/Coupon/index",
                "data" => $_rst
            );
        return $_arr;
    }
}