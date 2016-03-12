<?php

namespace Admin\Controller;
use Think\Controller;

class FileController extends AdminController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index(){
        $D_File = D('File');
        $_action = I('post.action');
        if($_action == 'delete_res'){
            $_id = (int)I('post.id');
            $_msg = '删除成功';
            if($_id){
                $_member_id = $this->_arr[self::MEMBER_ID];
                $_where_res['id'] = $_id?$_id:I('get.id');
                $_where_res['uid'] = intval($_member_id);
                $datainfo['delete'] = 1;
                $_rst = $D_File->where($_where_res)->data($datainfo)->save();
                //echo M()->_sql();
                if(!$_rst){
                    $_msg = '删除失败';
                }else{
                    pushJson($_msg);
                }
            }else{
                $_msg = '无效参数';
            }
            pushError($_msg);
        }
        $_where = array(
            "filetype" => I('get.type')?I('get.type'):1,
            "delete" => 0,
            "uid" => 0
        );
        $this->_arr['filetype'] = $_where['filetype'];
        $_order = array(
            "create_time" => "DESC"
        );
        $_imgList = $D_File->getPagesize($_where,20,$_order);
        $this->_arr['resList'] = $_imgList;
        $this->_showDisplay();
    }
    
    //filetype 0背景,2音乐,1图片
    public function fileUpload() {
        $data['filetype'] = (int)I('fileType')?(int)I('fileType'):1;
        $_info = $this->upload(0,$data['filetype']==1?1:0);
        $_file = $_info['info']['file'];
        $D_File = D('File');
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
        $D_File->create();
        $_id = $D_File->data($data)->add();
        
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
        $D_File = D('File');
        $_where = array(
            "filetype" => 1,
            "delete" => 0
        );
        $_order = array(
            "create_time" => "DESC"
        );
        $_imgList = $D_File->getPagesize($_where,18,$_order);
        unset($_imgList['show']);
        pushJson('ok',$_imgList);
    }

}
