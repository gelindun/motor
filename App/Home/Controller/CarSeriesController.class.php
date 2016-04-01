<?php

namespace Home\Controller;
use Think\Controller;

class CarSeriesController extends HomeController {
    private $D_CarSeries;
    private $D_CarFactory;

    public function _initialize() {
        parent::_initialize();
        $this->D_CarBrand = D('car\CarBrand');
        $this->D_CarSeries = D('car\CarSeries');
        $this->D_CarFactory = D('car\CarFactory');
        $this->D_MemberCar = D('my\MemberCar');
    }
    /**
    brand to carseries
    */
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

    public function rtnBrand(){
        $_where = array();
        $_order = array(
                "letter" => "ASC"
            );
        $_resBrandArr = $this->D_CarBrand->where($_where)->order($_order)->select();
        $_resBrand = array();
        foreach($_resBrandArr as $k=>$v){
            $_resBrand[$v['letter']][] = $v; 
        }
        $this->_arr['resBrand'] = $_resBrand;
        print($this->_fetch('brand_inc'));
    }
    //待续
    public function rtnSeries(){
        $_bid = $this->_arr['brand'] = I('get.brand');
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
        print($this->_fetch('series_inc'));
    }
    /*
    * 我的爱车
    */
    public function rtnMine(){
        

        $_where = array(
                "front_uid" => $this->_arr[self::FRONT_UID],
                "delete" => array("eq","0")
            );
        $_order = array(
            'id' => "DESC"
        );
        
        $_resList = $this->D_MemberCar->getAllPagesize($_where,$_order);
        foreach($_resList['lists'] as $k=>$v){
            $_where_car = array(
                    "id" => $v['sid']
                );
            $_car = $this->D_CarSeries->where($_where_car)->find();
            $_resList['lists'][$k]['img_url'] = $_car['img_url'];
        }
        $this->_arr['resList'] = $_resList['lists'];
        print($this->_fetch('mine_inc'));
    }

}
