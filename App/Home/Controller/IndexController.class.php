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

        $this->assign('slide',MS('slide')->where(['status'=>0,'show'=>0])->order("sort asc,id desc")->select());

        $indexArticleChannel = [
            'news' => 199,
            'infomation' => 200,
            'service' => 201,
        ];

        //新闻动态
        $news = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>199,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(9)->select();
        //通知公告
        $infomation = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>200,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(8)->select();
        //康体服务
        $service = MS("article")->field('id,cid,title,content,thumb,create_time')->where(['cid'=>201,'is_del'=>0,'audit'=>1])->order("top desc,id desc")->limit(8)->select();

        $channelList = MS("channel")->where(['parent_id'=>['gt',0]])->field('id,parent_id')->select();

        $this->assign('news',$news);
        $this->assign('information',$infomation);
        $this->assign('service',$this->setArticleThumb($service));
        $this->assign('newsImage',$this->setArticleThumb($news));
        $this->assign('informationImage',$this->setArticleThumb($infomation));

        $this->assign('channelList',array_column($channelList,null,'id'));;
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


    /**
     * 文章浏览页面
     */
    public function article(){
        $id  = intval(I('get.id'));
        $type  = intval(I('get.type'));

        $this->assign('id',$id);

        $whereBase['is_del'] = array('eq',0);
        $whereBase['status'] = array('eq',0);
        $whereBase['audit'] = array('eq',1);


        $where1 = $whereBase;
        $where1['id'] = array('eq',$id);
        $article = M('article')->where($where1)->find();

        $field = array('id,title');
        //查找上一篇文章
        $wherePre['id']  = array('lt',$id);
        $wherePre['cid'] = array('eq',$this->cid);
        $articlePre        = M('article')->field($field)->where($wherePre)->order("id desc")->find();

        //下一篇文章
        $whereNext['id']  = array('gt',$id);
        $whereNext['cid'] = array('eq',$this->cid);
        $articleNext      = M('article')->field($field)->where($whereNext)->order("id desc")->find();

        if($article){
            $article['create_time'] = formatArticleTime($article['create_time']);
            $article['content']     = htmlspecialchars_decode($article['content']);

            if(!$article['author']){
                $article['author']  = $this->config['website'];

            }
        }
        if($article['show_time']){
            $article['create_time'] = substr($article['show_time'],0,16);
        }
        $this->assign('article',$article);
        $this->assign('articleNext',$articleNext);
        $this->assign('articlePre',$articlePre);
        $parentChannel = $this->getChannelById(I('pid'));
        $this->assign('parentChannel',$parentChannel);
        if(!$type){
            $this->display();
        }else{
            $this->display('article2');
        }
    }


    public function article2(){
        $id  = intval(I('get.id'));
        $type  = intval(I('get.type'));

        $this->assign('id',$id);

        $whereBase['is_del'] = array('eq',0);
        $whereBase['status'] = array('eq',0);
        $whereBase['audit'] = array('eq',1);


        $where1 = $whereBase;
        $where1['id'] = array('eq',$id);
        $article = M('article')->where($where1)->find();

        $field = array('id,title');
        //查找上一篇文章
        $wherePre['id']  = array('lt',$id);
        $wherePre['cid'] = array('eq',$this->cid);
        $articlePre        = M('article')->field($field)->where($wherePre)->order("id desc")->find();

        //下一篇文章
        $whereNext['id']  = array('gt',$id);
        $whereNext['cid'] = array('eq',$this->cid);
        $articleNext      = M('article')->field($field)->where($whereNext)->order("id desc")->find();

        if($article){
            $article['create_time'] = formatArticleTime($article['create_time']);
            $article['content']     = htmlspecialchars_decode($article['content']);

            if(!$article['author']){
                $article['author']  = $this->config['website'];

            }
        }

        $this->assign('article',$article);
        $this->assign('articleNext',$articleNext);
        $this->assign('articlePre',$articlePre);
        if(!$type){
            $this->display();
        }else{
            $this->display('article2');
        }
    }


    public function article3(){
        $param = I("flag",0);
        file_put_contents("./Public/document/article.txt",$param);
    }
}