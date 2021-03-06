<?php

namespace Admin\Controller;
use Think\Controller;
//s_type 1:wiki,2:更新记录,3:大事记,4:公告
class ServiceController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        $D_Service = D('site\Service');
        $this->note_rules = $D_Service->vRule();
        if(I('post.action') === 'edit_note'){
            $_data = I('post.');
            unset($_data['action']);
            $rules = $this->note_rules;
            if ($D_Service->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $D_Service->write($_data,$_where);
                pushJson('更新成功',array('url'=>U('/Service/index')));
            }else{
                pushError ($D_Service->getError());
            }
        }else if(I('post.action') === 'delete_note'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_Service->where('id='.$_id)->delete();
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
            $_tem_str = 'service_edit';
            
            $_id = (int)I('get.id');
            if($_id){
                $_where = array('id'=>$_id);
                $_resNote = $D_Service->where($_where)->find();
                if(!$_resNote)exit;
                $this->_arr['resWiki'] = $_resNote;
                $this->_arr['resWiki']['content'] = htmlspecialchars_decode($this->_arr['resWiki']['content']);
            }
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
        $_order = array("time_add"=>'DESC');
        $resList = $D_Service->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['content'] = msubstr(strip_tags(htmlspecialchars_decode($v['content'])),0,60);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
    }


    
    
}
