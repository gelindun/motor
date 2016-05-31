<?php

namespace Home\Controller;
use Think\Controller;

class CouponController extends HomeController {
    

    public function _initialize() {
        parent::_initialize();
        
        
    }
    /**
     * 优惠券发放
     */
    public function index(){
        $this->chkLogin(0, U('/Coupon/index'));
        $_dataCoupon = safeGetCookie('dataCoupon');
        $_dataCoupon = json_decode($_dataCoupon,true);
        $_resList = array();
        if(count($_dataCoupon)){
            $_key = mt_rand(0,count($_dataCoupon)-1);
            $_data = $_dataCoupon[$_key];
            $_addCoupon = true;
            $D_CouponUser = D('coupon\CouponUser');
            if($_data['limit'] > 0){
                
                $_where = array(
                        "front_uid" => $this->_arr[self::FRONT_UID],
                        "cid" => $_data['id']
                    );
                $_count = $D_CouponUser->where($_where)->count('*');
                if($_count > $_data['limit']){
                    $_addCoupon = false;
                }
            }
            if($_addCoupon == true){
                $_data_u = array(
                        "cid" => $_data['id'],
                        "front_uid" => $this->_arr[self::FRONT_UID],
                        "time_begin" => $_data['time_valid_start'],
                        "time_end" => $_data['time_valid_end'],
                        "time_add" => time(),
                        "title" => $_data['title'],
                        "type" => $_data['type'],
                        "reduce" => $_data['reduce'],
                        "c_status" => 0,
                        "action" => "ShareSuccess",
                        "least_cost" => $_data['least_cost']
                    );
                $D_CouponUser->addData($_data_u);
            }
            safeGetCookie('dataCoupon',null);
            redirect('/My/coupon');
        }
        
        $this->_showDisplay();
    }


}
