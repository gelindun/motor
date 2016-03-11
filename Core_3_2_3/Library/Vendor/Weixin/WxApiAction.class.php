<?php
/**
 * @copyright www.itokit.com
 * 微信开发者接口
 */
class WxApiAction {
    private $_token = '';
    public $fromUsername; //发送用户
    public $toUsername; //接收用户(开发者帐号)
    public $keyword;   //接收内容
    public $postMsgType = '';  //接收内容类型
    public $event ='';   //接收的事件类型
    private $time = 0;
    private $_debug = true;
    
    function __construct() {
        $this->time = time();
        header("Content-type:text/html;charset=utf-8");
    }
    
    function setToken($val) {
        if($val) {
            $this->_token = $val;
            return true;
        }
        return false;
    }
    
    
    /**
     * 验证安全
     */
    public function checkSafe($uid='') {
        $_flog = false;
        $echoStr = $_GET['echostr'];
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];	

        $token = $this->_token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ) {
            echo $echoStr;
            $this->_getMessage($uid);
            $_flog = true;
        } else {
            $_flog = false;
            if(!$this->_debug) {
                exit('deny access!');
            } else {
                $this->fromUsername = 'aa';
                $this->postMsgType = 'text';
                $this->keyword = '人品袁大头';
                $this->_getMessage();
                //$this->postMsgType = 'event';
            }
        }
        return $_flog;
    }
    
    /**
     * 发送消息
     */
    public function sendMsg($opt, $type = 1) {
        if(in_array($type, array(9, 1))) {
            $this->_sendText($opt);
        } else if(in_array($type, array(3, 4))) {
            $this->_sendPic($opt);
        } else if(in_array($type, array(2))) {
            $opt['res'][0]['url'] = $opt['res'][0]['link_url'];
            $this->_sendPic($opt);
        }
        
    }
    
    /**
     * 接收消息
     */
    private function _getMessage($uid='') {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->fromUsername = $postObj->FromUserName; //发送者openid
            $this->toUsername = $postObj->ToUserName; //开发者id
            $this->postMsgType = $postObj->MsgType;
            
            if($this->postMsgType == 'text') {
                $this->keyword = trim($postObj->Content); //接收内容
            } else if($this->postMsgType == 'event') {
                $this->event = trim($postObj->Event); //接收事件
            }
            //存入消息列表
            $_data = array();
            $_data['keywords'] = $this->keyword;
            if($postObj->Location_X && $postObj->Location_Y) {
                $_data['keywords'] = $postObj->Location_X 
                        . ',' 
                        . $postObj->Location_Y;
            }
            $_mmM = D('MemberMessages');
            $_data['uid'] = $uid;
            $_data['uid_open_id'] = '' . $this->toUsername;
            $_data['open_id'] = '' . $this->fromUsername;
            $_data['time_add'] = time();
            $_data['k_type'] = '' . $this->postMsgType;
            $_r = $_mmM->addData($_data);
        }
        
    }    
    
    /**
     * 发送文字
     */
    private function _sendText($opt) {
        $_textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $_searchArr = array('<br>', '<br />', '</p><p>');
        $opt['Content'] = str_replace($_searchArr, "\n", $opt['Content']);
        $_searchArr = array('&#39;');
        $opt['Content'] = str_replace($_searchArr, '\'', $opt['Content']);
        
        $opt['Content'] = strip_tags($opt['Content']);
        $opt['Content'] = strCut($opt['Content'], 668);
        $resultStr = sprintf($_textTpl, 
                $this->fromUsername, 
                $this->toUsername, 
                $this->time, 
                $opt['Content']);
        echo $resultStr;exit;
    }
    
    /**
     * 发送图片
     */
    private function _sendPic($opt) {
        $_textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
 <FromUserName><![CDATA[%s]]></FromUserName>
 <CreateTime>%s</CreateTime>
 <MsgType><![CDATA[news]]></MsgType>
 <ArticleCount>" . $opt['count'] . "</ArticleCount>
 <Articles>";
        foreach($opt['res'] as $k => $v) {
            if($this->fromUsername) {
                $v['url'] .= '?token=' . $this->fromUsername;
            }
            $v['Title'] = strCut($v['Title'], 45);
            $v['content_search'] = strip_tags($v['content_search']);
            $v['content_search'] = strCut($v['content_search'], 55);
            $_textTpl .= "<item>
            <Title><![CDATA[" . $v['Title'] . "]]></Title> 
            <Description><![CDATA[" . $v['content_search'] . "]]></Description>
            <PicUrl><![CDATA[" . $v['pic_url'] . "]]></PicUrl>
            <Url><![CDATA[" . $v['url'] . "]]></Url>
            </item>";
        }
        $_textTpl .= "</Articles>
 </xml>";
        $resultStr = sprintf($_textTpl, 
                $this->fromUsername, 
                $this->toUsername, 
                $this->time
                );
        echo $resultStr;exit;
    }
    
    /**
     * 创建菜单
     * 
     * @param array $_appArr 传入数组，AppId和AppSecret; 
     * @param array $_jsonMenu 要创建菜单的json；
     */
    function createMenu($_appArr, $_jsonMenu) {
        $_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' 
                . $_appArr['appid'] . '&secret='
                . $_appArr['appsecret'];
        $_json = curl($_url);
        $_jsonArr = json_decode($_json);
        $_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' 
                . $_jsonArr->access_token;
        $_urldata = array();
        $_urldata = $_jsonMenu;
        $_res = curl($_url, $_urldata);
        $_res = json_decode($_res);
        return $_res->errcode;
    }
}