<?php

namespace Home\Controller;
use Think\Controller;

class FileController extends HomeController {

    public function _initialize() {
        parent::_initialize();
    }
 
    //头像
    public function avatarUpload() {
        if (empty($this->_arr[self::FRONT_UID])){
            return false;
        }
        $data['filetype'] = (int)I('fileType')?(int)I('fileType'):1;
        $_info = $this->upload(0,$data['filetype']==1?1:0);
        $_file = $_info['info']['file'];
        
        $_data['head_img'] = $_info['small'];
        $_where['id'] = $this->_arr[self::FRONT_UID];
        D('my\Member')->where($_where)->data($_data)->save();
        
        $_ext = I('post.ext');
        if(!$_ext){
            $_info = array(
                "filename" => $_info['info']['file']['name'],
                "size" => byte_format($_info['info']['file']['size']),
                "savename" =>  $_info['savename'],
                "small" => $_info['small']
            );
        }
        
        echo json_encode($_info);exit;
        
    }
    /**
     *  x:62
        y:55
        x2:605
        y2:615
        w:543
        h:559
        src:xx.jpeg
     */
    public function crop(){
        $_data = I('post.');
        header('Content-type: text/json');
        $_thumbName = $_data['src'];
        $_abs_path = preg_replace('/^\//','', $_thumbName);
        $_image = new \Think\Image();
        $_image->open($_abs_path);
        $_img = $_image->crop($_data['w'], $_data['h'], $_data['x'], $_data['y'],200, 200)->save($_abs_path);
                    
        $_arr = array(
            "success" => true,
            "code" => 200,
            "msg" => "success",
            "map" => null,
            "list" => null,
            "obj" => $_data['src']
        );
        
        echo json_encode($_arr);
        exit;
    }

}
