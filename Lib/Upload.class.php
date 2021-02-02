<?php
namespace Lib;
/**
 * 文件上传
 * Class Upload
 *  幻灯片
 *  资料管理
 *  相册
 *  文章内容和图片和文件
 */
use Think\Upload as ThinkUpload;

class Upload{

    public function upload(){

        $result = array(
            'errcode' => 1,
            'msg' => '',
        );
        $upload = new ThinkUpload();// 实例化上传类
        $upload->savePath  =     ''; // 设置附件上传（子）目录

        //来源页面不同存储位置不同
        $path = ""; //相对路径

        switch (CONTROLLER_NAME){
            case 'File': //文件列表
                $path  =     C('UPLOAD_FILE'); // 设置附件上传根目录
                $upload->maxSize   =     50*1024*1024 ;// 设置附件上传大小50M
                break;
            case 'HomeSlide': //幻灯片
            case 'Image':
                $path  =     C('UPLOAD_IMAGE');
                $upload->maxSize   =     1024*1024*10 ;// 设置附件上传大小10M
                $upload->exts      =     array('jpg', 'jpeg', 'png', 'JPEG','gif','JPG');// 设置附件上传类型
                break;
        }

        $upload->rootPath = C('WEB_ROOT').$path; //绝对路径

        // 上传文件
        $info   =   $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            return [
                'errcode' => 0,
                'msg' => $upload->getError()
            ];
        }

        $info = $info['file'];
        $info['errcode'] = 0;
        $info['msg']     = '上传成功';
        $info['path']    = $path.$info['savepath'].$info['savename'];
        $info['url']     = C('DOMAIN').$path.$info['savepath'].$info['savename'];
        return $info;


    }

}