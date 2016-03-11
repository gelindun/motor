<?php

namespace Common\Model\site;

/**
 * useage
 * D('site\LogAct')->;
 * 系统文章
 */
class AdminArticleTypeModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_admin_article_type';
    
    public function write($_data,$_where = array()){
        $_data['logo'] = str_replace(C('DATA_UPLOADS'), '', $_data['logo']);
        $_data['logo'] = str_replace('//', '/', $_data['logo']);
        if($_data['pid']){
            $_where_p = array(
                'id' => $_data['pid']
            );
            $_pstr = $this->where($_where_p)->getField('pidstr');
        }
        $_data['pidstr'] = $_pstr?$_pstr.','.$_data['pid']:(int)$_data['pid'];
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }
    
    public function vRule(){
        return array(
            array('title','require','请填写标题！'),
            array('logo','require','请选择logo！')
        );
    }
    
    public function rtnList($_where, $_pageSize=20, $_order=array(),$_rollPage=0){
        $rst =  $this->getPagesize($_where, $_pageSize, $_order,$_rollPage);
        return $rst;
    }
}