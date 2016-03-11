<?php

//爱帮的http请求类，用于和服务器通信
class ABHttp {
    const ENCRYPT_URLENCODE = 0;
    const ENCRYPT_MULTIPART = 1;
    const METHOD_GET = 0;
    const METHOD_POST = 1;

    /**
     * 发送get请求
     * @param string $url 请求url
     * @param array $params 请求附带的参数数组
     * @return array $ret 结构：array($code, $arr), $code为http_code的值，$arr为返回的数组对象
     */
    public static function get($url, $params = array()) {
        if ($params) {
            if (strpos($url, "?") !== false) {
                $url .= "&".self::encodeParams($params);
            } else {
                $url .= "?".self::encodeParams($params);
            }
        }
        list($code, $ret) = self::curl(self::METHOD_GET, $url);
        $arr = json_decode($ret, true);
        return array($code, $arr);
    }

    /**
     * 发送post请求
     * @param string $url 请求url
     * @param array $params 请求附带的参数数组
     * @param int $encrypt 普通的post请求或者是multipart/form-data（主要用于上传二进制数据，比如图片）
     * @return array $ret array($code, $arr), $code为http_code的值，$arr为返回的数组对象
     */
    public static function post($url, $params = array(), $encrypt = self::ENCRYPT_URLENCODE) {
        $fields = $params;
        if ($encrypt == self::ENCRYPT_URLENCODE) {
            $fields = self::encodeParams($params);
        }
        list($code, $ret) = self::curl(self::METHOD_POST, $url, $fields);
        $arr = json_decode($ret, true);
        return array($code, $arr);
    }

    //对array进行urlencode
    private static function encodeParams($params) {
        if (!$params) {
            return "";
        }
        $arr = array();
        foreach ($params as $k => $v) {
            $arr[] =urlencode($k)."=".urlencode($v);
        }
        return implode("&", $arr);
    }

    /**
     * 调用curl获取实际的post或者get请求
     * @param int $method 请求方法，get/post
     * @param string $url 请求url
     * @param mixed $fields 如果是post并且Content-Type是multipart/form-data，则为array, 否则为urlencode编码后的字符串（和curl保持一致）; get请求时为空
     */
    private static function curl($method, $url, $fields = NULL) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method == self::METHOD_POST && $fields) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        $ret = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array($info['http_code'], $ret);
    }
}


/***
 * 客户端调用接口类
 * 调用方式说明：
 * $appKey = "xxxxxxxx";
 * $client = new ABClient($appKey);
 * list($httpCode, $arr) = $client->methd(...)
 * 结果说明：
 * $httpCode: http状态码:200,400,401,404,500
 * $arr: 返回的数据，php数组格式，对于非200的状态码，该数组的message字段包含错误消息；对于200的状态码，则返回标准格式的结果
 * 注意：关于调用方法相关参数，请参考文档说明，方法只是列出了必要参数。
 */
class ABClient {
    const SERVER_ADDR = "http://openapi.aibang.com";
    const ALT = "json"; //输出参数，固定为json
    private $appKey = ""; //申请的爱帮key

    public function __construct($appKey) {
        $this->appKey = $appKey;
    }

    //发送get请求
    private function get($url, $params=false) {
        $params['app_key'] = $this->appKey;
        $params['alt'] = self::ALT;
        return ABHttp::get($url, $params);
    }

    //发送post请求
    private function post($url, $params) {
        $params['app_key'] = $this->appKey;
        $params['alt'] = self::ALT;
        return ABHttp::post($url, $params);
    }

    //发送multipart/form-data格式的post请求
    private function postMP($url, $params) {
        $params['app_key'] = $this->appKey;
        $params['alt'] = self::ALT;
        return ABHttp::post($url, $params, ABHttp::ENCRYPT_MULTIPART);
    }

    /**
     * 搜索接口
     * @param string $city 城市
     * @param array $params 附加查询参数
     * @return array $ret
     */
    public function search($city, $params) {
        $url = self::SERVER_ADDR."/search";
        $params['city'] = $city;
        return $this->get($url, $params);
    }

    /**
     * 地址定位接口
     * @param string $city 城市
     * @param string $addr 地址
     * @return array $ret
     */
    public function getLocation($city, $addr) {
        $url = self::SERVER_ADDR."/locate";
        $params['city'] = $city;
        $params['addr'] = $addr;
        return $this->get($url, $params);
    }

    /**
     * 获取商户详情接口
     * @param string $id 商户id
     * @return array $ret
     */
    public function getBiz($id) {
        $url = self::SERVER_ADDR."/biz/{$id}";
        return $this->get($url);
    }

    /**
     * 获取商户点评接口
     * @param string $id 商户id
     * @return array $ret
     */
    public function getBizComments($id) {
        $url = self::SERVER_ADDR."/biz/{$id}/comments";
        return $this->get($url);
    }

    /**
     * 获取商户图片接口
     * @param string $id 商户id
     * @return array $ret
     */
    public function getBizPics($id) {
        $url = self::SERVER_ADDR."/biz/{$id}/pics";
        return $this->get($url);
    }

    /**
     * 公交换乘查询接口
     * @param string $city 城市
     * @param array $params 附加查询参数
     * @return array $ret
     */
    public function getBusTransfer($city, $params) {
        $url = self::SERVER_ADDR."/bus/transfer";
        $params['city'] = $city;
        return $this->get($url, $params);
    }

    /**
     * 公交线路查询接口
     * @param string $city 城市
     * @param string $q 线路名称
     * @param int $withXY 是否包含坐标经纬度
     * @return array $ret
     */
    public function getBusLines($city, $q, $withXY=0) {
        $url = self::SERVER_ADDR."/bus/lines";
        $params['city'] = $city;
        $params['q'] = $q;
        $params['with_xys'] = $withXY;
        return $this->get($url, $params);
    }

    /**
     * 公交站点查询接口
     * @param string $city 城市
     * @param string $q 站点名称
     * @return array $ret
     */
    public function getBusStats($city, $q) {
        $url = self::SERVER_ADDR."/bus/stats";
        $params['city'] = $city;
        $params['q'] = $q;
        return $this->get($url, $params);
    }

    /**
     * 周边公交站点查询接口
     * @param string $city 城市
     * @param string $lng 地点经度
     * @param string $lat 地点纬度
     * @param int $dist 限制距离
     * @return array $ret
     */
    public function getBusStatsXY($city, $lng, $lat, $dist) {
        $url = self::SERVER_ADDR."/bus/stats_xy";
        $params['city'] = $city;
        $params['lng'] = $lng;
        $params['lat'] = $lat;
        $params['dist'] = $dist;
        return $this->get($url, $params);
    }

    /**
     * 发表商户点评接口
     * @param string $uname 发表点评的用户名
     * @param string $id 商户id
     * @param string $content 点评内容
     * @param int $score 打分，[1,5]的整数
     * @param int $cost 人均消费（单位：元）
     * @return array $ret
     */
    public function postBizComment($uname, $id, $content, $score, $cost=-1) {
        $url = self::SERVER_ADDR."/biz/{$id}/comment";
        $params['uname'] = $uname;
        $params['id'] = $id;
        $params['content'] = $content;
        $params['score'] = $score;
        $params['cost'] = $cost;
        return $this->post($url, $params);
    }

    /**
     * 发表商户图片接口
     * @param string $uname 发表图片的用户名
     * @param string $id 商户id
     * @param string $picData 图片数据
     * @param string $title 图片标题
     * @return array $ret
     */
    public function postBizPic($uname, $id, $picData, $title='') {
        $url = self::SERVER_ADDR."/biz/{$id}/pic";
        $params['uname'] = $uname;
        $params['id'] = $id;
        $params['data'] = $picData;
        $params['title'] = $title;
        return $this->postMP($url, $params);
    }

    /**
     * 修改商户信息接口
     * @param string $uname 用户名
     * @param string $id 商户id
     * @param array $params 附加信息参数
     * @return array $ret
     */
    public function modifyBiz($uname, $id, $params) {
        $url = self::SERVER_ADDR."/biz/{$id}";
        $params['uname'] = $uname;
        $params['id'] = $id;
        return $this->post($url, $params);
    }

    /**
     * 添加新商户接口
     * @param string $uname 用户名
     * @param string $bizName 商户名称
     * @param string $city 商户所在城市
     * @param string $county 商户所在区县
     * @param string $tel 电话
     * @param string $cate 类别
     * @param array $params 附加参数列表
     * @return array $ret
     */
    public function addBiz($uname, $bizName, $city, $county, $tel, $cate, $params) {
        $url = self::SERVER_ADDR."/biz";
        $params['uname'] = $uname;
        $params['name'] = $bizName;
        $params['city'] = $city;
        $params['county'] = $county;
        $params['tel'] = $tel;
        $params['cate'] = $cate;
        return $this->post($url, $params);
    }

}

?>