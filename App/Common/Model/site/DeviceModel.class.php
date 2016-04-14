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
    
    
}