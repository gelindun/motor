<?php

namespace Home\Controller;
use Think\Controller;
use Org\ThinkSDK\ThinkOauth;

class MyController extends HomeController {
    private $jump_url;
    public function _initialize() {
        parent::_initialize();
        if((!I('post.') && $this->_arr['ACT_NAME'] != 'snsCallBack' && $this->_arr['ACT_NAME'] != 'login') || (I('get.jump_url'))){
            $this->jump_url = I('get.jump_url') ? urldecode(I('get.jump_url')) : getenv("HTTP_REFERER");
            if(!strpos($this->jump_url,C('COOKIE_DOMAIN'))||!$this->jump_url){
                $this->jump_url = uDomain('www','/My');
            }
            safeGetCookie('jumpUrl',$this->jump_url);
        }
        $this->jump_url = $this->_arr['jump_url'] = (safeGetCookie('jumpUrl')?safeGetCookie('jumpUrl'):"/");
        if (!$this->_arr[self::FRONT_UID]) {
            if ($this->_arr['CTR_NAME'] == 'My' && 
                    ($this->_arr['ACT_NAME'] == 'login'|| 
                    $this->_arr['ACT_NAME'] == 'register' ||
                    $this->_arr['ACT_NAME'] == 'agreement'||
                    $this->_arr['ACT_NAME'] == 'snsCallBack') ) {
               
            } else {
                $this->redirect('/My/login');
            }
        }
        //dump($this->_arr);
    }
    /**
     * 第三方登录
     */
    public function snsCallBack(){
        
        $D_MemberBind = D('my\MemberBind');
        $D_Member = D('my\Member');
        $D_User = D('my\Member');
      
        
        if(safeGetCookie('user_info')){
            $token = safeGetCookie('token');
            $_data = json_decode(safeGetCookie('user_info'),true);
            $_data['bind_name'] = $_data['name'];
            safeGetCookie('token',null);
            safeGetCookie('user_info',null);
        }else{
            if(I('get.action_from') == 'mobile'){}else{
                $code   =   I('get.code');
                $type   =   I('get.type')?I('get.type'):'qq';
                $sns    =   ThinkOauth::getInstance($type);
                $extend =   null;
                // 获取token值。
                $token  =   @$sns->getAccessToken($code, $extend);
                //$openid =   $token['openid'];
                if (is_array($token)) {
                    $_data = $sns->getUserInfo();
                    $_data['bind_name'] = $_data['name'];
                    safeGetCookie('token',$token);
                    safeGetCookie('user_info',json_encode($_data));
                }else{
                    $this->error('获取用户信息失败1');
                }
            }
            
        }
        
        $_bind_rtn = $D_MemberBind->addBind($_data);

        if($_bind_rtn['status']){
            $_bind_info = $_bind_rtn['bind_info'];
            if($this->_arr['front_uid']){
                $_where = array(
                    'type_uid' => $_bind_info['type_uid'],
                    'type' => $_bind_info['type']
                );
                $_where['front_uid'] = $this->_arr['front_uid'];
                $_binded = $D_MemberBind->where($_where)->find();
            }
            
            if($this->_arr['front_uid'] && !$_binded){
                $_where_temp = array(
                    'front_uid' => $this->_arr['front_uid'],
                    'type' => $_bind_info['type']
                );
                $_bindExist = $D_MemberBind->where($_where_temp)->find();
                if(!$_bindExist){
                    $D_MemberBind->updateUid($this->_arr['front_uid'],$_bind_info);
                    $this->_arr['success'] ="绑定成功";
                }else{
                    $this->_arr['error'] = "绑定失败，您登陆的账号已被绑定";
                }
            }else if(!$this->_arr['front_uid']){
                if($_data['type'] == "weixin"){
                    $_data_reg = array(
                        "uname" => "#".$_data['type'].dechex(time().rand(100,999)),
                        "real_name" => $_data["nick"],
                        "head_img" => $_data["head"]
                    );
                    $_rst =  $D_User->addData($_data_reg);
                    if(!is_array($_rst) && $_rst){
                        $D_MemberBind->where(array(
                                "type" => $_data['type'],
                                "type_uid" => $_data['type_uid']
                            ))->data(
                                array("front_uid" => $_rst)
                            )->save();
                        $_rtn = $D_User->where(array(
                            'uname' => $_data_reg['uname']
                        ))->find();
                        $this->setCookie($_rtn);
                        redirect(urldecode($this->jump_url));
                        //pushJson('注册成功',array('redirect'=>$this->jump_url));
                    }
                    $this->_arr['error'] = "绑定失败，请检查网络状况是否正常！";
                }
                
            }else{
                $this->_arr['error'] = "绑定失败，您登陆的账号已被绑定";
            }
        }else{
            if($this->_arr[self::FRONT_UID] && ($_bind_rtn['front_uid'] != $this->_arr[self::FRONT_UID])){
                $this->_arr['error'] = $_bind_rtn['msg'];
            }else{
                $_where = array(
                    'id' => $_bind_rtn['front_uid']
                );
                $_rst = $D_User->where($_where)->find();
                
                $this->setCookie($_rst);
                redirect(urldecode($this->jump_url));
            }
        }

        $this->_arr['bind_info'] = $_bind_info;
        $this->_arr['userInfo'] = $_data;
        $this->_arr['seo_title'] = $this->_arr['seo_keywords'] = $this->_arr['seo_description'] = '帐号绑定';
        $this->_showDisplay('my:snsCallBack');

    }
   
    /**
    *  目前只开放微信登录， 如需开放用户名密码登录
    *  设置 $_weixinOnly = false;
    **/
    public function login(){
        if($this->_arr[self::FRONT_UID]&&I('get.action') !== 'bind'){
            redirect("/");
        }
        $_weixinOnly = false;
        if(I('get.type') == 'weixin' || $_weixinOnly){
            vendor('Weixin.wechat', '', '.class.php');
            $options = array(
                'token'=> $this->_arr['WX_BASE']['wx_token'],
                'appid'=> $this->_arr['WX_BASE']['wx_appid'],
                'appsecret'=> $this->_arr['WX_BASE']['wx_appsecret'],
            );
            $_code = I('get.code')?I('get.code'):'';
            $_wechat = new \Wechat($options);
            if($_code){
                $_json = $_wechat->getOauthAccessToken();
                $_jsonRes = $_wechat->getOauthUserinfo($_json['access_token'], $_json['openid']);
                $_data = array(
                    'type' => 'weixin',
                    'type_name' => '微信',
                    'type_uid' => $_jsonRes['unionid']?$_jsonRes['unionid']:$_jsonRes['openid'],
                    'oauth_token' => $_json['access_token'],
                    'name' => $_jsonRes['nickname'],
                    'nick' => $_jsonRes['nickname'],
                    'head' => $_jsonRes['headimgurl'],
                    'expired_time' => $_json['expires_in']
                );
                safeGetCookie('token',$_json['access_token']);
                safeGetCookie('user_info',json_encode($_data));
                redirect(uDomain('www','/My/snsCallBack',array('type'=>'weixin','action_from'=>'mobile')));
            } else {
               $callback = uDomain('www','/My/login',array(
                   'type' => 'weixin'
               ));
               $_r = $_wechat->getOauthRedirect($callback);
               redirect($_r);
            }
            exit;
        }
        
        
        $_callBack = I('get.type');
        if($_callBack){
            $_allowArr = array('qq','sina','weixin');
            if(in_array($_callBack,$_allowArr)){
                $sdk = ThinkOauth::getInstance($_callBack);
                redirect($sdk->getRequestCodeURL());
            }
            
        }
        if(I('post.action') == 'user_login'){
            
            $_data = I('post.');
            unset($_data['action']);
            $this->_do_login($_data);
        }
        $this->_showDisplay('my:login');
    }

    private function _do_login($_data,$_rtn=0){
        $D_User = D('my\Member');
        if(!$_data['uname'] || !$_data['upwd']){
                pushError('请填写用户名,密码');
            }
            $_where = array(
                'uname' => $_data['uname'],
                'upwd'  => md5(md5($_data['upwd']))
            );
            $_rst = $D_User->where($_where)->find();
            if($_rtn){
                return $_rst;
            }
            if($_rst){
                $this->setCookie($_rst,$_data['remember']);
                pushJson('登录成功',array('redirect'=>$this->jump_url));
            }else{
                pushError('用户名或密码错误');
            }
    }
    
    private function setCookie($_rst,$_remember=0){
  
        if($_remember){
            safeGetCookie(self::FRONT_UID,$_rst['id'],array('expire'=>3600*24*7));
        }else{
            safeGetCookie(self::FRONT_UID,$_rst['id']);
        }
    }
    
    public function logout(){
        safeGetCookie(self::FRONT_UID,null);
        safeGetCookie('token',null);
        safeGetCookie('user_info',null);
        $this->redirect('/My/login');
        exit;
    }
    
    public function register(){
        if($this->_arr[self::FRONT_UID]){
            $this->redirect('/My/');
        }
        $D_User = D('my\Member');
        if(I('post.action') === 'add_user'){
            $_data = I('post.','','trim');
            
            $_rst =  $D_User->write($_data);
            if(!is_array($_rst)){
                $_rtn = $D_User->where(array(
                    'uname' => $_data['uname']
                ))->find();
                $this->setCookie($_rtn);
                pushJson('注册成功',array('redirect'=>$this->jump_url));
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        
        
        $this->_arr['resPage'] = $_resPage;
        
        
        $this->_showDisplay('my:register');
    }
    /**
     * 基本信息,扩展信息
     */
    public function profile(){
        $D_User = D('my\Member');
        if(I('post.action') === 'edit_profile'){
            $_data = I('post.','','trim');
         
            if($_data['id'] == $this->_arr[self::FRONT_UID]){
                $_where = array(
                    'id' => $_data['id']
                );
            }else{
                pushError ('更新错误');
            }
            $_rst =  $D_User->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
           
        }
        
        
        $_id = (int)$this->_arr[self::FRONT_UID];
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_User->where($_where)->find();
            if(!$_resPage)redirect(U('/My'));
            $this->_arr['resPage'] = $_resPage;
        }
        $this->_arr['gender'] = $D_User->gender();
        $this->_showDisplay('my:profile');
    }
    /**
     * 修改密码
     */
    public function passwd(){
        $D_User = D('my\Member');
        if(I('post.action') === 'edit_passwd'){
            $_data = I('post.','','trim');
            if($_data['id'] == $this->_arr[self::FRONT_UID]){
                $_where = array(
                    'id' => $_data['id']
                );
            }else{
                pushError ('更新错误');
            }
            $_rst =  $D_User->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功',array("redirect"=>U('/My/logout')));
            }else{
                pushError ($_rst['msg']);
            }
        }
        
        $this->_arr['resPage']['id'] = $this->_arr[self::FRONT_UID];
        $this->_showDisplay('my:passwd');
    }
    
    public function bind(){
        $D_MemberBind = D('my\MemberBind');
        $_where = array(
            'front_uid' => $this->_arr[self::FRONT_UID]
        );
        $_resList = $D_MemberBind->where($_where)->select();
        $_resPage = array();
        foreach($_resList as $k => $v){
            $_resPage[$v['type']] = $v;
        }
        
        $this->_arr['resPage'] = $_resPage;
        $this->_showDisplay('my:bind');
    }
    
    public function agreement(){
        $this->_showDisplay('my:agreement');
    }
    /**
     * 前端会员首页
     */
    public function index(){
        
        $this->_showDisplay('my:index');
    }
    /**
     * 前端会员首页
     */
    public function order(){
        
        $_orderType = I('get.order_type')?I('get.order_type'):"ticket";
        $_orderStatus   =   (int)I('get.order_status');
        $D_Order = D('order\Order');
        $_where = array(
            'order_type' => $_orderType,
            'order_status'  =>  $_orderStatus,
            'front_uid' => $this->_arr[self::FRONT_UID]
        );
        $_order = array(
            'id' => 'DESC'
        );
        $_resList = $D_Order->getPagesize($_where,10,$_order);
        $_tempOrderstatus = $D_Order->orderStatus();
        foreach($_resList['lists'] as $k=>$v){
            $_tempStatus = $v['order_status'];
            $_resList['lists'][$k]['status_title'] = $_tempOrderstatus[$_tempStatus];
            $_resList['lists'][$k]["child"] = $D_Order->rtnOrderdetail($v);
        }
        $this->_arr['resList'] = $_resList;
        //dump($_resList);
        $this->_arr['order_type'] = $_orderType;
        $this->_arr['order_status'] = $_orderStatus;
        if(!$_tempStr){
            $_tempStr = 'my:order';
        }
        $this->_showDisplay($_tempStr);
    }
    
    
}
