<?php

namespace Common\Model\coupon;

/**
 * useage
 * D('coupon\Coupon')->write_logs();
 * 优惠券
 */
class CouponModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_coupon';
    
    public function write_logs($_data){
        
    }

    public function coupon_type(){
    	return array(
    			"reduce" => array(
    					"keys" => "reduce",
    					"title" => "现金券"
    				),
    			// "discount" => array(
    			// 		"keys" => "discount",
    			// 		"title" => "折扣券"
    			// )
    		);
    }
    
    public function add_coupon($_data){
    	return $this->addData($data);
    }

 	public function write($_data,$_where = array()){
        
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "edit_coupon"){
                
                if($this->validate($rules)->create($_data)){
                    
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }
            
        }else{
            $_data['time_add'] = time();
            if($this->validate($rules)->create($_data)){
                $rst = $this->data($_data)->add();
            }else{
                $_msg = $this->getError();
            }
        }
       
        return $_msg?array("msg"=>$_msg):$rst;
    }
    //1新增数据时候验证,2编辑数据时候验证,3全部情况下验证
    public function vRule(){
        return array(
            
        );
    }
    
}