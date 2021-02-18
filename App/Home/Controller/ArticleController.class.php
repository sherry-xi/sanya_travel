<?php
/**
 * 文章页面
 */
namespace Home\Controller;
use Think\Controller;


class ArticleController extends BaseController{


    public function index(){

        $id = I('id');
        $article = MS('article')->where(['id'=>$id,'is_del'=>0,'audit'=>1])->find();


        //上一篇，下一篇
        $where = [
            'is_del' => ['eq',0],
            'audit' => ['eq',1],
            'cid'   => ['eq',$this->cid],
            'id'    => ['lt',$id]
        ];
        $next = MS("article")->field("id,cid,title")
            ->where($where)
            ->order("top desc,id desc")
            ->find();

        $where['id'] = ['gt',$id];
        $prev = MS("article")->field("id,cid,title")
            ->where($where)
            ->order("top desc,id desc")
            ->find();
        if($article){
            $article['admin'] = MS("admin")->where(['id'=>$article['admin_id']])->getField('truename');
        }

        $this->assign('isSchoolChannel',$this->cid == 167); //是否学校简介分类
        $this->articleViewLog($id);
        $this->assign('next',$next);
        $this->assign('prev',$prev);
        $this->assign('article',$article);
        $this->assign('channelItem',$this->getChannelItem());

        if(isMobile()){
            $this->display("mobile");
        }else{
            $this->display();
        }

    }

    /***
     * 文章浏览记录
     * @param $id
     */
    public function articleViewLog($id){

        $data = ['article_id'=>$id,'ip'=>get_client_ip(),'datetime'=>date("Ymd")];

        if(MS("article_log")->where($data)->getField('id')){
            return false; //该篇文章今天已经浏览过
        }

        MS("article_log")->add($data);
    }
}