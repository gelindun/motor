<?php

namespace Admin\Controller;
use Think\Controller;
//s_type 1:wiki,2:更新记录,3:大事记,4:公告
class WebsiteController extends AdminController {
    private $note_rules ;
    private $case_rules ;
    private $article_rules ;
    
    private $_s_type = array(
        "1" => array("Wiki","wiki"),
        "2" => array("更新记录","history"),
        "3" => array("大事记","events"),
        "4" => array("公告","notice"),
        "5" => array("解决方案","solution")
    );

    public function _initialize() {
         parent::_initialize();
         
    }
    
    public function site_base(){
        
        if(I('post.action') === 'edit_siteBase'){
            $_data = I('post.');
            unset($_data['action']);
            $D_SiteBase = D('site\SiteBase');
            if($D_SiteBase->writeBase($_data)){
                pushJson('更新成功');
            }else{
                pushError ('更新失败');
            }
        }
        $this->_showDisplay();
    }
/*
        $d_test = D('Test');
        $_where = array();
        $_page = $d_test->getPagesize($_where,1,array(),5);
        $this->_arr['page'] = $_page;
 */
    protected function note($s_type){
        $D_AdminNote = D('site\AdminNote');
        $this->note_rules = $D_AdminNote->vRule();
        $this->_arr['note_str'] = $this->_s_type[$s_type][0];
        $this->_arr['note_action'] = $this->_s_type[$s_type][1];
        if(I('post.action') === 'edit_note'){
            $_data = I('post.');
            $_data['time_show'] = strtotime($_data['time_show']);
            unset($_data['action']);
            $rules = $this->note_rules;
            if ($D_AdminNote->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_AdminNote->writeNote($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_AdminNote->getError());
            }
        }else if(I('post.action') === 'delete_note'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_AdminNote->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }
        $_action = I('get.act');
        if($_action === 'edit'){
            $_tem_str = 'wiki_edit';
            
            $_id = (int)I('get.id');
            if($_id){
                $_where = array('id'=>$_id);
                $_resNote = $D_AdminNote->where($_where)->find();
                if(!$_resNote)exit;
                $this->_arr['resWiki'] = $_resNote;
                $this->_arr['resWiki']['content'] = htmlspecialchars_decode($this->_arr['resWiki']['content']);
            }
            $this->_arr['resWiki']['s_type'] = $_resNote['s_type']?$_resNote['s_type']:$s_type; 
            $this->_showDisplay($_tem_str);
            exit;
        }
        $_key_word = I('get.keyword');
        $_where = array(
            "s_type" => $s_type
        );
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("time_show"=>'DESC',"time_add"=>'DESC');
        $resList = $D_AdminNote->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['content'] = msubstr(strip_tags(htmlspecialchars_decode($v['content'])),0,60);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay('wiki');
    }


    public function wiki(){
        $this->note(1);
    }
    //更新记录
    public function history(){
        $this->note(2);
    }
    //大事记
    public function events(){
        $this->note(3);
    }
    //公告
    public function notice(){
        $this->note(4);
    }
    //解决方案
    public function solution(){
        $this->note(5);
    }
    //案例
    public function show_case(){
        $D_AdminCase = D('site\AdminCase');
        $this->case_rules = $D_AdminCase->vRule();
        if(I('post.action') === 'edit_case'){
            $_data = I('post.');
            $_data['time_show'] = strtotime($_data['time_show']);
            unset($_data['action']);
            $rules = $this->case_rules;
            if ($D_AdminCase->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_AdminCase->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_AdminCase->getError());
            }
        }else if(I('post.action') === 'delete_case'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_AdminCase->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }
        $_action = I('get.act');
        if($_action === 'edit'){
            $_tem_str = 'show_case_edit';
            $_id = (int)I('get.id');
            if($_id){
                $_where = array('id'=>$_id);
                $_resPage = $D_AdminCase->where($_where)->find();
                if(!$_resPage)exit;
                $_resPage['content'] = htmlspecialchars_decode($_resPage['content']);
                $this->_arr['resPage'] = $_resPage;
                
            }
            $this->_showDisplay($_tem_str);
            exit;
        }
        $_key_word = I('get.keyword');
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("time_show"=>'DESC',"id"=>'DESC');
        $resList = $D_AdminCase->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['content'] = msubstr(strip_tags(htmlspecialchars_decode($v['content'])),0,60);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
    }
    //文章
    public function article(){
        $D_AdminArticle = D('site\AdminArticle');
        $this->article_rules = $D_AdminArticle->vRule();
        if(I('post.action') === 'edit_article'){
            $_data = I('post.','','trim');;
            $_data['time_show'] = strtotime($_data['time_show']);
           
            unset($_data['action']);
            $rules = $this->article_rules;
            if ($D_AdminArticle->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_AdminArticle->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_AdminArticle->getError());
            }
        }else if(I('post.action') === 'delete_article'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_AdminArticle->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }
        $_action = I('get.act');
        if($_action === 'edit'){
            $_tem_str = 'article_edit';
            $_id = (int)I('get.id');
            if($_id){
                $_where = array('id'=>$_id);
                $_resPage = $D_AdminArticle->where($_where)->find();
                if(!$_resPage)exit;
                $_resPage['content'] = htmlspecialchars_decode($_resPage['content']);
                $this->_arr['resPage'] = $_resPage;
                
            }
            $D_AdminArticleType =D('site\AdminArticleType');
            $_order_type = array(
                "order_id" => "DESC",
                "id" => "DESC"
            );
            $_resTypeList = $D_AdminArticleType->order($_order_type)->select();

            $this->_arr['resTypeList'] = $_resTypeList;
            $this->_showDisplay($_tem_str);
            exit;
        }
        $_key_word = I('get.keyword');
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("time_show"=>'DESC',"id"=>'DESC');
        $resList = $D_AdminArticle->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['content'] = msubstr(strip_tags(htmlspecialchars_decode($v['content'])),0,60);
        }
        
        
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
    }
    
    //文章类型
    public function article_type(){
        $do_action = I('post.action');
        $D_AdminArticleType = D('site\AdminArticleType');
        $this->article_type_rules = $D_AdminArticleType->vRule();
        if($do_action == 'edit_article_type') {
            
            $_data = I('post.','','trim');
            unset($_data['action']);
            $rules = $this->article_type_rules;
            if ($D_AdminArticleType->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_AdminArticleType->write($_data,$_where);
                pushJson('更新成功');
            }else{
                pushError ($D_AdminArticleType->getError());
            }
            
        }else if($do_action == 'del_article_type'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_AdminArticleType->where('id='.$_id)->delete();
               if(!$_rst){
                   $_msg = '删除失败';
               }else{
                   pushJson($_msg);
               }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }else if($do_action == 'order_article_type'){
            $sort_order = I('post.sort_order');
            if($sort_order) {
                $sort_order_arr = $sort_order;
                foreach($sort_order_arr as $k => $v) {
                    $_where = $_data = array();
                    $_data['order_id'] = $k + 1;
                    $_where['id'] = $v;
                    $D_AdminArticleType->write($_data, $_where);
                }
                pushJson('更新成功');
            }
        }
        
        //取出所有分类
        $_where = $_order = array();
        $_order = array('order_id' => 'ASC', 'id' => 'DESC');
        $_resColumn = $D_AdminArticleType->where($_where)->order($_order)->select();
        $this->_arr['resColumn'] = list_to_tree($_resColumn);
        
        $_id = (int)I('get.id');
        if($_id){
            $_where = array('id'=>$_id);
            $_resPage = $D_AdminArticleType->where($_where)->find();
            if(!$_resPage)exit;
            $this->_arr['resPage'] = $_resPage;
        }
        
        
        $this->_showDisplay();
    }
    
}
