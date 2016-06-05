<?php

namespace Common\Model\wx;


class MaterialTextModel extends \Common\Model\BaseModel{
    protected $trueTableName = 'ton_material_text';
    
   
 	public function write($_data,$_where = array()){
        
        $rules = $this->vRule();
        if($_where){
            if($_data['action'] == "edit"){
                
                if($this->validate($rules)->create($_data)){
                    
                    $rst = $this->where($_where)->data($_data)->save();
                }else{
                    $_msg = $this->getError();
                }
            }
            
        }else{
            $_data['time_add'] = time();
            if($this->validate($rules)->create($_data)){
                $rst = $this->data($_data)->add();
            }else{
                $_msg = $this->getError();
            }
        }
       
        return $_msg?array("msg"=>$_msg):$rst;
    }
    //1新增数据时候验证,2编辑数据时候验证,3全部情况下验证
    public function vRule(){
        return array(
            
        );
    }
    
}