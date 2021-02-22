<?php
/**
 * 网站首页 V2版本 目前测试用 20210221
 */
namespace Home\Controller;
use Think\Controller;


class IndexV2Controller extends BaseController{

    /**
     * 初始化
     */
    public function __initialize(){

    }
    /**
     * 首页视图
     */
    public function index(){

        //横幅幻灯片
        $slide = MS('slide')->where(['status'=>0,'show'=>0])->order("sort asc,id desc")->select();
        $height = 0;
        foreach($slide as $k=>$v){
            $size = getimagesize(C('WEB_ROOT').$v['img']);
            $height += $size?$size[1]:400;
        }

        $indexArticleChannel = [
            'news' => 199,
            'infomation' => 200,
            'service' => 201,
        ];

        //新闻动态
        $news = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>199,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(8)->select();
        //通知公告
        $infomation = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>200,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(8)->select();
        //康体服务
        $service = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>201,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(8)->select();

        $channelList = MS("channel")->where(['parent_id'=>['gt',0]])->field('id,parent_id')->select();

        //专业介绍
        $major = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>168,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(9)->select();

        $this->assign('slide',$slide);
        $this->assign('bannerHeight',$height/count($slide));
        $this->assign('news',$news);
        $this->assign('information',$infomation);
        $this->assign('service',$this->setArticleThumb($service));
        $this->assign('newsImage',$this->setArticleThumb($news));
        $this->assign('informationImage',$this->setArticleThumb($infomation));

        $this->assign('channelList',array_column($channelList,null,'id'));
        $this->assign("major",$major);
        $this->assign('more',C("theme")['more-svg']);
        $this->display();

    }

    private function setArticleThumb($articles){
        $data = [];
        foreach($articles as $k=>$v){
            $v = getThumbImage($v);
            if($v['thumb']){
                $data[] = $v;
            }
        }
        return $data;
    }


}