<?php
/**
 * 网站首页
 */
namespace Home\Controller;
use Think\Controller;


class IndexController extends BaseController{

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
        $slide = MS('slide')->where(['status'=>0,'show'=>0,'type'=>0])->order("sort asc,id desc")->select();
        foreach($slide as $k=>$v){
            $slide[$k]['size'] = getimagesize(C('WEB_ROOT').$v['img']);
        }

        //首页展现的文章数据
        $channel = [
            'news'       => 199,  //新闻动态
            'information' => 200,  //通知公告
            'service'    => 201,  //旅游和健康服务
            'major'      => 168, //专业介绍
            'student'    => 208, //学生园地
            'work'       =>209, //就业创业
            'part'       =>210  //党建工作
        ];
        $channels     = MS("channel")->field("id,name,parent_id")->where(['status'=>0])->select();
        $channels = array_column($channels,null,"id");

        $data = [];
        foreach($channel as $key=>$id){

            $temp = $channels[$id];

            $data[$key]['name'] = $temp['name'];
            $data[$key]['isShow'] = $temp['show_index']?"displayNone":'';//不显示 class='dispaly:none';
            $data[$key]['url'] = U("Channel/index",['pid'=>$temp['parent_id'],'cid'=>$id]);
            $where = ['cid'=>$id];

            $limit = $key == 'major'?20:8;
            if(isMobile()){
                $limit = floor($limit * 0.66); //手机端不显示太多文章，会导致页面很长
            }
            $article = MS("article")->field('id,cid,title,content,thumb,show_create_time as create_time')
                        ->where($where)
                        ->where(['is_del'=>0,'audit'=>1])->order("top desc, show_create_time desc")->limit($limit)->select();

            if(in_array($key,['service','major'])){
                $this->assign($key,$this->setArticleUrl($this->setArticleThumb($article))); //设置缩略图
            }else{
                $this->assign($key,$this->setArticleUrl($article));
            }
            if($key == "news"){
                $this->assign($key."Image",$this->setArticleThumb($article));//只要有缩略图的文章
            }
        }

        $this->assign("link",MS('slide')->where(['status'=>0,'show'=>0,'type'=>1])->order("sort asc,id desc")->select());
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