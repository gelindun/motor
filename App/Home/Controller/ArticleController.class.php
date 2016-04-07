<?php

namespace Home\Controller;
use Think\Controller;

class ArticleController extends HomeController {
    private $D_AdminArticle;
    private $D_AdminArticleType;

    public function _initialize() {
        parent::_initialize();
        $this->D_AdminArticle =D('site\AdminArticle');
        $this->D_AdminArticleType =D('site\AdminArticleType');
        $_type = I('get.tid');
        $_order_type = array(
            "order_id" => "DESC",
            "id" => "DESC"
        );
        $_where_type = array();
        $_where_type['pid'] = $_type?$_type:0;
        $_resTypeList = $this->D_AdminArticleType->where($_where_type)->order($_order_type)->select();
        $this->_arr['resTypeList'] = $_resTypeList;
    }
    
    public function index(){
        $_type = I('get.tid');
        $_where = array();
        if($_type){
            $_where['tid'] = $_type;
        }
        $_key_word = $this->_arr['key_word'] = I('get.key');
        $_where = array();
        $_order = array(
            'id' => "DESC"
        );
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array(
            'time_show' => "DESC"
        );
        $_resList = $this->D_AdminArticle->rtnList($_where,10,$_order);
        
        foreach($_resList['lists'] as $k=>$v){
            $_resList['lists'][$k]['content'] = htmlspecialchars_decode($v['content']);
            $_resList['lists'][$k]['content'] = strip_tags($_resList['lists'][$k]['content']);
            $_where_tp = array(
                "id" => $v['tid']
            );
            $_resList['lists'][$k]['type_name'] = $this->D_AdminArticleType->where($_where_tp)->getField('title');
        }
        $this->_arr['resList'] = $_resList;
        $this->_arr['s_type'] = $_type;
        $this->_arr['keyWord'] = $_key_word;
        $this->_showDisplay('Article:index');
    }
    
    public function detail(){
        $_id = (int)I('get.id');
        
        $_where = array(
            'id' => $_id
        );
        $_rst = $this->D_AdminArticle->where($_where)->find();
        if(!$_rst){
            $this->redirect('/');
        }
        $_rst['content'] = htmlspecialchars_decode($_rst['content']);
        $this->_arr['resPage'] = $_rst;
        $this->D_AdminArticle->where($_where)->setInc('view_count');
        
        $this->_arr['seo_keywords'] = $_rst['title'];
        $this->_arr['seo_description'] = msubstr(strip_tags($_rst['content']),0,100);
        $this->_showDisplay('Article:detail');
    }
}
