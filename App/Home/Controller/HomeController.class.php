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
        $_data_order['price'] = $_amountCart;
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