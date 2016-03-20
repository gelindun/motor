<?php

namespace Common\Model;
use Think\Model;


//$article.content|htmlspecialchars_decode|stripslashes
class BaseModel extends Model{
    protected $pgSize = 20;
    
    public function add_data($_data){
        echo 'add '.$_data;
    }
    
    /**
     * 
     * @param type $where
     * @param type $pageSize
     * @param type $order
     * @param type $rollPage
     * @param type $group
     * @param type $pagestyle
     * @return type
     */
    public function getPagesize($where,$pageSize = 20,$order="",$rollPage=0,$group="",$pagestyle=0) {
        $_return = array();
        
        $_count = $this->_getCount($where);
        $page   = new \Think\Page($_count, $pageSize);
        if($rollPage > 0) {
            $page->rollPage = $rollPage;
        }
        $_return['show'] = $page->show();
        $_return['count'] = $_count;
        $_return['pgNum'] = ceil($_count/$pageSize);
        
        if($group){
            $_return['lists'] = $this->where($where)
                ->order($order)->group($group)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        }else{
            $_return['lists'] = $this->where($where)
                ->order($order)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        }
        return $_return;
    }

    public function getAllPagesize($where,$order="",$rollPage=0,$group="") {
        $_return = array();
        
        $_count = $this->_getCount($where);
        $_return['count'] = $_count;
        
        if($group){
            $_return['lists'] = $this->where($where)
                ->order($order)->group($group)
                ->select();
        }else{
            $_return['lists'] = $this->where($where)
                ->order($order)
                ->select();
        }
        return $_return;
    }
    
    protected function _getCount($where) {
        return $this->where($where)->count();
    }
    
    /**
     * 按条件排序取出指定记录
     */
    public function getList($where='', $pagesize=20, $order='', $startpage=0) {
        $_res['lists'] = $this->where($where)
                ->order($order)
                ->limit($startpage, $pagesize)
                ->select();
        return $_res;
    }
}