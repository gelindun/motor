<?php

namespace Home\Controller;
use Think\Controller;

class ArticleController extends HomeController {
    private $D_AdminArticle;
    private $D_AdminArticleType;

    public function _initialize() {
        parent::_initialize();
        $this->_arr['title'] = "新闻资讯";
        
    }
    
    public function index(){
        
        
        $this->_showDisplay('article:index');
    }
    
    public function detail(){
        
        $this->_showDisplay('article:detail');
    }
}
