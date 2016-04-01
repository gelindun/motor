<?php

namespace Admin\Controller;
use Think\Controller;

class MerchantController extends AdminController {

    public function _initialize() {
        parent::_initialize();
        $this->_arr['store_title'] = "门店";
    }
    
    public function index(){
        $D_Merchant = D('site\Merchant');
        $this->note_rules = $D_Merchant->vRule();
        if(I('post.action') === 'edit_note'){
            $_data = I('post.');
            unset($_data['action']);
            $rules = $this->note_rules;
            if ($D_Merchant->validate($rules)->create($_data)){
                if($_data['id']){
                    $_where = array(
                        'id' => $_data['id']
                    );
                }
                $_rcd = $D_Merchant->write($_data,$_where);
                if(!$_data['id']){
                    $this->initPrice($_rcd);
                }
                pushJson('更新成功',array('url'=>U('/Merchant/index')));
            }else{
                pushError ($D_Merchant->getError());
            }
        }else if(I('post.action') === 'delete_note'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
               $_rst =  $D_Merchant->where('id='.$_id)->delete();
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
            $_tem_str = 'merchant_edit';
            
            $_id = (int)I('get.id');
            if($_id){
                $_where = array('id'=>$_id);
                $_resNote = $D_Merchant->where($_where)->find();
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
            $_map["store_name"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map["content"] = array('EXP','REGEXP \'^.*'.$_key_word.'.*$\'');
            $_map['_logic'] = 'or';
            $_where['_complex'] = $_map;
        }
        $_order = array("time_add"=>'DESC');
        $resList = $D_Merchant->getPagesize($_where,$this->pgSize,$_order);
        foreach($resList['lists'] as $k=>$v){
            $resList['lists'][$k]['content'] = msubstr(strip_tags(htmlspecialchars_decode($v['content'])),0,60);
        }
        $this->_arr['resList'] = $resList;
        $this->_arr['keyword'] = $_key_word;
        $this->_arr['currPg']  = I('get.p');
        $this->_showDisplay();
    }
    /**
    **  初始化门店价格
    */
    public function initPrice($_store_id){
        $D_Merchant = D('site\Merchant');
        $D_CarCylinder = D('car\CarCylinder');
        $_cylinderList = $D_CarCylinder->cylinderList();
        $D_Product = D('product\Product');
        $_product = $D_Product ->clean_form();
        $_pro_str = $_product['type'].','.$_product['pid'];
        $D_Property = D('product\Property');
        $D_PropertyPrice = D('product\PropertyPrice');
        $_clean_form = $D_Property->clean_form();
        foreach($_cylinderList as $k => $v){
            $_data_p = array(
                "pro_str" => $_pro_str,
                "pro_key" => $_clean_form['store']['key'].'|'.$_store_id.
                ','.$_clean_form['cylinder']['key'].'|'.$v['key'],
                "price" => $v['price']
            );
            if($D_PropertyPrice->create()){
                $D_PropertyPrice->write($_data_p);
            }else{
                $_msg = $D_PropertyPrice->getError();
                break;
            }
        }
    }


    
    
}
