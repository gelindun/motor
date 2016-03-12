<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends \Common\Controller\CommonController {

    public function _initialize() {
        parent::_initialize();
        $this->_arr['admin_uid'] = safeGetCookie(self::ADMIN_UID);

        if (empty($this->_arr['admin_uid'])) {
            if ($this->_arr['CTR_NAME'] == 'System' && ($this->_arr['ACT_NAME'] == 'index')) {
                
            } else {
                $this->redirect('/System');
            }
        }else{
            $_where = array(
                'id' => $this->_arr['admin_uid']
            );
            $D_AdminUser = D('site\AdminUser');
            $_rst = $D_AdminUser->where($_where)->find();
            $_rst['role_name'] = $D_AdminUser->ROLE[$_rst['role']];
            if(!$_rst)exit;
            $this->_arr['admin_info'] = $_rst;
        }
        
        
    }

}
