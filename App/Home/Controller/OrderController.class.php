<?php

namespace Home\Controller;
use Think\Controller;

class OrderController extends HomeController {
    

    public function _initialize() {
        parent::_initialize();
        
        
    }
    /**
     * 购物车 ,预留
     */
    public function confirm(){
        $this->chkLogin(0, U('/Order/confirm'));
        $_orderType = I('get.type')?I('get.type'):$this->_arr['CLEAN_PRO']['type'];
        
        $this->_showDisplay();
    }
    /**
     * 核对订单信息,（选择物流,支付方式 预留）
     */
    public function checkout(){
        $this->chkLogin(0, U('/Order/checkout'));
        $D_Order = D('order\Order');
        $_countProduct = 0;
        $_error = "";
        //虚拟产品忽略物流信息
        $_ticketOnly = true; 
        //免费产品忽略订单支付
        $_amountCart = 0;

        $_dataPro = safeGetCookie(COOKIE_PRODUCT);
        $_dataPro = json_decode($_dataPro,true);
        // test start
        $_dataPro = array(
            array(
                    "pid" => 1,
                    "type" => $this->_arr['CLEAN_PRO']['type'],
                    "price" => 0.01
                )
        );
        // test end
        $_countProduct = count($_dataPro);
        foreach($_dataPro as $k => $v){
            $_amountCart += floatval($v['price']);
        }
        $_orderProductInfo = json_encode($_dataPro);
        
        if($_countProduct < 1){
            $_error = "您尚未选择任何商品,请返回确认";
        }
        $_do_action = I('post.do_action');
        //确认订单
        if($_do_action == 'confirm_order' || $_ticketOnly){
            $_isPost = I('post.')?true:false;
            if($_error){
                if($_isPost)pushError($_error);
            }else{
                if($_isPost){
                    //order_info(物流信息,收货地址)
                    //order_remark(订单备注)
                }
                //生成订单,拆分订单,免费订单订单状态为已付款
                $_orderStatus = $_amountCart?0:2;
                $_data_temp = array(
                    "order_remark" => I('post.order_remark')?I('post.order_remark'):'',
                    "front_uid" => $this->_arr[self::FRONT_UID],
                    "product_info" => $_orderProductInfo
                );
                
                $_tempOid = $D_Order->rtnOrderid();
                $_redirectOid = $_redirectOid?$_redirectOid:$_tempOid;
                $_data_order = array(
                    "order_id" => $_tempOid,
                    "order_status" => $_orderStatus
                );
                $_data_order['price'] = $_amountCart;
                $_data_order['order_type'] = $v['type'];
                $_data_order = array_merge($_data_temp,$_data_order);
                //dump($_data_order);
                $_oid = $D_Order->write($_data_order);
                //更新订单 未完待续..

                //更新订单end
                if(!$_amountCart){//免费订单
                    $_url = U('/My/order',array('order_type'=>$v['type'],'order_status'=>2));
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
        
        $this->_arr['amountCart'] = $_amountCart;
        $this->_arr['error'] = $_error;
        $this->_showDisplay();
    }
    /**
     * 付款
     */
    public function payment($_isWeixin = false){
        $D_Order = D('order\Order');
        $_order_id = I('get.order_id');
        $_where = array(
            "order_id" => $_order_id
        );
        $_order = $D_Order->where($_where)->find();
        if(!$_order_id || !$_order){
            $this->_arr['error'] = "当前查询的订单不存在";
        }else if($_order['order_status']>1){
            $this->_arr['error'] = "当前查询的订单状态为".$D_Order->orderStatus($_order['order_status']);
        }
        $this->_arr['orderType'] = $D_Order->payType();
        $this->_arr['orderRes'] = $_order;
        $_data = I('post.');
        if($_data['do_action'] === 'sub_pay'){
            if($this->_arr['error']){
                pushError($this->_arr['error']);
            }
            if($_data['pay_type'] === '线下支付'){
                $_data_order = array(
                    "pay_type" => $_data['pay_type'],
                    "pay_remark" => $_data['pay_remark']?$_data['pay_remark']:"",
                    "order_status" => 1
                );
                $_where = array(
                    "order_id" => $_order_id
                );
                //更新订单
                $D_Order->orderStatusinc($_data_order,$_where);
            }

            $_url = $D_Order->getPayurl($_order['id'],$_data['pay_type']);
            if(!$_isWeixin && $_data['pay_type'] === '微信支付'){
                $_url = http_get($_url.'?from=pc');
            }
            
            if(!$_url){
                pushError('订单信息出错');
            }
            pushJson('',array(
                "url" => $_url
            ));
        }
        
        $this->_showDisplay();
    }
    
    public function chkOrderstatus(){
        $D_Order = D('order\Order');
        $_order_id = I('post.order_id');
        $_where = array(
            "order_id" => $_order_id
        );
        $_order = $D_Order->where($_where)->find();
        pushJson('',array(
            "order_status" => $_order['order_status']
        ));
    }
}
