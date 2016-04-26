<?php

namespace Home\Controller;
use Think\Controller;
Vendor('yunplc.Yunplc','','.class.php');
//远程操作类
class AsynController extends HomeController {
    public static $errorread = "errorread";
    public static $unlock = "unlock";
    public static $unlocked = "unlocked";
    public static $error = "error";
    public static $working = "working";
    public static $timeout = "timeout";
    public static $errorsn = "errorsn";
    public static $end = "end";


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
        $D_LogUnlock = D('log\LogUnlock');
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
                'status' => self::$errorsn,
                'message' => 'sn,密码错误'
            );
            $_rtn = $D_LogUnlock->data($_data)->add();
            exit("ok");
        }
        //查询锁定状态
        $_remote = new \Verdor\yunplc\Yunplc($_device['device_sn'],$_device['device_pass']);
        
        $_arr = array('1','锁机');
        $_rtn_lock = $_remote->remote_read($_arr);

        $_rtn = array();
        foreach($_rtn_lock as $k=>$v){
            $_rtn[$k] = trim($v);
        }
        
        // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
        // fwrite($fp, time().','.json_encode($_rtn));
        // fclose($fp);
        if(trim($_rtn[0]) == 'ERROR'||count($_rtn) < 1){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => self::$errorread,
                'message' => $_rtn[2]?$_rtn[2]:"未通电源"
            );
            $_rtn_add = $D_LogUnlock->data($_data)->add();
            exit("ok");
        }else if(trim($_rtn[2]) == '0'){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => self::$unlocked,
                'message' => '机器已解锁'
            );
            $_rtn_add = $D_LogUnlock->data($_data)->add();
            exit("无需解锁");
        }
        //解锁，插入一条记录 time_start，并锁定设备
        // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
        // fwrite($fp, time().',83');
        // fclose($fp);
        $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => time(),
                'time_end' => time(),
                'status' => self::$unlock
            );
        $_rtn_a = $D_LogUnlock->data($_data)->add();
        $_arr = array('1','开机','1');
        $_rtn_w = $_remote->remote_write($_arr);
        $_rtn = array();
        foreach($_rtn_w as $k=>$v){
            $_rtn[$k] = trim($v);
        }
        if(trim($_rtn[0]) == 'ERROR'){
            $_where = array(
                    'id' => $_rtn_a
                );
            $_data = array(
                    'time_end' => time(),
                    'status' => self::$error,
                    'message' => trim($_rtn[2])
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
            exit("ok");
        }


        //每隔10秒更新time_end并查询是否停止
        $_time_s = time();
        $_has_start = 0;
        while(true){
            if(!$_has_start){
                $_arr_work = array('1','启动');
                $_rst_work = $_remote->remote_read($_arr_work);
            }else{
                $_arr_work = array('1','停止');
                $_rst_work = $_remote->remote_read($_arr_work);
            }

           
            $_where = array(
                    'id' => $_rtn_a
                );
            $_data = array(
                    'time_end' => time()
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
            $_rtn = array();
            foreach($_rst_work as $k=>$v){
                $_rtn[$k] = trim($v);
            }
            if(trim($_rtn[0]) == 'ERROR'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => trim($_rtn[2])
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                //如已停止或断电则更新time_end，并退出
                exit("ok");
            }

            if($_arr_work[1] == '启动' && trim($_rtn[2]) == '1'){
                $_has_start = 1;
                $_data = array(
                        'status' => self::$working,
                        'time_end' => time(),
                        'message' => '工作中'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
            }
            //如已停止或断电则更新time_end，并退出
            if($_arr_work[1] == '停止' && trim($_rtn[2]) == '1'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => '已停止'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("ok");
            }

            if(time() - $_time_s > 1200){
                $_data = array(
                        'status' => self::$timeout,
                        'time_end' => time(),
                        'message' => '超时退出'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("ok");
            }
            sleep(10);
        }

        //end
        exit('stop');
    }

    public function unlock_admin(){
        set_time_limit(0);
        $_update_id = I('get.update_id');
        $_device_sn = I('get.device_sn');
        $_device_pass = I('get.device_pass');

        $_time_s = time();
        $_remote = new \Verdor\yunplc\Yunplc($_device_sn,$_device_pass);
        $D_LogUnlock = D('log\LogUnlock');
        $_has_start = 0;
        while(true){
            // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
            // fwrite($fp, time().',');
            // fclose($fp);
            if(!$_has_start){
                $_arr = array('1','启动');
                $_rst = $_remote->remote_read($_arr);
            }else{
                $_arr = array('1','停止');
                $_rst = $_remote->remote_read($_arr);
            }
            if(trim($_rst) == ''){
                exit("错误");
            }
            $_where = array(
                    'id' => $_update_id
                );
            $_data = array(
                    'time_end' => time()
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
            foreach($_rst as $k=>$v){
                $_rtn[$k] = trim($v);
            }
            if(trim($_rtn[0]) == 'ERROR'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => trim($_rtn[2])
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
            }
            if($_arr[1] == '启动' && trim($_rtn[2]) == '1'){
                $_has_start = 1;
                $_data = array(
                        'status' => self::$working,
                        'time_end' => time(),
                        'message' => '工作中'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
            }
            //如已停止或断电则更新time_end，并退出
            if($_arr[1] == '停止' && trim($_rtn[2]) == '1'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => '已停止'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("ok");
            }
            //超时退出设置
            if(time() - $_time_s > 1200){
                $_data = array(
                        'status' => self::$timeout,
                        'time_end' => time(),
                        'message' => '超时退出'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("ok");
            }
            sleep(10);
        }
        exit("stop");
    }
    
}
