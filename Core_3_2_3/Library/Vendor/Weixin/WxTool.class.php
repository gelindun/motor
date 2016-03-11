<?php
/**
 * @copyright www.itokit.com
 * 第三方API接口
 */
class WxTool {
	public $str_cookie;
	public $url;
    public $rtn_arr;
     public $rtn_type = "string";
    public $err_msg = "您输入的词语暂未能被识别";
    //上一步操作纪录 
    public $last_record;
    //爱帮 key
    public $ab_key = "bb6c4d3995afeba4dd5bc93da5efb2c5";
    //爱帮 sdk路径
    public $ab_path;
    //baidu key
    public $baidu_key = "70c24e8f7953423f1c93588bbc0de511";
    //aikuaidi key
    public $kuaidi_key = "8fd3916036cb400a8820592fa09fcfd4";


	public function __construct() {
        header( 'Content-Type:text/html;charset=utf-8 ');
        $this->rtn_arr = array(
                "status" => "error",
                "msg" => ""
            );
    }
    /**
     * [setRecord 获取用户上一次操作纪录]
     * @param [type] $data [数组，包含坐标信息，发送的信息类别]
     */
    public function setRecord($data){
        $this->last_record = $data;
    }

    /**
     * [query 接受关键词，判断关键词进入对应api]
     * @param  [string] $q [关键词]
     * @param [array] [$_data] [额外参数]
     * @return [type]          [description]
     * 
     */
    public function query($q,$_data=array()){
        $q = trim($q);
        if(!$q){
            $this->rtn_arr['msg'] = $this->err_msg;
            return $this->pushArr($this->rtn_arr,$rtn_type);
        }

    	//翻译
        $prep_trans = '/^翻译/';
        $prep_kuaiDi = '/^快递/';
        $prep_currency = '/^汇率/';
        $prep_weather = '/^天气/';
        $prep_dream = '/^解梦/';
        $prep_astro = '/^星座/';
        $prep_area = '/^周边|附近/';
        $prep_mobile = '/^手机|归属地/'; 
        $prep_joke = '/^笑话|冷笑话/';
        $prep_tool_lottery = '/^大转盘/'; 
        $prep_tool_scratchcard = '/^刮刮乐/'; 
		$prep_character = '/^人品/';
        $rtn_type = $this->rtn_type;

        switch(1){
            case preg_match($prep_trans,$q):
                $q  =   trim(preg_replace($prep_trans, "", $q));
                $rst = $this->rtnTrans($q,$rtn_type);
            break;
            case preg_match($prep_kuaiDi,$q):
                $q  =   trim(preg_replace($prep_kuaiDi, "", $q));
                $rst = $this->rtnKuaiDi($q,$rtn_type);
            break;
            case preg_match($prep_currency,$q):
                $q  =   trim(preg_replace($prep_currency, "", $q));
                $rst = $this->rtnCurrency($q,$rtn_type);
            break;
            case preg_match($prep_weather,$q):
                $q  =   trim(preg_replace($prep_weather, "", $q));
                $rst = $this->rtnWeater($q,$rtn_type);
            break;
            case preg_match($prep_dream,$q):
                $q  =   trim(preg_replace($prep_dream, "", $q));
                $rst = $this->rtnDream($q,$rtn_type);
            break;
            case preg_match($prep_astro,$q):
                $q  =   trim(preg_replace($prep_astro, "", $q));
                $rst = $this->rtnAstro($q,$rtn_type);
            break;
            case preg_match($prep_mobile,$q):
                $q  =   trim(preg_replace($prep_mobile, "", $q));
                $rst = $this->rtnMobile($q,$rtn_type);
            break;
            case preg_match($prep_joke,$q):
                $q  =   trim(preg_replace($prep_joke, "", $q));
                $rst = $this->rtnJoke($q,$rtn_type);
                break;
			case preg_match($prep_character,$q):
				//人品
                $q  =   trim(preg_replace($prep_character, "", $q));
                $rst = $this->rtnCharacter($q,$rtn_type);
                break;
            case preg_match($prep_tool_lottery,$q):
                //大转盘
                $rst = $this->rtnLottery('1');
                break;
            case preg_match($prep_tool_scratchcard,$q):
                //刮刮乐
                $rst = $this->rtnLottery('2');
                break;
            case preg_match($prep_area,$q):
            //发送地理位置信息，然后输入“附近xxx” 
                $q  =   trim(preg_replace($prep_area, "", $q));
                //查询上一次的地理位置
                $_where = $_order = array();
                $_where['open_id'] = (string)$_data['fromUsername'];
   
                $_where['k_type'] = 'location';
                $_order = array('id' => 'DESC');
                $_mmM = D('MemberMessages');
                $this->last_record = $_mmM->getRowInfo($_where, $_order);
                //获取api信息
                if($this->last_record['keywords']||$this->last_record['q'])
                {
                    $lat_lng = $this->last_record['keywords']
                        ?$this->last_record['keywords']
                        :$this->last_record['q'];
                }else{
                    $this->rtn_arr['msg'] = "请输入您要查询的位置";
                    return $this->rtn_arr;
                }
                $data['lat_lng'] = $lat_lng;
                $data['q'] = $q;
                $rst = $this->rtnAb($data,$rtn_type);
            break;
            default:
                $this->rtn_arr['msg'] = $this->err_msg;
                return $this->pushArr($this->rtn_arr);
        }
        
        return $this->pushArr($rst);

    }
    /**
     * [rtnTrans description]
     * @param  [type] $data [description]
     * @return [type]       [http://fanyi.youdao.com/openapi?path=data-mode]
     * API key：1178365006 keyfrom：itokit创建时间：2013-09-26网站名称：itokit网站地址：http://www.itokit.com
     * http://fanyi.youdao.com/openapi.do?keyfrom=itokit&key=1178365006&type=data&doctype=xml或json或jsonp&version=1.1&q=要翻译的文本
     */
    public function rtnTrans($q,$rtn_type='string'){
    	$url = "http://fanyi.youdao.com/openapi.do?keyfrom=itokit&key=1178365006&type=data";
    	$url .= "&doctype=json&version=1.1&q=".urlencode($q);

    	$rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $str = $q."\n";
            $basic = $rst['basic'];
            if($basic['phonetic'])
            $str .= "拼音:".$basic['phonetic']."\n";
            if(is_array($rst['translation'])){
                $str .= "翻译:".$rst['translation'][0]."\n";
            }else if($rst['translation']){
                $str .= "翻译:".$rst['translation']."\n";
            }
            if($basic['explains']){
                foreach($basic['explains'] as $k=>$v){
                    $str .= $v."\n";
                }
            }
            $rst = $str;
        }
    	return $rst;
    }
    /**
     * [kuaiDi 快递]
     * http://www.aikuaidi.cn/rest/?key=参数1&order=参数2&id=参数3&ord=参数4&show=参数5
     * @return [type] [description]
     */
    public function rtnKuaiDi($q,$rtn_type='string'){
        $_url = 'http://api.open.baidu.com/pae/channel/data/asyncqury?appid=4001&nu=' . $q;
        $c = curl($_url);
        $c_arr = json_decode($c, true);
        $_string = '';
        if($c_arr['data']['info']['status'] == 1) {
            $_string .= $c_arr['data']['company']['fullname'];
            $_string .= "\n";
            if(!empty($c_arr['data']['notice'])) {
                $_string .= $c_arr['data']['notice'];
                $_string .= "\n";
            }
            foreach($c_arr['data']['info']['context'] as $k => $v) {
                $_string .= date('Y-m-d H:i:s', $v['time']);
                $_string .= "\n";
                $_string .= $v['desc'];
                $_string .= "\n";
            }
        } else {
            $_string .= '查询不到此快递单号';
        }
        
        return $_string;
    }
    /**
     * [currency 货币兑换]
     * @param  [type] $q [关键字 美元,人民币]
     * http://api.uihoo.com/currency/currency.http.php?from=当前货币&to=兑换货币&format=数据格式
     * @return [type]       [description]
     */
    public function rtnCurrency($q,$rtn_type='string'){
        $dollar = array(
                //CNY (人民币 Chinese Yuan Renminbi)
                "CNY" => "人民币",
                //HKD (港元 Hong Kong Dollar)
                "HKD" => "港元",
                //TWD (台币 Taiwan Dollar)
                "TWD" => "台币",
                //EUR (欧元 Euro)
                "EUR" => "欧元",
                //USD (美元 US Dollar)
                "USD" => "美元",
                //GBP (英镑 British Pound)
                "GBP" => "英镑",
                //AUD (澳元 Australian Dollar)
                "AUD" => "澳元",
                //KRW (韩元 South-Korean Won)
                "KRW" => "韩元",
                //JPY (日元 Japanese Yen)
                "JPY" => "日元",
            );
        $_reg = "/(,|，|-)/";
        $q = trim(preg_replace($_reg, "-", $q));
        $_arr = explode('-', $q);

        $_from = $_arr[0];
        $from = $this->arrReg2Key($dollar,$_from);
        $_to = $_arr[1]?$_arr[1]:'人民币';
        $to = $this->arrReg2Key($dollar,$_to);
        $url = "http://api.uihoo.com/currency/currency.http.php?format=json";
        $url .= "&from=".urlencode($from);
        $url .= "&to=".urlencode($to);

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $str = $_from.'兑换'.$_to."\n";
            if($rst['date']){
                $str .= "更新日期:". $rst['date']."\n";
            }
            if($rst['time']){
                $str .= "更新时间:". $rst['time']."\n";
            }
            if($rst['now']){
                $str .= "利率:". $rst['now']."\n";
            }
            if($rst['buy']){
                $str .= "买入:". $rst['buy']."\n";
            }
            if($rst['sale']){
                $str .= "卖出:". $rst['sale']."\n";
            }
            $rst = $str;
        }
        return $rst;
    }
    /**
     * [rtnWeater description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * http://api.uihoo.com/weather/weather.http.php?key=深圳&format=数据格式
     * http://api.uihoo.com/demo/weather_city.shtml#body_top
     * USERID:b4b6e94b0cd6e9092613fbf2170a9c03d41c5030
     */
    public function rtnWeater($q = "",$rtn_type='string'){
        $city = $q;
        if(!$city){
            $city = $this->ip2city();
        }
        $url = "http://api.uihoo.com/weather/weather.http.php?format=json";
        $url .= "&key=".urlencode($city);

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $str = $q."天气\n";
            if($rst['now']){
                if($rst['now']['temp'])$str .= "温度".$rst['now']['temp']."\n";
                if($rst['now']['WD'])$str .= "风向".$rst['now']['WD']."\n";
                if($rst['now']['WS'])$str .= $rst['now']['WS']."\n";
                if($rst['now']['SD'])$str .= "湿度".$rst['now']['SD']."\n";
                if($rst['now']['WSE'])$str .= "风速".$rst['now']['WSE']."\n";
            }
            $rst = $str;
        }
        return $rst;
    }
    /**
     * [dream 周公解梦]
     * 注意事项: 关键字只能是中文
     * http://api.uihoo.com/dream/dream.http.php?key=梦境关键词&format=数据格式
     * @param  [type] $q [description]
     * @return [type]    [description]
     */
    public function rtnDream($q,$rtn_type='string'){
        $url = "http://api.uihoo.com/dream/dream.http.php?format=json";
        $url .= "&key=".urlencode($q);

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $str = "梦见".$q."\n";
            if(sizeof($rst)){
                $_max = sizeof($rst)>10?10:sizeof($rst);
                for($i=0;$i<$_max;$i++){
                    $str .= $rst[$i]."\n";
                }
            }
            $rst = $str;
        }
        return $rst;
    }
    /**
     * [rtnAstro description]
     * http://api.uihoo.com/astro/astro.http.php?fun=day&id=星座ID&format=数据格式
     * fun         函数类型(day,tomorrow,week,month,year,love)  
     * id          星座编号(必填)
     * 0:白羊座、1:金牛座、2:双子座、3:巨蟹座、4:狮子座、 5:处女座、6:天秤座、7:天蝎座、8:射手座、9:魔羯座、10:水瓶座、11:双鱼座
     * @param  [type] $data [array("fun","id")]
     * @return [type]       [description]
     */
    public function rtnAstro($q,$rtn_type='string'){
        $_key = $q;
        $fun_arr = array(
            "day"=>"今天","tomrrow"=>"明天",
            "week"=>"本周","month"=>"本月",
            "year"=>"今年","love"=>"爱情"
            );
        $id_arr = array(
            "白羊","金牛","双子","巨蟹",
            "狮子","处女","天秤","天蝎",
            "射手","魔羯","水瓶","双鱼");
        $fun = $this->arrReg2Key($fun_arr,$q);
        if($fun)
        {
            $_reg = '/^'.$fun_arr[$fun].'/'; 
            $q = trim(preg_replace($_reg, "", $q));
        }
        $data['id'] = $this->arrReg2Key($id_arr,$q);
        $fun = $fun?$fun:"day";
        $id = $data['id'];

        $url = "http://api.uihoo.com/astro/astro.http.php?format=json";
        $url .= "&fun=".$fun."&id=".$id;

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $_time = array_pop($rst);
            if(is_array($_time)){
                $str = $_key."运势"."\n".$_time[0]."-".$_time[1]."\n";
            }else{
                $str = $_key."运势"."\n".$_time."\n";
            }
            foreach($rst as $k=>$v){
                if($v['title']){
                    $str .= $v['title']."\n";
                }
                if($v['rank']){
                    $str .= "星级:".$v['rank']."\n";
                }
                if($v['value']){
                    $str .= $v['value']."\n";
                }
            }
            $rst = $str;
        }
        return $rst;
    }
    /**
     * [rtnMobile description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * http://api.uihoo.com/mobile/mobile.http.php?mobile=13612907536&format=json
     */
    public function rtnMobile($q,$rtn_type='string'){
        $mobile = $q;

        $url = "http://api.uihoo.com/mobile/mobile.http.php?format=json";
        $url .= "&mobile=".$mobile;

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        if($rtn_type == "string"){
            $str = $mobile."归属地查询"."\n";
            $str .= $rst['city']?$rst['city']."\n":"";
            $str .= $rst['type']?$rst['type']."\n":"";
            $rst = $str;
        }
        return $rst;
    }
    
    /**
     * [rtnJoke 活动营销工具-大转盘、刮刮乐]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * 
     */
    public function rtnLottery($type){ 
        $_hM = D('Huodong');
        $_where = $_order = array();
        $_id = $_GET['id'];
        $_where['uid'] = $_id;
        $_where['tid_type'] = $type;
        $_order = array('tid' => 'DESC');
        $_res = $_hM->getPagesize($_where, 9, $_order);
        $_return = array();
        $_return['count'] = $_res['count'];
        if(is_array($_res['lists'])) {
            $_return['type'] = 2;
            foreach($_res['lists'] as $k => $v) {
                //图文内容
                $_return['picArr'][$k]['Title'] = $v['title'];
                $_return['picArr'][$k]['content_search'] = $v['contents'];
                if($type == 1) {
                    $_return['picArr'][$k]['pic_url'] = C('WWW_DOMAIN') . 'Public/Images/tool/start1.jpg';
                    $_return['picArr'][$k]['url'] = C('WWW_DOMAIN') . 'show/Lottery/index/id/'.$_id.'/tid/'.$v['tid'].'.html';
                } else if($type == 2) {
                    $_return['picArr'][$k]['pic_url'] = C('WWW_DOMAIN') . 'Public/Images/tool/start2.jpg';
                    $_return['picArr'][$k]['url'] = C('WWW_DOMAIN') . 'show/Scratchcard/index/id/'.$_id.'/tid/'.$v['tid'].'.html';
                }
                
                
                $_where = array();
                $_where['tid'] = $v['tid'];
                $_r = $_hM->getRowInfoExt($_where);
                if($_r) {
                    $_res['lists'][$k] = array_merge($_res['lists'][$k], $_r);
                }
            }
        } else {
            $_return = '暂时没有相关活动';
        }
        return $_return;
    }
    
    /**
     * [rtnJoke 笑话和冷笑话的内容]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * 
     */
    public function rtnJoke($q,$rtn_type='string'){
        $_r = curl('http://www.itokit.com/hgxo_json.php?q=' . $keywords);
        $_rr = '';
        if($q && empty($_r)) {
            //查找关键词未找到笑话时，返回随机的一条笑话
            $_rr = $_hgxoM->getRowInfoRand();
            $_rr = "未找到相关笑话，我们为您推荐一条:\n" . $_rr;
        }
        if($rtn_type == "string"){
            $str = empty($_rr) ? $q." 笑话:"."\n" : $_rr;
            $rst = $str . $_r;
            
            $_tmp_arr = array('&nbsp;', '　　');
            $rst = str_replace($_tmp_arr, '', $rst);
            $rst  =   trim(preg_replace("/\r\n/", "", $rst));
            $rst  =   trim(preg_replace("/\n\n/", "", $rst));
            $rst  =   html_entity_decode(strip_tags($rst));
        }
        return $rst;
    }
	
	/**
     * [rtnJoke 人品计算器]
     * @param  [type] $data [description]
     * @return [type]       [description]
     * 
     */
	public function rtnCharacter($q, $rtn_type='string'){
		$name = $q;
		$name = str_replace("+", "", $name);
		$f = mb_substr($name,0,1,'utf-8');
		$s = mb_substr($name,1,1,'utf-8');
		$w = mb_substr($name,2,1,'utf-8');
		$x = mb_substr($name,3,1,'utf-8');
		$n=($this->getUnicodeFromUTF8($f) 
			+ $this->getUnicodeFromUTF8($s) 
			+ $this->getUnicodeFromUTF8($w) 
			+ $this->getUnicodeFromUTF8($x)) % 100;
		$addd='';
		if(empty($name))
		{
			$addd="大哥不要玩我啊，名字都没有你想算什么！";

		} else if ($n <= 0) {
			$addd ="你一定不是人吧？怎么一点人品都没有？！";
		} else if($n > 0 && $n <= 5) {
			$addd ="算了，跟你没什么人品好谈的...";
		} else if($n > 5 && $n <= 10) {
			$addd ="是我不好...不应该跟你谈人品问题的...";
		} else if($n > 10 && $n <= 15) {
			$addd ="杀过人没有?放过火没有?你应该无恶不做吧?";
		} else if($n > 15 && $n <= 20) {
			$addd ="你貌似应该三岁就偷看隔壁大妈洗澡的吧..."; 
		} else if($n > 20 && $n <= 25) {
			$addd ="你的人品之低下实在让人惊讶啊..."; 
		} else if($n > 25 && $n <= 30) {
			$addd ="你的人品太差了。你应该有干坏事的嗜好吧?";
		} else if($n > 30 && $n <= 35) {
			$addd ="你的人品真差!肯定经常做偷鸡摸狗的事...";
		} else if($n > 35 && $n <= 40) {
			$addd ="你拥有如此差的人品请经常祈求佛祖保佑你吧...";
		} else if($n > 40 && $n <= 45) {
			$addd ="老实交待..那些论坛上面经常出现的偷拍照是不是你的杰作?"; 
		} else if($n > 45 && $n <= 50) {
			$addd ="你随地大小便之类的事没少干吧?";
		} else if($n > 50 && $n <= 55) {
			$addd ="你的人品太差了..稍不小心就会去干坏事了吧?"; 
		} else if($n > 55 && $n <= 60) {
			$addd ="你的人品很差了..要时刻克制住做坏事的冲动哦.."; 
		} else if($n > 60 && $n <= 65) {
			$addd ="你的人品比较差了..要好好的约束自己啊.."; 
		} else if($n > 65 && $n <= 70) {
			$addd ="你的人品勉勉强强..要自己好自为之.."; 
		} else if($n > 70 && $n <= 75) {
			$addd ="有你这样的人品算是不错了..";
		} else if($n > 75 && $n <= 80) {
			$addd ="你有较好的人品..继续保持.."; 
		} else if($n > 80 && $n <= 85) {
			$addd ="你的人品不错..应该一表人才吧?";
		} else if($n > 85 && $n <= 90) {
			$addd ="你的人品真好..做好事应该是你的爱好吧.."; 
		} else if($n > 90 && $n <= 95) {
			$addd ="你的人品太好了..你就是当代活雷锋啊...";
		} else if($n > 95 && $n <= 99) {
			$addd ="你是世人的榜样！";
		} else if($n > 100 && $n < 105) {
			$addd ="天啦！你不是人！你是神！！！"; 
		}else if($n > 105 && $n < 999) {
			$addd="你的人品已经过 100 人品计算器已经甘愿认输，3秒后人品计算器将自杀啊";
		} else if($n > 999) {
			$addd ="你的人品竟然负溢出了...我对你无语.."; 
		}
		return $name."的人品分数为：".$n."\n".$addd;
		return $rst;
	}

    //爱帮sdk地址
    public function setAbPath($path){
        $this->ab_path = $path;
    }

    public function getAbPath(){
        $this->ab_path =  $this->ab_path?$this->ab_path:dirname(__FILE__)."/ABClient.php";
    }
    /**
     * [geocoder description]
     * @param  [type] $lat_lng [description]
     * @param  [type] $rtn_key [description]
     * @return [array or string]          [返回数组或具体的值]
     * http://developer.baidu.com/map/webservice-geocoding.htm
     */
    public function geocoder($lat_lng,$rtn_key=0){
        $url = "http://api.map.baidu.com/geocoder/v2/?ak=".$this->baidu_key;
        $url .= "&location=".$lat_lng."&output=json&pois=0";

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);
        $rtn = $rst;
        if($rtn_key){
            if($rst[$rtn_key]){
                $rtn = $rst[$rtn_key];
            }else if($rst['result'][$rtn_key]){
                $rtn = $rst['result'][$rtn_key];
            }else if($rst['result']['addressComponent'][$rtn_key]){
                $rtn = $rst['result']['addressComponent'][$rtn_key];
            }
        }
        return $rtn;
    }
    //返回当前用户地址
    public function ip2city($ip=""){
        $url = "http://api.map.baidu.com/location/ip?coor=bd09ll&ak=".$this->baidu_key;
        $url .= $ip?"&ip=".$ip:"";

        $rst = $this->_proxy($url);
        $rst = json_decode($rst,true);

        $rtn = $rst['content']['address_detail']['city'];
        return $rtn;
    }
    /**
     * [rtnAb description]
     * http://www.aibang.com/api/usage#search 爱帮周边搜索
     * KEY:bb6c4d3995afeba4dd5bc93da5efb2c5 私钥:0b0d435a56262d30
     * @param  [type] $data [数组]
     * @return [type]       [description]
     * list($code, $arr) = $client->search("北京", array("a" => "西单", "q" => "餐馆", "cate" => "美食"));
     * list($code, $arr) = $client->search("北京", array("lng" => 116.337, "lat" => 39.993, "q" => "小吃"));
     */
    public function rtnAb($data,$rtn_type='string'){
        $ab_key = $this->ab_key;
        $this->getAbPath();
        if($data['lat_lng']){
            $city = $this->geocoder($data['lat_lng'],"city");
            $_tempArr = explode(',', $data['lat_lng']);
            $data['lat'] = $_tempArr[0];
            $data['lng'] = $_tempArr[1];
        }
        if(!isset($city)){
            $city = $this->ip2city();//根据ip获取城市
        }

        include_once $this->ab_path;
        $client = new ABClient($ab_key);
        //array("a"=>"车公庙", "q" => "小吃")
        $data['rc'] = (int)2;

        list($code, $arr) = $client->search($city, $data);
        $rst = $arr;
        if($rtn_type == "string"){
            $str = "周边查询".$data['q']."\n";
            $_size = sizeof($rst['bizs']['biz']);
            if($_size){
                /**
                 * [id] => 983207351-703173074
                    [name] => 富春江
                    [addr] => 朝阳区亚运村安慧里二区3号楼
                    [tel] => 4000065128-1338
                    [cate] => 美食;江浙菜;浙江菜;
                    [rate] => 0
                    [cost] => 0
                    [desc] => “富春江”三字让我眼前浮现出“水送山迎入富春，一川如画晚晴新”的景象。夏日里，长江周边的城市...
                    [dist] => -1
                    [lng] => 116.4116048
                    [lat] => 39.99467900
                    [img_url] => http://img0.aibangjuxin.com/ipic/f626c715bf52625c_8.jpg
                 */
                $biz = $rst['bizs']['biz'];
                foreach($biz as $k=>$v){
                    $str .= $v['name']?$v['name']."\n":"";
                    $str .= $v['addr']?'地址:'.$v['addr']."\n":"";
                    $str .= $v['tel']?'电话:'.$v['tel']."\n":"";
                    $str .= $v['cost']?'人均:'.$v['cost']."\n":"";
                    $str .= $v['desc']?$v['desc']."\n":"";
                    $str .= "\n";
                }
            }else{
                $str = "抱歉，暂时没有您要找的资料";
            }
            $rst = $str;
        }
        return $rst;
    }


    /**
     * [pushArr 返回数组]
     * @param  [array]  $arr   [待返回数组]
     * @param  integer $eFlog [是否exit]
     * @return [array]         [array]
     */
    private function pushArr($arr,$type="array"){
        if($type == "string"){
            return $arr['msg'];
        }
        return $arr;
    }
    //test
    private function pushPre($arr){
        print("<pre />");
        print_r($arr);
    }
    //
    public function arrReg2Key($arr,$q){
        foreach($arr as $k=>$v){
            $_reg = '/^'.$v.'/'; 
            if(preg_match($_reg,$q)){
                return $k;
            }
        }
    }

    /**
     * [_proxy description]
     * @param  [type] $_url [api 地址]
     * @param  array  $data [参数]
     * @param  string $act  [方法]
     * @return [type]       [description]
     * $_SERVER['REQUEST_METHOD']
     */
	private function _proxy($url,$data=array(),$act="GET"){

		$ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER,false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if ($act == "POST"){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $rst = curl_exec($ch); 

        curl_close($ch);

        return $rst;
	}   
    
    /**
     * 判断是否是工具
     * 
     * @param string $keywords 关键词
     * @param int $uid 哪个用户的接口uid
     */
    function isTool($keywords, $uid) {
        $_return = array('status'=>'', 'msg'=>'');        
        $_stM = D('SystemTool');
        $_mtM = D('MemberTools');
        $_where = array();
        //所有工具
        $_where['is_publish'] = 1;
        $_resToolAll = $_stM->getAllPagesize($_where);
        //此用户的工具
        //$_where['uid'] = $uid;
        //$_resTool = $_mtM->join('RIGHT JOIN weixin_system_tool st ON st.tid = weixin_member_tools.tid')->getAllPagesize($_where);
        if(is_array($_resToolAll['lists'])) {
            foreach($_resToolAll['lists'] as $k => $v) {
                $_paramatch = '/^' . $v['nick_name'] . '/';
                $_paramatch1 = '/^' . $v['tool_name'] . '/';
                $_where = array();
                $_where['uid'] = $uid;
                $_where['tid'] = $v['tid'];
                $_r = $_resTool = $_mtM->getRowInfo($_where);
                
                if(preg_match($_paramatch, $keywords) || preg_match($_paramatch1, $keywords)) {
                    //工具使用
                    $_return['status'] = 'use';                    
                    
                    if(!$_r) {
                        $_return['status'] = 'help';
                        $_return['msg'] = '此用户还未开启此工具的相关功能。';
                    }
                    break;
                }
                if($keywords == $v['tool_int']) {
                    if(!$_r) {
                        break;
                    }
                    //工具帮助
                    $_return['status'] = 'help';
                    $_return['msg'] = $v['tool_help'];
                    break;
                }
            }
        }
        return $_return;
    }
	
	private function getUnicodeFromUTF8($word) {   
		//获取其字符的内部数组表示，所以本文件应用utf-8编码！   
		if (is_array( $word))   
			$arr = $word;   
		else     
			$arr = str_split($word);   
		//此时，$arr应类似array(228, 189, 160)   
		//定义一个空字符串存储   
		$bin_str = '';   
		//转成数字再转成二进制字符串，最后联合起来。   
		foreach ($arr as $value)   
			$bin_str .= decbin(ord($value));   
		//此时，$bin_str应类似111001001011110110100000,如果是汉字"你"   
		//正则截取   
		$bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/','$1$2$3', $bin_str);   
		//此时， $bin_str应类似0100111101100000,如果是汉字"你"   
		return bindec($bin_str); //返回类似20320， 汉字"你"   
		//return dechex(bindec($bin_str)); //如想返回十六进制4f60，用这句   
	}


}
