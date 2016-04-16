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
    /**
    *   具体设备
    */
    public function device(){
        $D_Device = D('site\Device');
        Vendor('yunplc.Yunplc','','.class.php');
        
        $_id = (int)I('get.id');
        $_action = I('post.do_action');
        if($_id){
            $_where = array(
                'id'=>$_id
                );
            $_resNote = $D_Device->where($_where)->find();
            $_remote = new \Verdor\yunplc\Yunplc($_resNote['device_sn'],$_resNote['device_pass']);
            if($_action == 'read_attr'){
                if(!$_resNote){
                    pushError('当前设备不存在');
                }
                $_arr = $D_Device->actionArr();
                array_unshift($_arr, count($_arr));
                $_arr = array_values($_arr);
                $_rtn = $_remote->remote_read($_arr);
                foreach($_rtn as $k=>$v){
                    $_rtn[$k] = trim($v);
                }
                if(trim($_rtn[0]) == 'ERROR'){
                    pushError(trim($_rtn[2]));
                }
                pushJson('设备状态正常',$_rtn);
            }else if($_action == 'write_attr'){
                $_act_str = I('post.act_str');
                $_act_arr = explode('|', $_act_str);
                array_unshift($_act_arr, 1);
                $_rtn = $_remote->remote_write($_act_arr);
                foreach($_rtn as $k=>$v){
                    $_rtn[$k] = trim($v);
                }
                if(trim($_rtn[0]) == 'ERROR'){
                    pushError(trim($_rtn[2]));
                }
                pushJson('操作成功',$_rtn);
            }
            if(!$_resNote)exit;
            $this->_arr['resAttr'] = $D_Device->actionArr();
            $this->_arr['actionAttr'] = array('操作计数清零','锁机','开机');
            $this->_arr['resWiki'] = $_resNote;

         }else{exit;}
        $this->_showDisplay($_tem_str);

    }
    
    
}
