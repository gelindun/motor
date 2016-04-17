<?php

namespace Home\Controller;
use Think\Controller;
//远程操作类
class asynController extends HomeController {

    public function _initialize() {
        parent::_initialize();
        
        
    }
    /**
     * 设备远程解锁
     * front_uid,role,device_id
     */
    public function unlock(){
        set_time_limit(0);
        $_front_uid = I('get.front_uid');
        $_role = I('get.role')?I('get.role'):'member';
        $_device_id = I('get.device_id');
        //for test
  
        //test end
        $D_LogUnlock = D('log\Unlock');
        $D_Device = D('site\Device');
        $_device = $D_Device->where(
                array('id' => $_device_id)
            )->find();
        if(!$_device['device_sn'] || !$_device['device_pass']){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => 'errorsn',
                'message' => 'sn,密码错误'
            );
            $_rtn = $D_LogUnlock->data($_data)->add();
            exit();
        }
        //查询锁定状态
        $_remote = new \Verdor\yunplc\Yunplc($_resNote['device_sn'],$_resNote['device_pass']);
        
        $_arr = array('1','锁机');
        $_rtn = $_remote->remote_read($_arr);
        foreach($_rtn as $k=>$v){
            $_rtn[$k] = trim($v);
        }
        if(trim($_rtn[0]) == 'ERROR'){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => 'errorread',
                'message' => $_rtn[2]
            );
            $_rtn = $D_LogUnlock->data($_data)->add();
            exit;
        }
        //解锁，插入一条记录 time_start，并锁定设备
        
        $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => 'unlock'
            );
        $_rtn = $D_LogUnlock->data($_data)->add();
        $_arr = array('1','开机','1');
        $_rtn = $_remote->remote_write($_arr);
        if(trim($_rtn[0]) == 'ERROR'){
            $_where = array(
                    'id' => $_rtn
                );
            $_data = array(
                    'time_end' => time(),
                    'status' => 'error',
                    'message' => $_rtn[2]
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
            exit;
        }
        //每隔10秒更新time_end并查询是否停止
        $_time_s = time();
        while(true){
            $_arr = array('1','停止');
            $_rst = $_remote->remote_read($_arr);
            $_where = array(
                    'id' => $_rtn
                );
            $_data = array(
                    'time_end' => time()
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
            foreach($_rtn as $k=>$v){
                $_rtn[$k] = trim($v);
            }
            if(trim($_rtn[0]) == 'ERROR'||$_rtn[3] == '0'){
                $_data = array(
                        'status' => 'end',
                        'time_end' => time(),
                        'message' => $_rtn[2]
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                //如已停止或断电则更新time_end，并退出
                exit();
            }
            if(time() - $_time_s > 600){
                $_data = array(
                        'status' => 'timeout',
                        'time_end' => time(),
                        'message' => '超时退出'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit();
            }
            sleep(10);
        }
        //end
    }
    
}
