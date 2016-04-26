<?php

namespace Home\Controller;
use Think\Controller;

class PaywxController extends HomeController {

    public function _initialize() {
        parent::_initialize();
        
        define(APPID , $this->_arr['WX_BASE']['wx_appid']);  //appid
       
        define(SIGNTYPE, "sha1"); //method
        define(PARTNERKEY, $this->_arr['WX_BASE']['wx_pay_key']); //API密钥
        define(APPSERCERT, $this->_arr['WX_BASE']['wx_appsecret']);
        define(APPMCHID,$this->_arr['WX_BASE']['wx_pay_mchid']);
        
        vendor('payWx.CommonUtil');
        vendor('payWx.SDKRuntimeException', '','.class.php');
        vendor('payWx.MD5SignUtil');
        vendor('payWx.WxPayPubHelper');
        
        C('WXPAY_APPID', APPID);
        C('WXPAY_MCHID', APPMCHID);
        C('WXPAY_KEY', PARTNERKEY);
        C('WXPAY_APPSECRET', APPSERCERT);
        C('WXPAY_CURL_TIMEOUT', 30);
        
        //获取订单信息 
        $D_Order = D('order\Order');
        $_where = array(
            "id" => I('get.oid')
        );
       
        $_order = $D_Order->where($_where)->find();
        $_grpPrice = $D_Order->rtnOrderprice($_order['id']);
        $this->_arr['order'] = array(
            'oid' => $_order['id'],
            'front_uid' => $_order['front_uid'],
            'order_id' => $_order['order_id'],
            'ori_price' => $_grpPrice?$_grpPrice:$_order['price'],
            'price' => ($_grpPrice?$_grpPrice:$_order['price']) * 100
        );
        $this->log_result($log_name,"order_type:".$_order['type']."###".$this->_arr['CLEAN_PRO']['type']);
        if($_order['type'] == $this->_arr['CLEAN_PRO']['type']){
            $_pro_info = json_decode($_order['product_info'],true);
            $_pro_detail = $_pro_info[0]['product_detail'];
            if($_pro_detail['device_id']){
                $this->_arr['order']['device_id'] = $_pro_detail['device_id'];
            }
        }
        
    }
    /**
     * 微信网页支付,扫码支付
     */
    public function index(){
        $_isWeixin = strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger');
        $_fromPc = I('get.from') === 'pc'?true:false;
        $notify_url = uDomain('www','/Paywx/notify_url',array(
            'oid' => I('get.oid')
        ));
        //使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
        $unifiedOrder->setParameter("body",$this->_arr['order']['order_id']);//商品描述
        
        $out_trade_no = $this->_arr['order']['order_id'];//自定义订单号
        $unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号
        $unifiedOrder->setParameter("total_fee",$this->_arr['order']['price']);//总金额
        $unifiedOrder->setParameter("notify_url", $notify_url);//通知地址
        $log_name= C('DATA_CACHE_PATH')."/notify_url.log";
        if($_isWeixin){
            $jsApi = new \JsApi_pub();
            if(!I('get.code')) {
                //触发微信返回code码
                //'http://m.green-ton.com/'
                $_pay_url = uDomain('www').$_SERVER['REQUEST_URI'];
                C('WXPAY_JS_API_CALL_URL',urlencode($_pay_url));
                $_url = $jsApi->createOauthUrlForCode(C('WXPAY_JS_API_CALL_URL'));
                header("Location: $_url"); 
            } else {
                $code = I('get.code');
                $jsApi->setCode($code);
                $_openid = $jsApi->getOpenId();
            }
            $unifiedOrder->setParameter("openid","$_openid");//商品描述
            $unifiedOrder->setParameter("trade_type","JSAPI");
            
            $_prepay_id = $unifiedOrder->getPrepayId();
            $jsApi->setPrepayId($_prepay_id);
            $jsApiParameters = $jsApi->getParameters();
            $_jsApiParametersArr = json_decode($jsApiParameters,true);
            $this->assign('params',$_jsApiParametersArr);
            $this->assign('jsApiParameters',$jsApiParameters);
            $_tempStr = "index";
        }else{
            $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
            $_tempStr = "qrcode";
        }
        
        //获取统一支付接口结果
        $unifiedOrderResult = $unifiedOrder->getResult();
        $_err = "";
        if ($unifiedOrderResult["return_code"] == "FAIL"){
            //商户自行增加处理流程
        }elseif($unifiedOrderResult["result_code"] == "FAIL"){
            //商户自行增加处理流程
        }elseif($unifiedOrderResult["code_url"] != NULL){
            //从统一支付接口获取到code_url
            $code_url = $unifiedOrderResult["code_url"];
            //商户自行增加处理流程
        }
        $this->assign('out_trade_no',$out_trade_no);
        $this->assign('code_url',$code_url);
        $this->assign('unifiedOrderResult',$unifiedOrderResult);
        
        if(!$_isWeixin || $_fromPc){
            exit($code_url);
        }
        
        $this->_showDisplay($_tempStr);
    }
    
    public function notify_url(){
        //使用通用通知接口
        $notify = new \Notify_pub();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        $log_name= C('DATA_CACHE_PATH')."/notify_url.log";//log文件路径
        //$this->log_result($log_name,"pay test:".M()->_sql());
        if($notify->checkSign() == TRUE)
        {
            if ($notify->data["return_code"] == "FAIL") {
                $this->log_result($log_name,"【通信出错】:\n".$xml."\n");
                exit('return fail');
            }elseif($notify->data["result_code"] == "FAIL"){
                $this->log_result($log_name,"【业务出错】:\n".$xml."\n");
                exit("fail");
            }else{
                $out_trade_no = $notify->data["out_trade_no"];
                $D_Order = D('order\Order');
                $_data_order = array(
                    "pay_type" => '微信支付',
                    "order_status" => 2,
                    "time_pay" => time()
                );
                $_where = array( "order_id" => $out_trade_no );
                $D_Order->saveData($_data_order,$_where);
                //异步为机器解锁
                if(!$this->_arr['order']['device_id']){
                    $this->_arr['order'] = $D_Order->where($_where)->find();
                    $_pro_info = json_decode($this->_arr['order']['product_info'],true);
                    $_pro_detail = $_pro_info[0]['product_detail'];
                    if($_pro_detail['device_id']){
                        $this->_arr['order']['device_id'] = $_pro_detail['device_id'];
                    }
                }
                $this->log_result($log_name,"###order####:".json_encode($this->_arr['order']));
                if($this->_arr['order']['device_id']){
                    Vendor('asynHandle.asynHandle','','.class.php');
                    $obj    = new \Verdor\asynHandle\asynHandle();
                    $_url = uDomain('www','/Asyn/unlock',array(
                        'front_uid' => $this->_arr['order']['front_uid'],
                        'role' => 'member',
                        'device_id' => $this->_arr['order']['device_id']));
                    $this->log_result($log_name,"url:".$_url);
                    $obj->Request($_url);
                    //提交设备数据log
                }
                $this->log_result($log_name,"pay test:".M()->_sql());
                exit("success");
            }
            //商户自行增加处理流程,更新订单状态,数据库操作,推送支付完成信息
        }
        
    }
    
    /**
     * Native支付回调页面
     */
    public function getpackage(){
        
    }
    
    private  function  log_result($path,$word) {
        exit;
        $path = $path?$path:C('DATA_CACHE_PATH')."log_tenpay.txt";
        $fp = fopen($path,"a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    
}
