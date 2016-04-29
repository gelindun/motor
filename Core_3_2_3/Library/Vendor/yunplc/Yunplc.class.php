<?php
namespace Verdor\yunplc;

class yunplc {
  private $sn;
  private $password;
  private $_sid;
  private $_act_host;
  private $_login_host = "http://www.yunplc.com:7080";
  private $_cache_path;

  public function __construct($sn, $password) {
    $this->sn = $sn;
    $this->password = $password;
    $this->_cache_path = C('DATA_CACHE_PATH') . 'yunplc/';
    forMkdir($this->_cache_path);
    $_remote = $this->read_cache();
    if($_remote){
      $this->_sid = $_remote['SID'];
      $this->_acthost = $_remote['ADDR'];
    }
  }

  public function login(){
    $_url_login = $this->_login_host."/exlog";
    $_data_login = array(
            "GRM" => $this->sn,
            "PASS" => $this->password
        );
    $_rst = http_post($_url_login,$_data_login);
    $_rst = explode(PHP_EOL, $_rst);
    $_rtn = array();
    foreach($_rst as $k=>$v){
      if($k == 0 && trim(strtoupper($v))!='OK'){
        return $_rtn;
      }
      if($k > 0){
        $_r = explode('=', $v);
        $_rtn[trim($_r[0])] = trim($_r[1]);
      }
    }

    $_rtn['ADDR'] = "http://".$_rtn['ADDR'];
    $this->write_cache($_rtn);
    $this->_sid = $_rtn['SID'];
    $this->_acthost = $_rtn['ADDR'];
    return $_rtn;
  }

  public function remote_write($_data = array("1","开机","0")){
        if(!$this->_sid || !$this->_acthost){
          $_rtn = $this->login();
        }
        $_sid = $_rtn['SID']?$_rtn['SID']:$this->_sid;
        $_host = $_rtn['ADDR']?$_rtn['ADDR']:$this->_acthost;
        $_url = $_host."/exdata?SID=".$_sid."&OP=W";
        $_post_str = $_data;
        $_rst = $this->file_get_contents_post($_url,$_post_str);
        if($_rst){
          $_rtn = explode(PHP_EOL, $_rst);
          if(trim($_rtn[0]) == 'ERROR'&&trim($_rtn[1]) == 8){
            $this->login();
            $this->remote_write($_data);
            exit;
          }
        }
        return $_rtn;
    }
  //ERROR
  //9
  //该设备没有登录信息，请确认您的设备是否已经开机并连接到网络
  public function remote_read($_data = array("1","开机")){
        if(!$this->_sid || !$this->_acthost){
          $_rtn = $this->login();
        }
        $_sid = $_rtn['SID']?$_rtn['SID']:$this->_sid;
        $_host = $_rtn['ADDR']?$_rtn['ADDR']:$this->_acthost;
        $_url = $_host."/exdata?SID=".$_sid."&OP=R";


        $_post_str = $_data;
        $_rst = $this->file_get_contents_post($_url,$_post_str);
        //dump($_url);

        if($_rst){
          $_rtn = explode(PHP_EOL, $_rst);
          if(trim($_rtn[0]) == 'ERROR'&&trim($_rtn[1]) == 8){
            $this->login();
            $this->remote_read($_data);
            exit;
          }
        }
        return $_rtn;
    }

  /**
  * array(1,2,3)
  * http://www.yunplc.com:7080
  */
  public function file_get_contents_post($url, $post = array()) {
      
      $options = array(  
          'http' => array(  
              'method' => 'POST',  
              'header'=>"Content-type: text/plain;charset=UTF-8\r\n",
          ),  
      ); 
      if($post){
        $_content = implode($post,"\r\n");
        $options['http']['content'] = $_content;
      }
      $result = file_get_contents($url, false, stream_context_create($options));  
      return $result;  

  }  

  protected function write_cache($_data){
    $fp = fopen($this->_cache_path . $this->sn . '.json', "w");
    fwrite($fp, json_encode($_data));
    fclose($fp);
  }

  protected function read_cache(){
    $_data = json_decode(file_get_contents($this->_cache_path . $this->sn . '.json'),true);
    return $_data;
  }

  
}

