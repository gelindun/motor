<?php

namespace Common\Model\site;

/**
 * useage
 * D('site\Device')->;
 * 地区
 */
class DeviceModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_device';

    public function write($_data,$_where = array()){
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $_data['time_add'] = time();
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }

    
    
    public function vRule(){
        return array(
            array('device_name','require','请输入设备名称！'),
            array('device_sn','require','请输入设备序列号！'),
            array('device_pass','require','请输入设备密码！'),
        );
    }


    /******************
    *   序号  变量名  变量值
        1   启动      启动
        2   操作技术    0
        3   产气量 0   
        4   停止      停止
        5   故障      
        6   缺液
        7   泵运行
        8   加水  
        9   电源
        10  锁机      否
        11  开机      否
        12  操作计数清零  否
    SB接口要求传中文指令！  
    *********************/
    public function actionArr(){
        $_arr = array(
            "启动","操作计数","产气量","停止",
            "故障","缺液","泵运行","加水",
            "电源","锁机","开机","操作计数清零"
        );
        return $_arr;
    }
    
    
}