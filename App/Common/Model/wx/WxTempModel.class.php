<?php
/**
 * useage:D('wx\WxTemp')->readBase()
 */
namespace Common\Model\wx;

Class WxTempModel {
  
  public function tempBase($_key=""){
    $_arr = array(
        "TM00015" => array(
            "first_class" => "IT科技",
            "second_class" => "互联网|电子商务",
            "title" => "订单支付成功",
            "template_id_short" => "TM00015",
            "template_id" => "RlzwVwIEhfjkE-3_UGSoarsEeXw2VMFdjcoc4-xtFTU"
        )
    );
    if($_arr[$_key]){
        return $_arr[$_key];
    }
    return $_arr;
  }
  /**
  * 发送模板消息 
    "touser":"OPENID",
    "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
    "url":"http://weixin.qq.com/download",
    "topcolor":"#FF0000",
    "data":{
        "参数名1": {
            "value":"参数",
            "color":"#173177"    //参数颜色
            },
        "Date":{
            "value":"06月07日 19时24分",
            "color":"#173177"
            },
        "CardNumber":{
            "value":"0426",
            "color":"#173177"
            },
        "Type":{
            "value":"消费",
            "color":"#173177"
            }
    }
  */
  public function sendTemp($_open_id,$_template_id_short,$_detail_url,$_ext = array()){
    $_template = $this->tempBase($_template_id_short);
    $_template_id = $_template["template_id"];
    vendor('Weixin.wechat', '', '.class.php');
        $options = array(
            'token' => $this->_arr['WX_BASE']['wx_token'],
            'appid' => $this->_arr['WX_BASE']['wx_appid'],
            'appsecret' => $this->_arr['WX_BASE']['wx_appsecret'],
        );
    $wechatObj = new \Wechat($options);
    switch($_template_id_short){
        case "TM00015":
            $_data = array(
                    'first' => array(
                        'value' => "您的订单已支付成功!",
                        "color" => "#4a5077"
                    ),
                    'orderMoneySum' => array(
                        'title' => '支付金额',
                        'value' => $_ext['money'],
                        "color" => "#4a5077"
                    ),
                    'orderProductName' => array(
                        'title' => '商品信息',
                        'value' => $_ext['product_name'],
                        "color" => "#4a5077"
                    ),
                    'Remark' => array(
                        'value' => "\r\n如有问题可直接在公众号留言，我们将第一时间为您服务！",
                        "color" => "#4a5077"
                    )
                );
        break;
        default :
        break;
    }
    $sendData = array(
            "touser" => $_open_id,
            "template_id" => $_template_id,
            "url" => $_detail_url,
            "topcolor" => "#FF0000",
            "data" => $_data
        );
    $_r = $weObj->sendTemplateMessage($sendData);
    return $_r;

  }
    
}