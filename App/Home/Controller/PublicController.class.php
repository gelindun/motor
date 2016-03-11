<?php

namespace Home\Controller;
use Think\Controller;

class PublicController extends Controller {

    public function _initialize() {
        
    }
    
    public function error(){
        echo "error";
        
        exit;
    }
}
