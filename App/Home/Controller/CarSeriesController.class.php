<?php

namespace Home\Controller;
use Think\Controller;

class CarSeriesController extends HomeController {
    private $D_CarSeries;
    private $D_CarFactory;

    public function _initialize() {
        parent::_initialize();
        $this->D_CarSeries =D('car\CarSeries');
        $this->D_CarFactory =D('car\CarFactory');
    }
    
    public function fetchSeries(){
        $_bid = $this->_arr['bid'] = I('get.bid');
        $_fidArr = $this->D_CarFactory->where(
                array('bid'=>$_bid)
            )->field('id')->select();
        $_fidStr = "";
        foreach($_fidArr as $k=>$v){
            if($k > 0)$_fidStr .= ",";
            $_fidStr .= $v['id'];
        }

        $_where = array(
                "fid" => array(
                        "in",$_fidStr
                    )
            );
        $_order = array(
            'id' => "DESC"
        );
        
        $_resList = $this->D_CarSeries->getAllPagesize($_where,$_order);
        
        
        $this->_arr['resList'] = $_resList;
        pushJson('ok',array(
                "list" => $_resList['lists']
            ));
    }
    
}
