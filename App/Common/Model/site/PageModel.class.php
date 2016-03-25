<?php

namespace Common\Model\site;

/**
 * 系统单页面
 */
class PageModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_page';

    public function pgKeys($_key = false){
        $_arr = array(
                "company" => array(
                        "key" => "company",
                        "title" => "公司介绍"
                    ),
                "help" => array(
                        "key" => "help",
                        "title" => "帮助中心"
                    ),
                "product" => array(
                        "key" => "product",
                        "title" => "产品介绍"
                    ),
                "contact" => array(
                        "key" => "contact",
                        "title" => "关于我们"
                    ),
            );
        return $_key?$_arr[$_key]:$_arr;

    }
    
    public function write($_data,$_where = array()){
        if($_data['pic_url']){
            $_data['pic_url'] = str_replace(C('DATA_UPLOADS'), '', $_data['pic_url']);
            $_data['pic_url'] = str_replace('//', '/', $_data['pic_url']);
        }
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }
    
    public function vRule(){
        return array(
            array('s_key','require','请选择类型！')
        );
    }
    
}