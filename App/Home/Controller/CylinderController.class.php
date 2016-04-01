<?php

namespace Home\Controller;
use Think\Controller;
//发动机清洗 支付表单
class CylinderController extends HomeController {
   

    public function _initialize() {
         parent::_initialize();
         
    }
    /**
    array(8) {
      ["nickName"] => string(6) "ayoway"
      ["mobile"] => string(11) "13800138000"
      ["plate_num"] => string(10) "粤B321717"
      ["car_brand"] => string(2) "35"
      ["car_series"] => string(1) "6"
      ["store_id"] => string(1) "3"
      ["cylinder_id"] => string(2) "12"
      ["amount"] => string(3) "140"
    }
    */
    public function index(){
        $this->chkLogin(0, U('/Cylinder/index'));
        if(I('post.action') === 'post_order'){
            $_dataPro = array(
                "order_type" => $this->_arr['CLEAN_PRO']['type'],
                "list" => array()
            );
            $_obj = array(
                    "store_id" => I('post.store_id'),
                    "cylinder_id" => I('post.cylinder_id')
                );
            $_price = $this->orderAmount($_obj);
            $_dataDetail = I('post.');
            unset($_dataDetail['action']);
            $_product = array(
                    "pid" => 0,
                    "price" => $_price,
                    "product_detail" => $_dataDetail
                );
            $_dataPro["list"][] = $_product;
            $this->createOrder($_dataPro);
        }
        $D_Property = D('product\Property');
        $D_Merchant = D('site\Merchant');
        $D_CarCylinder = D('car\CarCylinder');
        $D_CarBrand = D('car\CarBrand');
        $_clean_form = $D_Property->clean_form();
        //门店
        $_where = array();
        $_order = array("time_add"=>'DESC');
        $_resStoreList = $D_Merchant->getAllPagesize($_where,$_order);
        $this->_arr['resStore'] = $_resStoreList;
        //发动机
        $_cylinderList = $D_CarCylinder->cylinderList();
        $this->_arr['resCarCylinder'] = $_cylinderList;
        //汽车品牌
        $_order = array("letter"=>'ASC');
        $_carBrandList = $D_CarBrand->getAllPagesize($_where,$_order);;
        $this->_arr['resCarBrand'] = $_carBrandList;
        //默认发动机缸数
        $this->_arr['cylinder_id'] = 0;
        //最后一次订单
        $D_Order = D('order\Order');
        $D_Product = D('product\Product');
        $_lastOrderRCD = array();
        $_clean_form = $D_Product->clean_form();
        $_where = array(
                "front_uid" => $this->_arr[self::FRONT_UID],
                "order_type" => $_clean_form['type']
            );
        $_lastOrderRCD = $D_Order->where($_where)->order(array('id'=>'DESC'))->find();
        $_lastPro = json_decode($_lastOrderRCD['product_info'],true);
        $_lastOrder = $_lastPro[0]['product_detail'];
        if(!$_lastOrder['text_series']&&$_lastOrder['car_series']){
            $_where_car = array(
                    "id" => $_lastOrder['car_series']
                );
            $_car = D('car\CarSeries')->where($_where_car)->find();
            $_lastOrder['text_series'] = $_car['title'];
        }

        $this->_arr['lastOrder'] = $_lastOrder;
        $this->_showDisplay();
    }

    public function fetchAmount(){
        $_store_id = I('post.store_id');
        $_cylinder_id = I('post.cylinder_id');
        $_obj = array(
                "store_id" => $_store_id,
                "cylinder_id" => $_cylinder_id
            );
        $_price = $this->orderAmount($_obj);
        pushJson('ok',array(
                "amount" => floatval($_price)
            ));
    }

    protected function orderAmount($_obj){
        $_store_id = $_obj['store_id'];
        $_cylinder_id = $_obj['cylinder_id'];
        $D_Property = D('product\Property');
        $D_PropertyPrice = D('product\PropertyPrice');
        $D_Product = D('product\Product');
        $_product = $D_Product ->clean_form();
        $_pro_str = $_product['type'].','.$_product['pid'];
        $_clean_form = $D_Property->clean_form();
        $_where = array(
                "pro_str" => $_pro_str,
                "pro_key" => $_clean_form['store']['key'].'|'.$_store_id .
                    ','.$_clean_form['cylinder']['key'].'|'.$_cylinder_id
            );
        $_price = $D_PropertyPrice->where($_where)->getField('price');
        return $_price;
    }

    
    
   
    
}
