<?php
/**
 * 频道页面
 */
namespace Home\Controller;
use Think\Controller;


class ChannelController extends BaseController{


    public function index(){

        $pageSize = 8;

        if(($this->pid == C("channelId")['download']) || ($this->channel[$this->pid]['name'] == '下载专区')){
            $this->fileChannel();
            exit;
        }
        if(($this->cid == 168) || ($this->channel[$this->pid]['name'] == '专业介绍')){
            $pageSize = 1000; //专业介绍页面不分页
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
        $page    = getpage($count,$pageSize);
        $article = MS("article")->field("id,cid,admin_id,title,content,show_create_time as create_time,thumb,banner")
                    ->where($where)
                    ->limit($page->firstRow.','.$page->listRows)
                    ->order("top desc,id desc")
                    ->select();

        if((count($article) == 1) && $this->cid &&(!I("p"))){ //只有一篇文章而且是二级导航直接跳到文章详细页面
            redirect(U("Article/index",['id'=>$article[0]['id'],'pid'=>$this->pid,'cid'=>$this->cid  ]));
        }
        foreach($article as $k=>$v){
            $v = getThumbImage($v,$this->config['article_thumb']);
            $v['content2'] = $v['content'];
            $v['content']  = trim(strip_tags($v['content']));
            $v['admin']    = MS("admin")->where(['id'=>$v['admin_id']])->getField("truename");

            $article[$k] = $v;
        }

        $this->assign('page',getPageShow($page));
        $this->assign('pageShow',$page->show());
        $this->assign('article',$article);

        if(($this->cid == 168) || ($this->channel[$this->pid]['name'] == '专业介绍')){
            $selectArticle = $article[0]; //默认选中的专业文章
            foreach($article as $v){
                if($v['id'] == I('id')){
                    $selectArticle = $v;
                }
            }
            $this->assign('selectArticle',$selectArticle);
            $this->assign('article',$article);
            $this->assign("apartmentName",$this->getChannelById($this->cid)['name']);
            $this->display("apartment");
            exit;
        }
        $this->display();
    }



    /**
     * 文章导航频道
     */
    public function search(){
        $keyword = trim(I('keyword'));

        $keyword   = preg_replace("/select |insert |update |delete |truncate |create |grant |commit |unoin /i","",$keyword);
        if(!$keyword || strlen($keyword)>30){ //关键词太长
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
                ->field("id,cid,admin_id,title,show_create_time as create_time,content")
                ->where($where)
                ->order("top desc,id desc")
                ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $search = array(" ","　","\n","\r","\t","&nbsp;");
        $replace = array("","","","","","");
        $keywordReplace = "<span style='color: #ffc107 !important;'>{$keyword}</span>";

        foreach($article as $k=>$v){
            $article[$k] = getThumbImage($v,C("image")['thumb_channelnews']);
            $article[$k]['title'] = str_replace($keyword,$keywordReplace,$v['title']); //搜索词部分替换成高亮
            $article[$k]['pid'] = MS("channel")->where(['id'=>$v['cid']])->getField('parent_id');
        }
        $this->assign("latestNews",$this->getLatestNews());
        $this->assign('keyword',$keyword);
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

        $fileIcon = [
            'doc' =>'file_docx.png',
            'docx' => 'file_docx.png',
            'ppt' => 'file_pptx.png',
            'pptx' => 'file_pptx.png',
            'zip' => 'file_zip.png',
            'rar' => 'file_rar.png',
            'tar' => 'file_zip.png',
            'xls' => 'file_xlsx.png',
            'xlsx' =>'file_xlsx.png',
            'txt' => 'file_text.png',
            'pdf' => 'file_pdf.png'
        ];

        foreach($files as $k=>$file){

            $fileType = strtolower(substr($file['path'],strpos($file['path'],'.')+1));
            $files[$k]['fileIcon'] = isset($fileIcon[$fileType])?$fileIcon[$fileType]:'';
            $files[$k]['size'] = file_size_format($file['size']);
            $files[$k]['url'] = $this->config['domain'].$file['path'];
        }

        $this->assign('channelItem',$this->getChannelItem("filedownload"));

        $this->assign('files',$files);
        $this->assign('page',getPageShow($page));
        $this->display('file');
    }

}