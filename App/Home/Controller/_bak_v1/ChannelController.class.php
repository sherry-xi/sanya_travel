<?php
/**
 * 频道页面
 */
namespace Home\Controller;
use Think\Controller;


class ChannelController extends BaseController{

    /**
     * 频道页面视图
     */
    public function index(){
        $parentChannel = $this->getChannelById(I('pid'));
        $this->assign('parentChannel',$parentChannel);

        //判断页面类型
        $channel = $this->getChannelById($this->mid?$this->mid:$this->pid);

        switch ($channel['classify']){
            case 0: //文章
                $this->articleChannel($channel);
                break;
            case 1: //相册
                $this->imageChannel($channel);
                break;
            case 2: //资料
                $this->fileChannel($channel);
                break;
            case 3://超链接
                $this->linkChannel($channel);
                break;
        }
    }

    /**
     * 文章导航频道
     */
    public function articleChannel($channel){
        $keyword = trim(I('keyword'));

        if($keyword){
            //用户通过顶部搜索框 搜索文章
            $article = $this->searchByKeyword($keyword,10);
        }else{
            //新闻频道 获取新闻列表
            if($this->mid){
                $article = $this->getArticleTwoByCid($this->mid,false);//用户点击二级导航
            }else{
                $article = $this->getArticleTwoByCid($this->pid,true);//用户点击一级导航
            }
            //如果只有一篇文章 直接显示文章内容
            if(count($article) == 1){
                $this->assign('isSingle',1);//标志是否单篇文章
                $this->article($article[0]);
                exit;
            }else{//不止一篇文章 显示文章列表
                $cid = $this->mid?$this->mid:$this->pid;
                $article = $this->getArticleByCids($cid);
            }

        }

        $newsList = $article['article'];
        $search = array(" ","　","\n","\r","\t","&nbsp;");
        $replace = array("","","","","","");
        $keywordReplace = "<span style='color: #116ee2 !important;'>{$keyword}</span>";

        foreach($newsList as $k=>$v){
            $v['author']      = $v['author']?$v['author']:$this->config['website'];
            $v['create_time'] = formatArticleTime($v['create_time']);
            $v['content']     = htmlspecialchars_decode($v['content']);
            $v['description'] = str_replace($search, $replace, strip_tags($v['content']));
            $v['thumb']       = $v['thumb']?$v['thumb']:$this->config['defaultThumb'];

            if($keyword){    //搜索词部分替换成高亮
                $v['title'] = str_replace($keyword,$keywordReplace,$v['title']);
            }
            if($v['show_time']){
                $v['create_time'] = substr($v['show_time'],0,16);
            }
            $newsList[$k]     = $v;

        }

        $this->assign('keyword',$article['keyword']);
        $this->assign('page',$article['page']->show());
        $this->assign('newsList',$newsList);

        $this->display();
    }

    /**
     * 相册导航
     */
    public function imageChannel($channel){
        $where = [];
        if($channel['parent_id']){
            $where = ['channel_id'=>$channel['id']];
        }else{ //父级
            $parentChannel = $this->getChannelById($channel['id']);
            $ids = array_column($this->sonChannel,'id');
            $where['channel_id'] = array('in',array_merge($ids,[$channel['id']]));
        }

        $images  = MS("photo")->where($where)->order("sort asc,id desc")->select();
        $this->assign('images',$images);
        $this->display('img');
    }

    /**
     * 资料导航
     */
    public function fileChannel($channel){

        if($channel['parent_id']){
            $where = ['channel_id'=>$channel['id']];
        }else{ //父级
            $parentChannel = $this->getChannelById($channel['id']);
            $ids = array_column($this->sonChannel,'id');
            $where['channel_id'] = array('in',array_merge($ids,[$channel['id']]));
        }

        $count   = M("file")->where($where)->count();
        $page    = getpage($count,20);


        $files = M('file')->where($where)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

        foreach($files as $k=>$file){
            $tempChannel = $this->getChannelById($file['channel_id']);
            $files[$k]['size'] = file_size_format($file['size']);
            $files[$k]['channel'] = $tempChannel['name'];

        }
        $rootPath = $this->config['domain'].C("UPLOAD_FILE");
        $this->assign('rootPath',$rootPath);
        $this->assign('files',$files);
        $this->assign('pageShow',$page->show());
        $this->display('file');
    }

    /**
     * 超链接导航
     */
    public function  linkChannel($channel){

    }


    /**
     * 文章浏览页面
     */
    public function article($article){

        $article['create_time'] = formatArticleTime($article['create_time']);
        $article['content']     = htmlspecialchars_decode($article['content']);

        if(!$article['author']){
            $article['author']  = $this->config['website'];
        }
        if($article['show_time']){
            $article['create_time'] = substr($article['show_time'],0,16);
        }

        $this->assign('article',$article);
        $parentChannel = $this->getChannelById(I('pid'));
        $this->assign('parentChannel',$parentChannel);
        $this->display("Index/article");
    }
}