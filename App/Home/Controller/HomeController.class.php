<?php

namespace Home\Controller;
use Think\Controller;

class HomeController extends \Common\Controller\CommonController {

    public function _initialize() {
        parent::_initialize();
        
        if((int)$this->_arr[self::FRONT_UID]){
            $D_User = D('my\Member');
            $_rst = $D_User->loginInfo($this->_arr[self::FRONT_UID]);
            $this->_arr['my_info'] = $_rst;
        }
        $_data['url'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        D('log\LogBrowser')->write_logs($_data);
        //$this->_arr['WX_BASE']
        $this->attentionCheck();
    }
    // 关注校验
    protected function attentionCheck(){
        if(isWeixinBrowser() && $this->_arr[self::FRONT_UID]){
            $front_uid = $this->_arr[self::FRONT_UID];
            $_flag_subscribe = true;
            $D_Member = D('my\Member');
            $D_MemberBind = D('my\MemberBind');
            $D_LogAttention = D('log\LogAttention');
            $_where = array(
                "type" => "weixin",
                "front_uid" => $front_uid
            );
            $wx_openid = $D_MemberBind->where($_where)->getField('type_uid');
            $_where_c = array(
                "wx_open_id" => $wx_openid
            );
            $_rst = $D_LogAttention->where($_where_c)->order(
                    array(
                        "id" => "DESC"
                    )
                )->find();
            
            if($_rst['a_status'] > 1){
                $_flag_subscribe = false;
            }else if(!$_rst['id']){
                $_resArr = $D_Member->chkSubscribe($this->_arr['WX_BASE'],$wx_openid);
                $_flag = $_resArr['subscribe'];
                if(!$_flag){
                    $_flag_subscribe = false;
                }else{
                    $_data_log = array(
                        "wx_open_id" => $wx_openid,
                        "a_status" => 0,
                        "time_add" => $_resArr['subscribe_time'],
                        "sid" => 0,
                        "ip" => get_client_ip()
                    );
                    $D_LogAttention->addData($_data_log);
                }
            }
            if(!$_flag_subscribe){
                $this->_arr['showBtnSubscribe'] = true;
            }
        }//broswer check end
    }

    /**
    * 生成订单
    $_dataPro = array(
    		"order_type" => $this->_arr['CLEAN_PRO']['type']
            "list" => array(
	            	array(
	                    "pid" => 1,
	                    "price" => 0.01
	                )
            	)
        );
    */
    protected function createOrder($_dataPro = array()){
    	$_isPost = I('post.');
        // test end
        $_countProduct = count($_dataPro["list"]);
        foreach($_dataPro["list"] as $k => $v){
            $_amountCart += floatval($v['price']);
        }
    	$_orderProductInfo = $_dataPro["list"];
    	$_orderType = $_dataPro['order_type'];//$this->_arr['CLEAN_PRO']['type']
    	$D_Order = D('order\Order');

    	$_orderStatus = $_amountCart?0:2;
        $_data_temp = array(
            "order_remark" => I('post.order_remark')?I('post.order_remark'):'',
            "front_uid" => $this->_arr[self::FRONT_UID],
            "product_info" => json_encode($_orderProductInfo)
        );
        
        $_tempOid = $_redirectOid = $D_Order->rtnOrderid();
        $_data_order = array(
            "order_id" => $_tempOid,
            "order_status" => $_orderStatus
        );
        //查询优惠券，CouponUser, 优惠券金额小于待付金额，且满额小于待付金额 的最大值优惠券
        $_rst_coupon = D('coupon\CouponUser')->match_coupon($_amountCart,$this->_arr[self::FRONT_UID]);
        $_reduce = $_rst_coupon['reduce'];
        ////////////////////////////////

        $_data_order['price'] = $_amountCart - $_reduce;
        $_data_order['reduce'] = floatval($_reduce);
        $_data_order['order_type'] = $_orderType;
        $_data_order = array_merge($_data_temp,$_data_order);
        //dump($_data_order);
        $_oid = $D_Order->write($_data_order);

        if(!$_amountCart){//免费订单
            $_url = U('/My/order',array('order_type'=>$_orderType,'order_status'=>2));
        }else{
            $_url = U('/Order/payment',array('order_id'=>$_redirectOid));
        }
        if($_isPost){
            pushJson('跳转中,请稍候', array(
                "url" => $_url
            ));
        }else{
            redirect($_url);
        }

    }
    
}