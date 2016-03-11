<?php

namespace Common\Model\site;

/**
 * 系统单页面
 */
class PageModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_page';
    
    public function write($_data,$_where = array()){
       
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }
    
    public function vRule(){
        return array(
            array('s_type','require','请选择类型！')
        );
    }
    
}