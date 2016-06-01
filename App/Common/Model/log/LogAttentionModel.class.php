<?php
/**
 * @author yeping
 * @copyright green-ton
 * 关注、取消关注日志
 * @return 
 * 
 */
namespace Common\Model\log;
class LogAttentionModel extends \Common\Model\BaseModel {
	protected $trueTableName = 'ton_log_attention';
    
    /**
     * 增加日志
     * 
     * @param string $sid 关注的渠道
     * @param int   $uid 关注的用户
     * @param int $a_status 关注状态-0关注、1扫描已经关注过、2取消关注
     */
    public function addDataLog($sid='',$a_status=0,$open_id="") {
        if($sid) {
            $sid = str_replace('qrscene_', '', $sid);
            if(strlen($sid) > 10) {
                $sid = substr($sid, 0, -10);
            }
        } else {
            $sid = 0;
        }
        $_data = array();
        $_data['sid'] = $sid;
        $_data['wx_open_id'] = $open_id;
        $_data['time_add'] = time();
        $_data['a_status'] = $a_status;
        $_data['ip'] = $_SERVER["REMOTE_ADDR"];
        $_id = $this->addData($_data);
        return $_id;
    }
}