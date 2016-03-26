<?php

namespace Common\Model\my;

/**
 * useage
 * D('my\MemberBind')->;
 * front_uid
 */
class MemberBindModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_member_bind';
    
    
    public function checkBind($userInfo){
		$_where = array();
		$_where['type_uid'] = $userInfo['type_uid'];
		$_where['type'] = $userInfo['type'];
		$bindInfo = $this->where($_where)->find();
		return $bindInfo;
	}
    
    public function updateUid($front_uid,$bind_info){
        $_where = array();
		$_where['type_uid'] = $bind_info['type_uid'];
		$_where['type'] = $bind_info['type'];
        $_data = array(
            "front_uid" => $front_uid
        );
		$_rst = $this->saveData($_data,$_where);
        return $_rst;
    }
	
	public function addBind($_data){
		$bindInfo = $this->checkBind($_data);
		if(!$bindInfo){
			if($this->create($_data)){
				$bind_id = $this->addData($_data);
                $bindInfo = $this->where(
                        array(
                            "id" => $bind_id
                        )
                    )->find();
				$_return['status'] = 1;
				$_return['bind_info'] = $bindInfo?$bindInfo:array();
			}else{
				$_return['status'] = 0;
				$_return['msg'] = '绑定失败'; 
			}
		}else{
            if($bindInfo['front_uid']){
                $_return['status'] = 0;
                $_return['msg'] = '帐号已经绑定';
                $_return['front_uid'] = $bindInfo['front_uid'];
                D('Member')->where(array(
                		"id" => $bindInfo['front_uid']
                	))->data(array(
                		"head_img" => $_data['head']
                	))->save();
            }else{
                $_where = array(
                    'type_uid' => $bindInfo['type_uid'],
                    'type' => $bindInfo['type']
                );
                $bind_id = $this->saveData($_data,$_where);
				$_return['status'] = 1;
				$_return['bind_info'] = $bindInfo?$bindInfo:array();
            }
		}
		return $_return;
	}
    
    
}