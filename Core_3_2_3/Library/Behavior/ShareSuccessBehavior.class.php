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
        $_arr = array(
                "msg" => "一大波优惠券袭来，请注意查收",
                "url" => "/Index"
            );
        return $_arr;
    }
}