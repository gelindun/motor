<?php

namespace Home\Controller;


class WxController extends HomeController {

    public function _initialize() {
       parent::_initialize();
    }
    
    public function index(){
        import("Vendor.Weixin.wechat");
        $_options = array(
            'token'=> $this->_arr['WX_BASE']['wx_token'],
            'appid'=> $this->_arr['WX_BASE']['wx_appid'],
            'appsecret'=> $this->_arr['WX_BASE']['wx_appsecret']
        );
        $weObj = new \Wechat($_options);
        $weObj->valid();
        $D_LogAttention = D('log\LogAttention');
        $_where = array();
        //$type = \Wechat::MSGTYPE_EVENT;
        $type = $weObj->getRev()->getRevType();
        //接收内容
        $_dataMessage = array();
        $_tmpData['fromUsername'] = $weObj->getRevFrom();
        $_tmpData['toUsername'] = $weObj->getRevTo();
        \Think\Log::write('type:'.$type);
        switch($type) {
            case \Wechat::MSGTYPE_TEXT:
                //在线客服
                //关键语回复
                //关键语回复
                $messageAll = $weObj->getRevContent();
                $resReturn = $M_UserAttention->returnKeyword($messageAll, $id, $_tmpData);
                //$resReturn = $M_UserAttention->returnKeyword('#sq#上墙，求iphone', $id);
                if($resReturn['type'] == 'text') {
                    $weObj->text($resReturn['text'])->reply();
                } else if($resReturn['type'] == 'news') {
                    $weObj->news($resReturn['news'])->reply();
                }
                break;
            case \Wechat::MSGTYPE_EVENT:
                $eventArr = $weObj->getRevEvent();
                // $eventArr['event'] = 'subscribe';
                // $eventArr['key'] = '14';
                // $_tmpData['fromUsername'] = "ohhsxt7iYRZT6HZkgxGHf0q08gww";//test
                if($eventArr['event'] == 'subscribe') {
                    //新增关注
                    //关注日志
                    $D_LogAttention->addDataLog($eventArr['key'],  0,$_tmpData['fromUsername']);
                } else if($eventArr['event'] == 'unsubscribe') {
                    //取消关注
                    //关注日志
                    $D_LogAttention->addDataLog($eventArr['key'],  2, $_tmpData['fromUsername']);
                } else if($eventArr['event'] == 'CLICK') {
                    //菜单点击
                    
                } else if($eventArr['event'] == 'SCAN') {
                    $D_LogAttention->addDataLog($eventArr['key'],  1, $_tmpData['fromUsername']);
                }
                break;
            case \Wechat::MSGTYPE_IMAGE:
                $messageAll = $weObj->getRevPic();
                $_dataMessage['keywords']= $messageAll;
                break;
            case \Wechat::MSGTYPE_LOCATION:
                $messageAll = $weObj->getRevGeo();
                $_dataMessage['keywords']= $messageAll['x'] . ',' . $messageAll['y'];
                break;
            case \Wechat::MSGTYPE_LINK:
                $messageAll = $weObj->getRevLink();
                $_dataMessage['keywords']= $messageAll['url'];
                break;
            default:
                $_dataMessage['k_type'] = '';
                break;
        }//end switch

    }
}
