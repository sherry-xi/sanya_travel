<?php
namespace Admis\Controller;

use Lib\Upload;

/**
 * 首页管理-幻灯片管理
 * Class HomeChannel
 * @package Admis\Controller
 *  HomeSlide 幻灯片管理
 *      index 幻灯片列表
 *      add,addHanle,添加/编辑幻灯片
 *     delete,删除幻灯片
 */
class HomeSlideController extends BaseController {

    /**
     * 幻灯片管理
     */
    public function index(){
        if(!I('get')){
            $this->display();
            exit;
        }
        $result = MS('slide')->where(['status'=>0])->order("sort asc,id desc")->select();
        $i=0;
        $data = [];
        foreach($result as $k=>$v){
            $imgUrl    = $v['img'];

            $v['show_id'] = ++$i;
            $v['link'] = "<a href='".$v['link']."' target='_blank'>{$v['link']}</a>";
            $v['show'] = $v['show']?'不显示':"显示";
            $v['show_img']  = "<a href='{$imgUrl}' target='_blank'> <img src='{$imgUrl}'/></a>";
            $data[] = $v;
        }
        $this->ajaxTable($data);
    }



    /**
     * 添加/编辑
     */
    public function add(){
        $data= MS('slide')->where(['id'=>I('id')])->find();
        if($data){
            $data['img_url'] = $this->config['domain'].$data['img'];
        }
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 执行添加或编辑
     */
    public function addHandle(){

        $id   = I('id');
        $data = array(
            'title'     => trim(I('title')),
            'link'      => trim(I('link')),
            'show'    => trim(I('show')),
            'img'       => trim(I('img')),
            'sort'      => intval(I('sort')),
            'title'       => trim(I('title')),
            'content'       => trim(I('content'))
        );

        if($id){
            $data['id'] = $id;
            MS('slide')->save($data);
            $this->ajax(0,'编辑成功');

        }else{
            $data['cid'] =  $data['cid']? $data['cid']:'';
            $data['create_time'] = date("Y-m-d H:i:s");
            MS('slide')->add($data);
            $this->ajax(0,'添加成功');
        }
    }

    /**
     * 删除
     */
    public function delete(){
        MS('slide')->where(['id'=>I('id')])->save(['status'=>1]);
        unlink('./'.I('img'));
        $this->ajax(0,'删除成功');
    }

    /**
     * 上传文件
     */
    public function uploadFile(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle.finish('".json_encode($result)."');</script>";
    }


}
