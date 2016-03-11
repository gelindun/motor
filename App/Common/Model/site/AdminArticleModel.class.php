<?php

namespace Common\Model\site;

/**
 * useage
 * D('site\LogAct')->;
 * 系统文章
 */
class AdminArticleModel extends \Common\Model\BaseModel{
    
    protected $trueTableName = 'ton_admin_article';
    
    public function write($_data,$_where = array()){
        $_data['logo'] = str_replace(C('DATA_UPLOADS'), '', $_data['logo']);
        $_data['logo'] = str_replace('//', '/', $_data['logo']);
        if($_where){
            $rst = $this->where($_where)->data($_data)->save();
        }else{
            $_data['time_add'] = time();
            $rst = $this->data($_data)->add();
        }
        return $rst;
    }
    //
    public function vRule(){
        return array(
            array('title','require','请填写标题！'),
            array('content','require','请填写内容！'),
            array('logo','require','请选择logo！'),
            array('time_show','require','请填写发布时间！')
        );
    }
    
    public function rtnList($_where, $_pageSize = 20, $_order="", $_rollPage=0){
        $rst =  $this->getPagesize($_where, $_pageSize, $_order,$_rollPage);
        return $rst;
    }
    
}