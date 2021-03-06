<?php

namespace Common\Controller;
use Think\Controller;
/**
 * tp 百度编辑器提交的数据处理
 * 详细内容显示 stripslashes(htmlspecialchars_decode($v['content']));
 * 编辑器中显示 htmlspecialchars_decode($this->_arr['resWiki']['content'])
 */
class CommonController extends Controller {
    protected $_arr     = array(); //输出模板变量
    const ADMIN_UID = 'admin_uid';
    const FRONT_UID = 'front_uid';//前台会员
    const COOKIE_PRODUCT = 'ton_product';

    public function _initialize() {
        $this->_arr['ACT_NAME'] = ACTION_NAME;
        $this->_arr['CTR_NAME'] = CONTROLLER_NAME;
        $this->_arr['MODULE_NAME'] = MODULE_NAME;
        $this->_arr['map_baidu_key'] = '70c24e8f7953423f1c93588bbc0de511';
        $this->_arr['SITE_BASE'] = D('site\SiteBase')->readBase();
        $this->_arr['CLEAN_FORM'] = C("CLEAN_FORM");
        $this->_arr['CLEAN_PRO'] = D('product\Product')->clean_form();
        $this->_arr['s_domain'] = $_SERVER['SERVER_NAME'];

        $this->_arr['WX_BASE'] = C('WX_BASE');
        $_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if(strstr($_user_agent, 'micromessenger')) {
            $this->_arr['isWeixin'] = true;
            vendor('Weixin.jssdk');
            $jssdk = new \JSSDK($this->_arr['WX_BASE']['wx_appid'], $this->_arr['WX_BASE']['wx_appsecret']);
            $this->_arr['signPackage'] = $jssdk->GetSignPackage();
        }
        $this->_arr[self::FRONT_UID] = safeGetCookie(self::FRONT_UID);
        $this->_readSeo();
        // test start
        //$this->_arr['front_uid'] = 1;
        //  test end
        
    }
    
    /**
     *每个控制器里方法调取此方法渲染页面的方法 
     * @param string $string 模板文件名
     */
    protected function _showDisplay($string = '') {
        $this->assign($this->_arr);
        $this->display($string);
    }

    /**
     * 渲染局部模板，返回
     * @param type $string
     * @return type
     */
    protected function _fetch($string = '') {
        $this->assign($this->_arr);
        return $this->fetch($string);
    }
    /**
     * @param type $filter
     * @param type $arrThumb
     * @param type $errRtn
     * @return boolean
     */
    protected function _fileUpload($filter = array('jpg', 'gif', 'png', 'jpeg'),
        $arrThumb = array(), $errRtn = 0) {
        
        $do_action = I('post.do_action')?I('post.do_action'):I('get.do_action');
        
        if ($do_action == 'action.file_upload') {
            $_rtn = uploadFile('file', $filter, $arrThumb, $errRtn);
            if ($_rtn)
                return $_rtn;
        }
        return false;
        
    }
    
    
        /**
     * {"info":{"file":{"name":"13.pic_hd2.jpg","type":"image\/jpeg",
     * "size":51610,"key":"file","ext":"jpg",
     * "md5":"94044d6db0e72d847aeecad092bdd07a",
     * "sha1":"e41c52f035ef81cde6be09cf3152a4ba5d64c0c3",
     * "savename":"1437393791894.jpg","savepath":"2015\/0720\/2020\/"}},
     * "savename":"2015\/0720\/2020\/1437393791894.jpg",
     * "small":"2015\/0720\/2020\/1437393791894_small.jpg"}
     */
    protected function upload($_ext = "",$_is_img = 0){
        
        $filter = array('jpg', 'gif', 'png', 'jpeg');
        $filter = $_ext?$_ext:$filter;
        $arrThumb = $_is_img?array(
            "small" => array('width'=>200,'height'=>200)
        ):false;
        $_info = $this->_fileUpload($filter,$arrThumb,1);
    
        if(!is_array($_info)){
            $custom_error['jquery-upload-file-error']   =   $_info;
            echo json_encode($custom_error);exit;
        }
        return $_info;
    }

    /**
     * 读取各模块及方法，然后分别设置SEO信息；
     */
    private function _readSeo() {
        $_file_name = C('DATA_CACHE_PATH')."site_seo.php";
        $_www_seo = require_once $_file_name;
        $_seo_name = strtoupper(MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME);
        if(!$_www_seo[$_seo_name]['title']) {
            $_seo_name = 'HOME_EMPTY_INDEX';
        }
        $p = (int)I('get.p');
        if($p > 1) {
            $_www_seo[$_seo_name]['title'] = $_www_seo[$_seo_name]['title'] . '_第' . $p . '页';
        }
        $this->_arr['seo_title'] = $_www_seo[$_seo_name]['title'];
        $this->_arr['seo_keywords'] = $_www_seo[$_seo_name]['seo_keywords'];
        $this->_arr['seo_description'] = $_www_seo[$_seo_name]['seo_description'];
    }
    
    /**
     * 返回判断前台用户
     * @param type $_return
     * @param type $from_url
     * @return type
     */
    protected function chkLogin($_return = false,$from_url="") {
        if(!$from_url)$from_url = urlencode("http://".$this->_arr['s_domain'] . $_SERVER['REQUEST_URI']);
        else{
            if(!strpos($from_url,C('COOKIE_DOMAIN'))){
                $from_url = "http://".$this->_arr['s_domain'] . $from_url;
            }
            $from_url = urlencode(urldecode($from_url));
        }
            
        if(empty($this->_arr['front_uid'])) {
            if(!$_return){
                $this->redirect('/My/login/' ,array(
                        'jump_url' => $from_url
                    ));
                exit;
            }else{
                return array(
                    "status" => 0,
                    "url" => U('/My/login/',array(
                        'jump_url' => $from_url
                    ))
                );
            }
        }
    }
    
   
}
