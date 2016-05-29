<?php

namespace Admin\Controller;
use Think\Controller;

class CouponController extends AdminController {

    public function _initialize() {
         parent::_initialize();
         
    }
    
    public function index(){
       $D_Coupon = D('coupon\Coupon');
       $_key_word = I('get.keyword');
       if(I('post.action') === 'delete_coupon'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_data = array(
                   "delete" => 1
               );
               $_rst =  $D_Coupon->write($_data,array('id'=>$_id));
               if(is_array($_rst)){
                    $_msg = $_rst['msg'];
                }
               
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["desc"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_where['delete'] = 0;
        $_order = array("time_add"=>'DESC',"id"=>'DESC');
        $resList = $D_Coupon->getPagesize($_where,$this->pgSize,$_order);

        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
        //exit;
    }
    
    public function coupon_edit(){
        $D_Coupon = D('coupon\Coupon');
        if(I('post.action') === 'edit_coupon'){
            $_data = I('post.','','trim');
            $_data['time_start'] = strtotime($_data['time_start']);
            $_data['time_end'] = strtotime($_data['time_end']);
          
            
            if($_data['id']){
                $_where = array(
                    'id' => $_data['id']
                );
            }
            $_rst =  $D_Coupon->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_Coupon->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        
        $this->_showDisplay();
    }
    
    
}
