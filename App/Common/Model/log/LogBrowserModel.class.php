<?php

namespace Common\Model\log;

/**
 * useage
 * D('logs\LogAccount')->write_logs();
 * 前台浏览日志
 */
class LogBrowserModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_log_browser';
    
    public function write_logs($_data){
        $_data['ip'] = get_client_ip();
		$_data['time_add'] = NOW_TIME;
        $_data['url'] = $_data['url']?$_data['url']:$_SERVER["HTTP_REFERER"];
		$_data['agent'] = $_SERVER['HTTP_USER_AGENT'];
		return $this->add($_data);
    }
}