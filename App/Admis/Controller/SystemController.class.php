<?php
/**
 * 系统设置
 * Created by PhpStorm.
 * User: Aous
 * Date: 2017/8/22
 * Time: 22:35
 */

namespace Admis\Controller;

use Lib\Upload;

/**
 * Class SystemController
 * @package Admis\Controller
 *  System 系统设置
 *    baseSet 基本设置
 */
class SystemController extends BaseController{

    /**
     * 系统设置页面
     */
    public function index(){

        $this->setPos();
        $this->display();
    }

    /**
     * 首页图片链接设置
     * @deprecated
     */
    public function img(){

        $config = formatByKey(MS('config')->where(['type'=>6])->select(),'keyword');
        $keys = C('web-links');
        $data = [];

        foreach($keys as $key=>$value){
            $data[$key] = [
                'name' => $key,
                'text' => $value,
                'img' => $config[$key."_img"]?$config[$key."_img"]['value']:'',
                'link' => $config[$key."_link"]?$config[$key."_link"]['value']:'',
            ];
        }
        $keyword = I('get.keyword');
        $this->assign('data',$data);
        $this->setPos();
        $this->display();
    }

    /*
    * 首页图片链接设置
     * @deprecated
    */
    public function imgHandle(){
        $data = I('');
        unset($data['pid']);
        unset($data['mid']);
        unset($data['token']);

        foreach($data as $k=>$value){
            if(MS("config")->where(['keyword'=>$k])->find()){
                MS("config")->where(['keyword'=>$k])->save(['value'=>trim($value)]);
            }else{
                MS("config")->add(['value'=>trim($value),'keyword'=>$k,'type'=>6]);
            }

        }
        $this->message('设置成功',0);

    }


    /**
     * 首页链接图片上传
     * @return array|void
     * @deprecated
     */
    public function uploadLinkImg(){
        $result = parent::uploadImg();
        if(!$result['url']){
            $result = array('errcode'=>1,'上传失败');
        }
        $result = json_encode($result);
        echo "<script type='text/javascript'>";
        echo "parent.uploadCallBack('{$result}')";
        echo "</script>";
        exit;
    }


    /**
     * 系统基本信息设置
     */
    public function baseSet(){
        $this->set(0);
    }
    /**
     * 网站设置
     * @param $type 类型 0基础信息 1 网站文字信息
     */
    private function set($type){

        $except = ['type','token','PHPSESSID'];
        $keywords = array_column(MS("config")->where(['type'=>$type])->field("keyword")->select(),"keyword");

        foreach(I('') as $k=>$v){
            if(in_array($k,$except)){
                continue;
            }
            if(in_array($k,$keywords)){ //更新
                MS('config')->where(['keyword'=>$k])->save(['value'=>$v]);
            }else{ //添加
                MS('config')->add([
                        'keyword' => trim($k),
                        'value'   => $v,
                        'type'    => $type
                    ]
                );
            }
        }

        $this->ajax(0,'设置成功');
    }

    /**
     * 上传文件
     */
    public function uploadFile(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle.finish('".json_encode($result)."');</script>";
    }

    /**
     * 上传文件
     */
    public function uploadFile2(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle2.finish('".json_encode($result)."');</script>";
    }
}