<?php

namespace Common\Model\log;


class LogUnlockModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_log_unlock';
    
    public function status_arr($_key){
    	$_arr =  array(
    			"errorread" => "读取属性错误",
    			"unlock" => "解锁中",
                "working" => "工作中",
    			"unlocked" => "已解锁",
    			"error" => "未知错误",
    			"timeout" => "超时自动结束",
    			"errorsn" => "sn，密码错误",
                "end" => "已停止"
    		);
    	if($_arr[$_key]){
    		return $_arr[$_key];
    	}else{
    		return $_arr;
    	}
    }
    
}