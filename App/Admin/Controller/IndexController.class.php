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
        
        $_id = $_device_id = (int)I('get.id');
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
                //写记录之前应先查询当前操作是否与当前状态一致，若一致则返回无需操作
                $_act_read = array('1',$_act_arr[1]);
                $_rtn_read = $_remote->remote_read($_act_read);
                foreach($_rtn_read as $k=>$v){
                    $_rtn_read[$k] = trim($v);
                }
                if(trim($_rtn_read[0]) == 'ERROR'){
                    pushError(trim($_rtn_read[2]));
                }
                if($_rtn_read[2] == $_act_arr[2]){
                    pushError('本次操作未更新状态');
                }

                $_rtn = $_remote->remote_write($_act_arr);

                foreach($_rtn as $k=>$v){
                    $_rtn[$k] = trim($v);
                }
                if(trim($_rtn[0]) == 'ERROR'){
                    pushError(trim($_rtn[2]));
                }

                //挂远程操作
                if(($_act_arr[1] == '开机' && $_act_arr[2] == '1') || 
                    ($_act_arr[1] == '锁机' && $_act_arr[2] == '0')){
                    $_update_id = D('log\LogUnlock')->data(
                        array(
                                'device_id' => $_device_id,
                                'front_uid' => 0,
                                'role'  => 'admin',
                                'time_add' => time(),
                                'time_end' => time(),
                                'status' => 'unlock'
                            )
                    )->add();
                    Vendor('asynHandle.asynHandle','','.class.php');
                    $obj    = new \Verdor\asynHandle\asynHandle();
                    $_url = uDomain('www','/Asyn/unlock_admin',array(
                        'update_id' => $_update_id,
                        'device_sn' => $_resNote['device_sn'],
                        'device_pass' => $_resNote['device_pass']
                        ));

                    $obj->Request($_url);
                }//远程 end
                pushJson('操作成功',$_rtn);
            }
            if(!$_resNote)exit;
            $this->_arr['resAttr'] = $D_Device->actionArr();
            $this->_arr['actionAttr'] = array('操作计数清零','锁机','开机');
            $this->_arr['resWiki'] = $_resNote;

         }else{exit;}
        $this->_showDisplay($_tem_str);

    }

    public function unlock_log(){
        $D_Device = D('site\Device');
        $_id = $_device_id = (int)I('get.id');
        $_where = array(
            'id'=>$_id
            );
        $_resNote = $D_Device->where($_where)->find();
        if(!$_resNote)exit('设备不存在');
        $D_LogUnlock = D('log\LogUnlock');
        $_key_word = I('get.keyword');
        $_time_start = I('get.time_start');
        $this->_arr['time_end'] = $_time_end = I('get.time_end');
        $_where = array(
            "device_id" => array("eq",$_id)
        );
        if($_time_start && $_time_end){
            $_where['time_add'] = array(
                    "between",array(strtotime($_time_start),strtotime($_time_end))
                );
        }else if($_time_start && !$_time_end){
            $_where['time_add'] = array(
                    "egt",strtotime($_time_start)
                );
        }else if(!$_time_start && $_time_end){
            $_where['time_add'] = array(
                    "elt",strtotime($_time_end)
                );
        }
        if($_time_start){
            $this->_arr['time_start'] = strtotime($_time_start);
        }
        if($_time_start){
            $this->_arr['time_end'] = strtotime($_time_end);
        }
        $_order = array("id"=>'DESC');
        $resList = $D_LogUnlock->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['device_name'] = $_resNote['device_name'];
            $resList['lists'][$k]['member'] = ($v['role'] == 'admin')?'管理员':
            (D('my\Member')->where(array('id'=>$v['front_uid']))->getField('real_name'));
            $resList['lists'][$k]['status_name'] = $D_LogUnlock->status_arr($v['status']);
        }
        $this->_arr['resList'] = $resList;
        $this->_showDisplay();

    }
    
    
}
