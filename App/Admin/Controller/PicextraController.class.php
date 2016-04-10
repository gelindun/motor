<?php

namespace Admin\Controller;
use Think\Controller;

class PicextraController extends AdminController {
    protected $_picKey;

    public function _initialize() {
         parent::_initialize();
         $this->_picKey = D('site\PicPos')->rtnKeyArr();
    }
    
    public function index(){
       $D_PicPos = D('site\PicPos');
       $_key_word = I('get.keyword');
       if(I('post.action') === 'delete_pic'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_data = array(
                   "action" => 'delete_pic',
                   "delete" => 1
               );
               $_rst =  $D_PicPos->write($_data,array('key'=>$_key));
               if(is_array($_rst)){
                    $_msg = $_rst['msg'];
                }
               
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
        if($_key_word){
            $_map["title"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["pic_json"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        
        $_order = array("id"=>'DESC');
        $resList['lists'] = array();
        foreach($this->_picKey as $k=>$v){
            $_res = $D_PicPos->where(
                    array("key" => $v["key"])
                )->find();
            $_arr = array(
                    "id" => $_res['id']?$_res['id']:'无',
                    "key" => $v["key"],
                    "title" => $_res['title']?$_res['title']:$v['title'],
                    "pic_json" => $_res['pic_json']
                );
            $resList['lists'][] = $_arr;
        }
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['pic_arr'] = 
            json_decode($v['pic_json'],true);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_showDisplay();
        //exit;
    }
    
    public function pic_edit(){
        $D_PicPos = D('site\PicPos');
        if(I('post.action') === 'edit_pic'){
            $_data = I('post.','','trim');
            $_data['pic_json'] = explode(',', $_data['pic_str']);
            $_data['pic_json'] = json_encode($_data['pic_json']);
            unset($_data['pic_str']);
            unset($_data['action']);
            $_data['action'] = "edit_pic";
            
            if($_data['key']){
                $_where = array(
                    'key' => $_data['key']
                );
                if(!$D_PicPos->where($_where)->find()){
                    unset($_where);
                }
            }
            $_rst =  $D_PicPos->write($_data,$_where);
            if(!is_array($_rst)){
                pushJson('更新成功');
            }else{
                pushError ($_rst['msg']);
            }
        }
        
        $_key = I('get.key');
        if($_key){
            $_where = array('key'=>$_key);
            $_resPage = $D_PicPos->where($_where)->find();
            if(!$_resPage){
                $_resPage = $this->_picKey[$_key];
                if(!$_resPage)exit;
            };
            $_resPage['pic_arr'] = json_decode($_resPage['pic_json'],true);
            $_resPage['pic_str'] = implode(',', $_resPage['pic_arr']);
            $this->_arr['resPage'] = $_resPage;
        }
        
        $this->_showDisplay();
    }
    
    
}
