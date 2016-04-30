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
        $this->_cache_path = C('DATA_CACHE_PATH') . 'async/';
        
    }
    /**
     * 设备远程解锁
     * front_uid,role,device_id
     */
    public function unlock(){
        set_time_limit(0);
        $_unlock_action_start = time();

        $_front_uid = I('get.front_uid');
        $_role = I('get.role')?I('get.role'):'member';
        $_device_id = I('get.device_id');


        $this->write_cache(array(
                "act" => "unlock start 36",
                "date" => date('Y-m-d H:i')
            ));
        usleep(500000);

        //test end
        $D_LogUnlock = D('log\LogUnlock');
        $D_Device = D('site\Device');
        $_device = $D_Device->where(
                array('id' => $_device_id)
            )->find();
        $_auto_lock = $_device['auto_lock'];
        if(!$_device['device_sn'] || !$_device['device_pass']){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => $_unlock_action_start,
                'time_end' => time(),
                'status' => self::$errorsn,
                'message' => 'sn,密码错误'
            );
            $_rtn = $D_LogUnlock->data($_data)->add();
            if($_rtn)exit("56");
        }

        //查询锁定状态
        $_remote = new \Verdor\yunplc\Yunplc($_device['device_sn'],$_device['device_pass']);
        
        $_arr = array('1','锁机');
        $_rtn_lock = $_remote->remote_read($_arr);
 
        $this->write_cache(array(
                "act" => "return lock 67",
                "date" => date('Y-m-d H:i'),
                "rtn" => json_encode($_rtn_lock)
            ));
 
        $_rtn = array();
        foreach($_rtn_lock as $k=>$v){
            $_rtn[$k] = trim($v);
        }
        
        $this->write_cache(array(
                "act" => "lock start 74",
                "date" => date('Y-m-d H:i'),
                "rtn" => json_encode($_rtn)
            ));

        
        if(trim($_rtn[0]) == 'ERROR'||count($_rtn) < 1){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => $_unlock_action_start,
                'time_end' => time(),
                'status' => self::$errorread,
                'message' => $_rtn[2]?$_rtn[2]:"未通电源"
            );
            $_rtn_add = $D_LogUnlock->data($_data)->add();
            $this->write_cache(array(
                "act" => "lock start 99",
                "date" => date('Y-m-d H:i'),
                "rtn" => json_encode($_rtn)
            ));
            if($_rtn_add)exit("86");
        }else if(trim($_rtn[2]) == '0'){
            $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => $_unlock_action_start,
                'time_end' => time(),
                'status' => self::$unlocked,
                'message' => '机器已解锁'
            );
            $_rtn_add = $D_LogUnlock->data($_data)->add();
            if($_rtn_add)exit("无需解锁");
        }


        $_data = array(
                'device_id' => $_device_id,
                'front_uid' => $_front_uid,
                'role'  => $_role,
                'time_add' => $_unlock_action_start,
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
            $r = $D_LogUnlock->where($_where)->data($_data)->save();
            if($r)exit("131");
        }
        // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
        // fwrite($fp, "开机".',128'.date('Y-m-d H:i'));
        // fclose($fp);

        //每隔10秒更新time_end并查询是否停止
        $_time_s = time();
        $_has_start = false;
        $_act_count = 0;
        $_locked = false;
        while(true){
            if(!$_has_start){
                $_arr_work = array('2','启动','操作计数');
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
                $_r = $D_LogUnlock->where($_where)->data($_data)->save();
                //如已停止或断电则更新time_end，并退出
                if($_r)exit("171");
            }

            if($_arr_work[1] == '启动' && trim($_rtn[2]) == '1'){
                $_has_start = true;
                $_act_count = trim($_rtn[3]);


                $_data = array(
                        'status' => self::$working,
                        'time_end' => time(),
                        'message' => '工作中'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
            }

            if($_has_start && !$_locked && $_auto_lock){
                $_arr_count = array('1','操作计数');
                $_rst_count = $_remote->remote_read($_arr_count);
                $_rtn = array();
                foreach($_rst_count as $k=>$v){
                    $_rtn[$k] = trim($v);
                }

                // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
                // fwrite($fp, "返回：".$_rtn[2]."##".$_act_count.',184');
                // fclose($fp);

                if(trim($_rtn[2]) > $_act_count){
                    $_arr = array('1','锁机','1');
                    $_rtn_lock = $_remote->remote_write($_arr);
                    $_locked = true;
                }
                
            }

            //如已停止或断电则更新time_end，并退出
            if($_arr_work[1] == '停止' && trim($_rtn[2]) == '1'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => '已停止'
                    );
                $_r = $D_LogUnlock->where($_where)->data($_data)->save();
                if($_r)exit("215");
            }

            if(time() - $_time_s > 1200){
                $_data = array(
                        'status' => self::$timeout,
                        'time_end' => time(),
                        'message' => '超时退出'
                    );
                $_r = $D_LogUnlock->where($_where)->data($_data)->save();
                if($_r)exit("225");
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
        $_auto_lock = I('get.auto_lock')?true:false;

        $_time_s = time();
        $_remote = new \Verdor\yunplc\Yunplc($_device_sn,$_device_pass);
        $D_LogUnlock = D('log\LogUnlock');
   
        $_has_start = false;
        $_act_count = 0;
        $_locked = false;
        while(true){
            // $fp = fopen(C('DATA_CACHE_PATH') . 'yunplc/' . 'test.txt', "a+");
            // fwrite($fp, date('Y-m-d h:i:s',time()).'###');
            // fclose($fp);
            if(!$_has_start){
                $_arr_work = array('2','启动','操作计数');
                $_rst_work = $_remote->remote_read($_arr_work);
            }else{
                $_arr_work = array('1','停止');
                $_rst_work = $_remote->remote_read($_arr_work);
            }
            if(!is_array($_rst_work) && trim($_rst_work) == ''){
                exit("错误");
            }
            $_where = array(
                    'id' => $_update_id
                );
            $_data = array(
                    'time_end' => time()
                );
            $D_LogUnlock->where($_where)->data($_data)->save();
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
                exit('278');
            }
            if($_arr[1] == '启动' && trim($_rtn[2]) == '1'){
                $_has_start = true;
                $_act_count = trim($_rtn[3]);
                $_data = array(
                        'status' => self::$working,
                        'time_end' => time(),
                        'message' => '工作中'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
            }

            if($_has_start && !$_locked && $_auto_lock){
                $_arr_count = array('1','操作计数');
                $_rst_count = $_remote->remote_read($_arr_count);
                $_rtn = array();
                foreach($_rst_count as $k=>$v){
                    $_rtn[$k] = trim($v);
                }

                if(trim($_rtn[2]) > $_act_count){
                    $_arr = array('1','锁机','1');
                    $_rtn_lock = $_remote->remote_write($_arr);
                    $_locked = true;
                }
            }


            //如已停止或断电则更新time_end，并退出
            if($_arr[1] == '停止' && trim($_rtn[2]) == '1'){
                $_data = array(
                        'status' => self::$end,
                        'time_end' => time(),
                        'message' => '已停止'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("315");
            }
            //超时退出设置
            if(time() - $_time_s > 1200){
                $_data = array(
                        'status' => self::$timeout,
                        'time_end' => time(),
                        'message' => '超时退出'
                    );
                $D_LogUnlock->where($_where)->data($_data)->save();
                exit("325");
            }
            sleep(10);
        }
        exit("stop");
    }

    protected function write_cache($_data){
        if(true){
            $fp = fopen($this->_cache_path .  'log.txt', "a+");
            fwrite($fp, json_encode($_data).'###\n\r');
            fclose($fp);
        }
    }
    
}
