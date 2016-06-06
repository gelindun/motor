<?php
/**
 * D('site\SiteBase')->readBase()
 */
namespace Common\Model\site;

Class SiteBaseModel {
    public $_SiteBase = "site_base";
    public $_SiteSeo = "site_seo";
    public $_Subscribe = "subscribe_base";
    
    public function readBase(){
        $_arr = array();
        $_arr = F($this->_SiteBase, '', C('DATA_CACHE_PATH'));
        
        return $_arr;
    }
    
    public function writeBase($_data = array()){
        F($this->_SiteBase, $_data, C('DATA_CACHE_PATH'));
        return true;
    }
    //关注回复文本
    public function readSubscribe(){
        $_arr = array();
        $_arr = F($this->_Subscribe, '', C('DATA_CACHE_PATH'));
        
        return $_arr;
    }
    
    public function writeSubscribe($_data = array()){
        F($this->_Subscribe, $_data, C('DATA_CACHE_PATH'));
        return true;
    }
    
    public function readSeo(){
        
        
    }
}