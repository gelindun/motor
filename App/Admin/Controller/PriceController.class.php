<?php

namespace Admin\Controller;
use Think\Controller;

class PriceController extends AdminController {

    public function _initialize() {
        parent::_initialize();
        $this->pgSize = 15;
    }
    
    public function index(){
        $D_Merchant = D('site\Merchant');
        $D_CarCylinder = D('car\CarCylinder');
        $_cylinderList = $D_CarCylinder->cylinderList();
        $D_Product = D('product\Product');
        $_product = $D_Product ->clean_form();
        $_pro_str = $_product['type'].','.$_product['pid'];
        $D_Property = D('product\Property');
        $D_PropertyPrice = D('product\PropertyPrice');
        $_clean_form = $D_Property->clean_form();
        if(I('post.action') === 'edit_price'){
            $_data = I('post.');
            unset($_data['action']);
            //$_clean_form['store']['key'].'|'.$_data['store']
            $_where = array(
            		"pro_str" => $_pro_str,
            		"pro_key" => array('like',$_clean_form['store']['key'].'|%')
            	);
            $D_PropertyPrice->where($_where)->delete();
            foreach($_data['price'] as $k => $v){
            	$_data_p = array(
            		"pro_str" => $_pro_str,
            		"pro_key" => $_clean_form['store']['key'].'|'.$_data['store'].
            		','.$_clean_form['cylinder']['key'].'|'.$k,
            		"price" => $v
            	);
            	if($D_PropertyPrice->create()){
            		$D_PropertyPrice->write($_data_p);
            	}else{
            		$_msg = $D_PropertyPrice->getError();
            		break;
            	}
            }
            

            if(!$_msg){
                pushJson('更新成功');
            }else{
                pushError($_msg);
            }
        }


        $_key_word = I('get.keyword');
        $_where = array(
            "s_type" => $s_type
        );
        if($_key_word){
            $_map["store_name"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("id"=>'DESC');
        $resList = $D_Merchant->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
        	$resList['lists'][$k]['cylinderList'] = $_cylinderList;
        	$_cy = $resList['lists'][$k]['cylinderList'];
        	foreach($_cy as $kk => $vv){
        		$_where = array(
        			"pro_str" => $_pro_str,
        			"pro_key" => $_clean_form['store']['key'].'|'.$v['id'].
            		','.$_clean_form['cylinder']['key'].'|'.$kk
        		);
        		$_cy[$kk]['price'] = $D_PropertyPrice->where($_where)->getField('price');
        	}
        	$resList['lists'][$k]['cylinderList'] = $_cy;
        }

        $this->_arr['resList'] = $resList;
        $this->_arr['cylinderList'] = $_cylinderList;
        $this->_showDisplay();
    }


    
    
}
