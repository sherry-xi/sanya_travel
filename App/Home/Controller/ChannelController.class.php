<?php
/**
 * 频道页面
 */
namespace Home\Controller;
use Think\Controller;


class ChannelController extends BaseController{


    public function index(){


        if($this->pid == C("channelId")['download'] || $this->channel[$this->pid]['name'] == '下载专区'){
            $this->fileChannel();
            exit;
        }

        $this->assign('channelItem',$this->getChannelItem());

        if(!$this->cid){ //一级频道
            $cid = array_column($this->channel[$this->pid]['son'],'id');
            $cid = $cid?$cid:[-1];//避免为空
        }else{//二级频道
            $cid = [$this->cid];
        }

        $where = [
            'is_del' => ['eq',0],
            'audit' => ['eq',1],
            'cid'   => ['in',$cid]
        ];
        $count = MS("article")->where($where)->count();
        $page    = getpage($count,8);
        $article = MS("article")->field("id,cid,admin_id,title,content,create_time")
                    ->where($where)
                    ->limit($page->firstRow.','.$page->listRows)
                    ->order("top desc,id desc")
                    ->select();

        if((count($article) == 1) && $this->cid){ //只有一篇文章而且是二级导航直接跳到文章详细页面
            redirect(U("Article/index",['id'=>$article[0]['id'],'pid'=>$this->pid,'cid'=>$this->cid  ]));
        }
        $article = getThumbImage($article,100,false);
        foreach($article as $k=>$v){
            $article[$k]['content'] = strip_tags($v['content']);
            $article[$k]['admin'] = MS("admin")->where(['id'=>$v['admin_id']])->getField("truename");
        }

        $this->assign('page',getPageShow($page));
        $this->assign('pageShow',$page->show());
        $this->assign('article',$article);
        $this->display();
    }


    /**
     * 文章导航频道
     */
    public function search(){
        $keyword = trim(I('keyword'));

        $keyword   = preg_replace("/select |insert |update |delete |truncate |create |grant |commit |unoin /i","",$keyword);
        if(strlen($keyword)>20){ //关键词太长
            $keyword = '-999';
        }
        $where = [
            'is_del' => ['eq',0],
            'status' => ['eq',0],
            'audit' => ['eq',1],
            'title' => ['like',"%{$keyword}%"],
        ];

        $count   = M("article")->where($where)->count();
        $page    = getpage($count,10);

        $article = M('article')
                ->field("id,cid,admin_id,title,create_time")
                ->where($where)
                ->order("top desc,id desc")
                ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $search = array(" ","　","\n","\r","\t","&nbsp;");
        $replace = array("","","","","","");
        $keywordReplace = "<span style='color: #ffc107 !important;'>{$keyword}</span>";

        foreach($article as $k=>$v){
            $article[$k]['title'] = str_replace($keyword,$keywordReplace,$v['title']); //搜索词部分替换成高亮
            $article[$k]['pid'] = MS("channel")->where(['id'=>$v['cid']])->getField('parent_id');
        }

        $this->assign('keyword',$keyword);
        $this->assign('latestNews',$this->getLatestNews());
        $this->assign('page',getPageShow($page));
        $this->assign('article',$article);
        $this->display();
    }


    /**
     * 资料导航
     */
    public function fileChannel(){

        $where = ['show'=>1];
        if(I('cid')){
            $where['cid'] = I('cid');
        }
        $count   = MS("file")->where($where)->count();
        $page    = getpage($count,10);


        $files = M('file')->where($where)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();

        foreach($files as $k=>$file){
            $files[$k]['size'] = file_size_format($file['size']);
            $files[$k]['url'] = $this->config['domain'].$file['path'];
        }


        $this->assign('channelItem',$this->getChannelItem("filedownload"));

        $this->assign('files',$files);
        $this->assign('page',getPageShow($page));
        $this->display('file');
    }

}