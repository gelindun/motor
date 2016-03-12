<?php

namespace Admin\Controller;
use Think\Controller;

class FileController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        
        $this->_showDisplay();
    }
    
    //filetype 0背景,2音乐,1图片
    public function fileUpload() {
        $data['filetype'] = (int)I('fileType')?(int)I('fileType'):1;
        $_info = $this->upload(0,$data['filetype']==1?1:0);
        $_file = $_info['info']['file'];
        $D_FileSys = D('FileSys');
        // 保存当前数据对象
        $data['ext'] = strtoupper($_file['ext']);
        $data['filename'] = $_file['name'];
        
        $data['biztype'] = (int)I('bizType')?(int)I('bizType'):0;
        $data['uid'] = (int)I('uid');
        $data['filesrc'] = $_info['savename'];
        $data['sizekb'] = (int)($_file['size']/1024);
        $data['filethumbsrc'] = $_info['small']?$_info['small']:"";
        $data['create_time'] = time();
        $data['limit'] = 1;
        $D_FileSys->create();
        $_id = $D_FileSys->data($data)->add();
        
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
    
    public function imgList(){
        $D_FileSys = D('FileSys');
        $_where = array(
            "filetype" => 1,
            "delete" => 0
        );
        $_order = array(
            "create_time" => "DESC"
        );
        $_imgList = $D_FileSys->getPagesize($_where,18,$_order);
        unset($_imgList['show']);
        pushJson('ok',$_imgList);
    }

}
