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
        ),
        "TM00351" => array(
            "first_class" => "IT科技",
            "second_class" => "互联网|电子商务",
            "title" => "新订单通知",
            "template_id_short" => "TM00351",
            "template_id" => "CC1GXttduCluc5YnqYZxbFwUMA2QXXdEbu3k6xan_Y8"
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
  public function sendTemp($_options,$_open_id,$_template_id_short,$_detail_url,$_ext = array()){
    $_template = $this->tempBase($_template_id_short);
    $_template_id = $_template["template_id"];
    vendor('Weixin.wechat', '', '.class.php');
    $options = array(
        'token' => $_options['wx_token'],
        'appid' => $_options['wx_appid'],
        'appsecret' => $_options['wx_appsecret'],
    );
    $wechatObj = new \Wechat($options);
    $_color_blue = "#4a5077";
    switch($_template_id_short){
        case "TM00015":
            $_data = array(
                    'first' => array(
                        'value' => "您的订单已支付成功!",
                        "color" => $_color_blue
                    ),
                    'orderMoneySum' => array(
                        'value' => $_ext['money'],
                        "color" => $_color_blue
                    ),
                    'orderProductName' => array(
                        'value' => $_ext['product_name'],
                        "color" => $_color_blue
                    ),
                    'Remark' => array(
                        'value' => "如有问题可直接在公众号留言，我们将第一时间为您服务！",
                        "color" => $_color_blue
                    )
                );
        break;
        case "TM00351":
            $_data = array(
                'first' => array(
                    'value' => "您收到了一条新的订单!",
                    "color" => $_color_blue
                ),
                'tradeDateTime' => array(
                    'value' => $_ext['tradeDateTime'],
                    "color" => $_color_blue
                ),
                'orderType' => array(
                    'value' => $_ext['orderType'],
                    "color" => $_color_blue
                ),
                'customerInfo' => array(
                    'value' => $_ext['customerInfo'],
                    "color" => $_color_blue
                ),
                'orderItemName' => array(
                    'value' => $_ext['orderItemName'],
                    "color" => $_color_blue
                ),
                'orderItemData' => array(
                    'value' => $_ext['orderItemData'],
                    "color" => $_color_blue
                ),
                'remark' => array(
                    'value' => $_ext['remark']?$_ext['remark']:"",
                    "color" => $_color_blue
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
    $_r = $wechatObj->sendTemplateMessage($sendData);
    return $_r;

  }
    
}