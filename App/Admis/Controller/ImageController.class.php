<?php
namespace Admis\Controller;
/**
 * 相册管理
 * Class ImageController
 */
class ImageController extends BaseController {
    /**
     * 相册列表
     */
    public function index(){

        $channel = MS("channel")->field(['id','name','parent_id'])->where(["classify"=>1,'status'=>0])->order("sort",'asc')->order("parent_id",'asc')->select();

        $images  = MS("photo")->order("sort asc,id desc")->select();
        array_unshift($channel,['id'=>0,'name'=>'未分组','parent_id'=>0]);

        $data = [];
        foreach($channel as $v){
            $data[$v['id']] = $v;
        }

        foreach($data as $id => $v){
            if($v['parent_id'] && isset($data[$v['parent_id']])){
                $data[$id]['name'] = $data[$v['parent_id']]['name'].' > '.$v['name'];
            }
        }

        foreach($images as $k=>$v){
            $data[$v['channel_id']]['image'][] = $v;
        }

        $this->assign('dataJsong',json_encode($images));
        $this->assign('images',$data);
        $this->assign('channel',$channel);
        $this->setPos();
        $this->display();
    }

    /**
     * 上传相册
     */
    public function uploadImg3(){
        $result = parent::uploadImg();
        $flag = false;
        if($result['errcode'] == 0 && $result['url']){
            $data = array(
                'user_id' => session("user.id"),
                'url' => $result['url']
            );
            $flag = MS("photo")->add($data);
            if($flag){
                session("msg",array('msg'=>"上传成功",'errcode'=>0));
            }else{
                session("msg",array('msg'=>"上传成功,但插入数据库失败",'errcode'=>1));

            }
        }

        $result = json_encode($result);

        echo "<script type='text/javascript'>";
        echo "parent.uploadCallBack('{$result}')";
        echo "</script>";
        exit;

    }


    /**
     * 删除相片
     */
    public function delImg(){

        $id = intval(I('id'));
        $img = MS("photo")->where(array('id'=>$id))->find();
        if(!$img){
            $this->message('相片不存在',1);
        }
        $res = MS("photo")->where(array('id'=>$id))->delete();
        if($res){
            unlink("./Public/uploadimg/".$img['url']);
            $this->message('删除成功');
        }else{
            $this->message('删除失败',1);
        }
    }

    /**
     * 修改相片
     */
    public function editImg(){
        $data = array(
            'id'     => intval(I('id')),
            'remark' => trim(I('remark')),
            'status'=> intval(I('status')),
            'show_index'=> intval(I('show_index')),
            'channel_id' => I('channelId')
        );
        MS('photo')->save($data);
        $this->ajaxReturn(array('status'=>0));
    }
}
