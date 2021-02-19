<?php
namespace Admis\Controller;

use Lib\Upload;

/**
 * 首页管理-导航管理
 * Class HomeChannel
 * @package Admis\Controller
 *      HomeChannel 导航管理
 *      index,son 导航列表
 *      delete,deleteSon删除频道
 *      add ,addHandle,addSon,addSonHandle添加/编辑频道
 */
class HomeChannelController extends BaseController {


    /**
     * 频道列表 一级频道
     */
    public function index(){
        if(!I('get')){
            $this->display();
            exit;
        }

        $result     = MS('channel')->where(array('status'=>0,'parent_id'=>0))->order('sort asc,id asc')->select();

        $i  =0;
        $data = [];
        foreach($result as $k=>$v){
            $sonList = MS('channel')->where(array('status'=>0,'parent_id'=>$v['id']))->field('id')->select();
            $ids = array_column($sonList,'id');


            $data[]  = [
                'id'            => $v['id'],
                'show_id'       => ++$i,
                'name'          => $v['name'],
                'level'         => '一级',
                'show_nav'      => $v['show_nav']?'不显示':'显示',
                'sonCnt'        => count($sonList),
                'articleCnt'    =>  $ids?MS("article")->where(['cid'=>['in',$ids],'is_del'=>0])->count():0,
                'sort'          => $v['sort'],
                'classify_name' => C("channel-classify")[$v['classify']],
                'create_time'   => substr($v['create_time'],0,16)
            ];
        }

        $this->ajaxTable($data);
    }

    /*
    * 频道列表 二级频道
    */
    public function son(){

        if(!I('get')){
            $this->assign("parent",MS('channel')->where(['status'=>0,'id'=>I('id')])->find());
            $this->display();
            exit;
        }
        $channel = MS("channel")->where(['status'=>0,'parent_id'=>I('parent_id')])->order("sort asc,id asc")->select();
        $parentName  = MS('channel')->where(['status'=>0,'id'=>I('parent_id')])->getField('name');
        $data = [];
        $i = 0;
        foreach($channel as $k=>$v){
            $data[]  = [
                'id'            => $v['id'],
                'show_id'       => ++$i,
                'name'          => $parentName.' > '.$v['name'],
                'level'         => '二级',
                'show_nav'      => $v['show_nav']?'不显示':'显示',
                'articleCnt'    =>  MS('article')->where(['cid'=>$v['id'],'is_del'=>0])->count(),
                'sort'          => $v['sort'],
                'classify_name' => C("channel-classify")[$v['classify']],
                'create_time'   => substr($v['create_time'],0,16),
                'parent_id'     => I('parent_id')
            ];
        }
        $this->ajaxTable($data);
    }


    /**
     * 删除频道
     */
    public function delete(){
        $id = I('id');
        //删除改频道下的所有文章
        $channelIds = MS("channel")->where(['parent_id'=>$id,'status'=>0])->field('id')->select();
        if($channelIds){
            $channelIds = array_column($channelIds,'id');
            MS('article')->where(['cid'=>['in',$channelIds]])->save(['is_del'=>2]); //删除子级文章
        }
        MS('article')->where(['parent_cid'=>$id])->save(['is_del'=>2]); //删除本级文章

        //删除子频道
        MS('channel')->where(array('parent_id'=>$id))->save(['status'=>1]);
        MS('channel')->where(array('id'=>$id))->save(['status'=>1]);
        $this->ajax(0,"删除成功");
    }

    /**
     * 添加导航频道
     */
    public function add(){
        $this->assign('channel',MS('channel')->where(['id'=>I('id')])->find());
        $this->assign('classify',C("channel-classify"));
        $this->display();
    }

    /**
     * 添加/编辑一级导航
     */
    public function addHandle(){

        $id        = I('id');
        $data = array(
            'name'       => trim(I('name')),
            'sort'       => I('sort'),
            'show_nav'   => I('show_nav'),
            'classify'  => I('classify'),
            'banner'     => I("banner"),
            'banner_title'     => I("banner_title"),
            'banner_content'     => I("banner_content"),
        );

        if($id){ //编辑

            $data['id'] = $id;
            MS("channel")->save($data);
            MS('channel')->where(["parent_id" => $id])->save(['classify'=>$data['classify']]); //同时更新子级分类

            $this->ajax(0,'编辑成功');
        }

        $data['create_time'] = date("Y-m-d H:i:s");
        MS('channel')->add($data);
        $this->ajax(0,'添加成功');

    }

    /**
     * 添加二级导航频道
     */
    public function addSon(){
        $this->assign('channel',MS('channel')->where(['id'=>I('id')])->find());
        $this->assign('parent',MS('channel')->where(['id'=>I('parent_id')])->find());
        $this->display();
    }

    /**
     * 添加编辑二级导航
     */
    public function addSonHandle(){
        $id        = I('id');
        $data = array(
            'parent_id'  => I('parent_id'),
            'name'       => trim(I('name')),
            'sort'       => I('sort'),
            'show_nav'   => I('show_nav'),
            'classify'  => I('classify'),
            'banner'    => I('banner'),
            'banner_title'     => I("banner_title"),
            'banner_content'     => I("banner_content"),
        );
        if($id){ //编辑

            $data['id'] = $id;
            MS("channel")->save($data);
            $this->ajax(0,'编辑成功');
        }

        $data['create_time'] = date("Y-m-d H:i:s");
        MS('channel')->add($data);
        $this->ajax(0,'添加成功');
    }

    /**
     * 删除二级频道
     */
    public function deleteSon(){
        $id = I('id');
        MS('article')->where(['cid'=>$id])->save(['is_del'=>2]); //删除文章
        MS('channel')->where(array('id'=>$id))->save(['status'=>1]);
        $this->ajax(0,"删除成功");
    }
    /**
     * 上传文件
     */
    public function uploadFile(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle.finish('".json_encode($result)."');</script>";
    }

}
