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
        //\Think\Log::write('type:'.$type);
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
                    $D_LogAttention->addDataLog($eventArr['key'],  0,$_tmpData['fromUsername']);
                } else if($eventArr['event'] == 'unsubscribe') {
                    //取消关注
                    $D_LogAttention->addDataLog($eventArr['key'],  2, $_tmpData['fromUsername']);
                } else if($eventArr['event'] == 'CLICK') {
                    //菜单点击
                    $D_WxMenu = D('wx\WxMenu');
                    $_where['id'] = $eventArr['key'];
                    $_res = $D_WxMenu->where($_where)->find();
                    if($_res['type'] == '1') {
                        $D_MaterialText = D('wx\MaterialText');
                        $_text = $D_MaterialText->where(array("id"=>$_res['rid']))->getField('content');
                        $weObj->text($_text)->reply();
                    } else if($_res['type'] == '3') {
                        $D_MaterialNews = D('wx\MaterialNews');
                        /**
                        $_return[0]['Title'] = $_resInfo['title'];
                        $_return[0]['PicUrl'] = C('S_DOMAIN') . showPic($_resInfo['title_pic_url']);
                        $_return[0]['Description'] = $_resInfo['contents'];                    
                        $_return[0]['Url'] = $_resInfo['url'];   
                        */
                        $_where_e = array(
                            "id" => (int)$_res['rid']
                        );
                        $_rstA = $D_MaterialNews->where($_where_e)->find();
                        if($_rstA){
                            $_arr = array();
                            array_push($_arr,$_rstA);
                            $_where_c = array(
                                    "parent_id" => $_rstA['id']
                                );
                            $_rst_c = $D_MaterialNews->where($_where_c)->select();
                            if(count($_rst_c)){
                                foreach($_rst_c as $k=>$v){
                                    array_push($_arr,$v);
                                }
                            }
                            foreach($_arr as $k=>$v){
                                if($v['url']){
                                    $_link = $v['url'];
                                    $preg = '|^http|';
                                    if(!preg_match($preg,$url)) {
                                         $_link = C('MAIN_DOMAIN').$_link;
                                    }
                                }

                                $_arrNew[$k] = array(
                                        "Title" => $v['title'],
                                        "PicUrl" => C('MAIN_DOMAIN').showPic($v['thumb']),
                                        "Description" => $v['Description'],
                                        "Url" = '';
                                    )
                                if($_link){
                                    $_arrNew[$k]['Url'] = $_link;
                                }
                            }
                            $weObj->news($_arrNew)->reply(); 
                        }
                    }

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
