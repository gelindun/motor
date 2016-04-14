<?php

namespace Admin\Controller;
use Think\Controller;

class IndexController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        $D_Device= D('site\Device');
        $D_Merchant = D('site\Merchant');
        $_key_word = I('get.keyword');
        $_where = array(
            "delete" => array("eq","0")
        );
        if($_key_word){
            $_map["device_name"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["device_sn"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["remark"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("time_add"=>'DESC');
        $resList = $D_Device->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['store_name'] = $D_Merchant->where(
                    array('id' => $v['mid'])
                )->getField('store_name');
        }
        $this->_arr['resList'] = $resList;
        $this->_showDisplay();
        
    }
    
    
}
