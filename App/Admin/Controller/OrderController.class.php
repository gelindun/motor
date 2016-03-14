<?php

namespace Admin\Controller;
use Think\Controller;

class OrderController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        $_orderType = I('get.order_type')?I('get.order_type'):$this->_arr['CLEAN_FORM'];
        $_orderStatus   =   (int)I('get.order_status');
        $_keyword = I('get.keyword');
        $D_Order = D('order\Order');
        $_where = array(
            'order_type' => $_orderType,
            'order_status'  =>  $_orderStatus
        );
        if($_keyword){
            $_where['order_id'] = array('EXP','REGEXP \'^.*'.$_keyword.'.*$\'');
        }
        $_order = array(
            'id' => 'DESC'
        );
        $_resList = $D_Order->getPagesize($_where,10,$_order);
        $_tempOrderstatus = $D_Order->orderStatus();
        foreach($_resList['lists'] as $k=>$v){
            $_tempStatus = $v['order_status'];
            $_resList['lists'][$k]['status_title'] = $_tempOrderstatus[$_tempStatus];
            $_resList['lists'][$k]["child"] = $D_Order->rtnOrderdetail($v);
        }
        $this->_arr['resList'] = $_resList;
        //dump($_resList);
        $this->_arr['order_type'] = $_orderType;
        $this->_arr['order_status'] = $_orderStatus;
        $this->_showDisplay();
    }
}
