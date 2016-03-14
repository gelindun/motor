<?php

namespace Common\Model\order;


class OrderModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_order';
    
    public function write($_data = array(),$_where = array()){
        $_data['group_id'] = (int)$_data['group_id'];
        if(!$this->create($_data)){
            return array(
                'msg' => $this->getError(),
                'status' => 0
            );
        }
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $_data['time_add'] = time();
            $rst = $this->data($_data)->add();
        }
        
        return $rst?$rst:$_rst;
        
    }
    
    /**
     * 付款类型
     * @param bool $_key
     * @return string
     */
    public function payType($_key = false){
        $_arr = array(
            0   =>    array(
                "title" => "线下支付",
                "ext"   =>  "offline"
            ),
            1   =>  array(
                "title" =>  "微信支付",
                "ext"   =>  "wechat"
            )
        );
        if($_key){
            return $_arr[0];
        }
        return $_arr;
    }
    
    
    /**
     * 订单状态
     */
    public function orderStatus($_key = ""){
        $_arr = array(
            0   =>  "未支付",
            1   =>  "待确认",
            2   =>  "已支付",
            3   =>  "已发货",
            4   =>  "已完成"
        );
        
        return ((!$_key)?$_arr:$_arr[$_key]);
    }
    /**
     * 订单号
     * @return type
     */
    public function rtnOrderid(){
        return NOW_TIME.rand(1001,9999);
    }
    
    public function getPayurl($oid,$pay_type){
        if(!$oid || !$pay_type){
            return false;
        }
        if($pay_type == '微信支付') {
            $_return_url = C('MAIN_DOMAIN').U('/Paywx/index',array(
                "oid" => $oid
            ));
        } else if($pay_type == '线下支付') {
            $_member_url = '/My/order';
            $_return_url = U($_member_url);
        }
        return $_return_url;
    }
    /**
     * 订单更新状态,向上更新!
     * @param type $_data_order
     * @param type $_where
     */
    public function orderStatusinc($_data_order,$_where){
        $_order_status = $_data_order['order_status']?$_data_order['order_status']:1;
        $_rst = $this->where($_where)->find();
        if(!$_rst){
            return false;
        }
        $this->write($_data_order,$_where);
        $_where = array(
            "group_id" => $_rst['id'],
            "order_status" => array(
                'lt',$_order_status
            )
        );
        $this->write($_data_order,$_where);
        return true;
    }
    /**
     * 获取组订单价格
     */
    public function rtnOrderprice($_oid){
        $_where = array(
            "group_id" => $_oid
        );
        $_rst = $this->where($_where)->select();
        if(count($_rst)){
            return false;
        }
        $_price = 0;
        foreach($_rst as $k=>$v){
            if($v['order_status'] < 2){
                $_price += $v['price'];
            }
        }
        return $_price;
    }
    /**
     * 订单详细
     * @param type $_order
     */
    public function rtnOrderdetail($_order){
        if(!is_array($_order) || !$_order['order_type'] || !$_order['id']){
            return false;
        }
        
        switch ($_order['order_type']){
            case C('CLEAN_FORM'):
                $_rstList = array();
            break;
            default:
                $_rstList = array();
            break;
        }
        return $_rstList;
    }
    
}