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
        foreach($slide as $k=>$v){
            $slide[$k]['size'] = getimagesize(C('WEB_ROOT').$v['img']);
        }

        //首页展现的文章数据
        $channel = [
            'news'       => 199,  //新闻动态
            'information' => 200,  //通知公告
            'service'    => 201,  //旅游和健康服务
            'major'      => 168, //专业介绍
            'student'    => 161, //学生园地
            'work'       =>162, //就业创业
            'part'       =>163  //党建工作
        ];

        $data = [];
        foreach($channel as $key=>$id){

            $temp = $this->originChannel[$id];
            $data[$key]['name'] = $temp['name'];

            if($temp['son']){//一级级导航
                $data[$key]['url'] = U("Channel/index",['pid'=>$temp['id']]);
                $where = ['cid'=>['in',array_keys($temp['son'])]];
            }else{//二级导航
                $data[$key]['url'] = U("Channel/index",['pid'=>$temp['parent_id'],'cid'=>$id]);
                $where = ['cid'=>$id];
            }
            $limit = $key == 'major'?20:8;
            $article = MS("article")->field('id,cid,title,content,thumb,create_time')
                        ->where($where)
                        ->where(['is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit($limit)->select();

            if(in_array($key,['service','major'])){
                $this->assign($key,$this->setArticleUrl($this->setArticleThumb($article))); //设置缩略图
            }else{
                $this->assign($key,$this->setArticleUrl($article));
            }
            if($key == "news"){
                $this->assign($key."Image",$this->setArticleThumb($article));//只要有缩略图的文章
            }
        }

        $this->assign('slide',$slide);
        $this->assign('more',C("theme")['more-svg']);
        $this->assign('articleChannel',$data);
        $this->display();

    }

    /**
     * 设置文章url
     */
    private function setArticleUrl($article){
        foreach($article as $key=>$value){
            if($value['cid'] == 168){  //专业介绍页面
                $article[$key]['url'] = U("Channel/index",['pid'=>$this->originChannel[$value['cid']]['parent_id'],'cid'=>$value['cid'],'id'=>$value['id']]);
            }else{
                $article[$key]['url'] = U("Article/index",['pid'=>$this->originChannel[$value['cid']]['parent_id'],'cid'=>$value['cid'],'id'=>$value['id']]);
            }

        }
        return $article;
    }

    /**
     * 设置文字缩略图
     * @param $articles
     * @return array
     */
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