<?php
/**
 * 前台基础控制器
 * Created by PhpStorm.
 * User: Aous
 * Date: 2017/8/19
 * Time: 9:44
 */

namespace Home\Controller;


use Think\Controller;

class BaseController extends Controller{

    public $channel;            //只显示在首页的导航频道
    public $allChannel;         //所有导航
    public $originChannel;      //未处理过得导航数据
    public $pid;                //当前浏览一级频道id
    public $audit;                //当前浏览二级频道ID
    public $config;             //网站配置数据
    /**
     * 初始化
     */
    public function __construct(){
        parent::__construct();

        //初始化公共数据

        $this->pid = I('pid',0);
        $this->cid = I('cid',0);

        $this->setConfig(); //网站配置数据
        $this->setChannel(); //导航数据
        $this->setQuikLink();


        $this->assign('pid',$this->pid);
        $this->assign('cid',$this->cid);
        $this->assign('config',$this->config);
        $this->assign('channel',  $this->channel);
        $this->assign("keyword",I('keyword'));
        $this->assign("isMobile",isMobile());
        //$this->assign('configCss',makeCss($this->config));
        $this->assign("channelBanner",$this->getChannelBanner()); //当前访问导航页面的banner图

    }

    /**
     * 设置网站配置数据
     */
    public function setConfig(){

        $this->config = formatConfig(M('config')->select());

        if($this->config['close']){
            $this->assign('reasion',$this->config['close_reasion']);
            $this->display("index/close");
            exit;
        }
    }

    /**
     * 设置频道数据
     */
    public function setChannel(){

        $field  = ['id','parent_id','name','show_index','show_nav','banner','banner_title',"banner_content"];

        $parents = M('channel')->field($field)->where(['status'=>0,'parent_id'=>0])->order("parent_id asc,sort asc,id asc")->select();
        $this->originChannel = array_column($parents,null,'id');

        foreach($parents as $v){

            $son     = MS("channel")->field($field)->where(['status'=>0,'parent_id'=>$v['id']])->order("parent_id asc,sort asc,id asc")->select();

            foreach($son as $key=>$value){

                $this->originChannel[$value['parent_id']]['son'][$value['id']] = $value;
                $value['son'] = [];
                $this->originChannel[$value['id']] = $value;


            }

            $v['son'] = $son;
            if($v['show_nav'] == 0){
                $this->channel[$v['id']] = $v;
            }
            $this->allChannel[$v['id']] = $v;

        }

    }

    /**
     * 底部快捷链接
     */
    public function setQuikLink(){
        $quikLink = [];
        $quikName = ['联系我们','招生信息','招聘信息','人才引进'];
        foreach($this->channel as $value){
            foreach($value['son'] as $son){
                if(in_array($son['name'],$quikName)){
                    $quikLink[] = $son;
                }

            }
        }
        $this->assign('quikLink',$quikLink);
    }

    /**
     * 根据频道cid获取新闻列表 cid也可以是一级ID或者二级ID
     * @param $cid 频道id
     * 
     */
    public function getArticleByCids($cid,$limit=7){
        if(!isset($this->allChannel[$cid])){
            $where['cid']    = array('eq',$cid); //二级分类
        }else{
            //一级分类
            $sonList = isset($this->allChannel[$cid]['son'])?$this->allChannel[$cid]['son']:array();
            $cids = getArrKeyValue($sonList,'id');
            if(!$cids){
                $cids = [-1]; //防止为空
            }
            $where1['cid'] = array('in',$cids);
            $where1['parent_cid'] = ['eq',$cid];
            $where1['_logic'] = 'or';
            $where['_complex'] = $where1;
        }
        $where['is_del'] = array('eq',0);
        $where['status'] = array('eq',0);
        $where['audit'] = array('eq',1);

        $count   = M("article")->where($where)->count();
        $page    = getpage($count,$limit);
        
        $article = M('article')->where($where)->order("top desc,show_time desc")->limit($page->firstRow.','.$page->listRows)->select();
        return array('article'=>$article,'page'=>$page);
    }

    /**
     * 根据一级分类获或二级分类，获取两篇文章
     * @param $cid
     */
    public function  getArticleTwoByCid($cid,$isParentChannel = false){
        $where['is_del'] = array('eq',0);
        $where['status'] = array('eq',0);
        $where['audit'] = array('eq',1);
        if($isParentChannel){
            $where['parent_cid'] = $cid;
        }else{
            $where['cid'] = $cid;
        }

        return M("article")->where($where)->limit(2)->select();
    }





    /**
     * 根据频道id获取频道信息
     * @param $id
     * @return mixed
     */
    public function getChannelById($id){

        foreach($this->allChannel as $cid=>$channels){
            if($cid == $id){
                return $channels; //一级频道
            }
            foreach($channels['son'] as $channel){
                if($channel['id'] == $id){
                    return $channel; //二级频道
                }
            }
        }
        return [];
    }

    /**
     * 获取频道列表页面导航栏数据
     * @param  $channelType 导航类型 article/filedownload
     */
    public function getChannelItem($channelType = 'article'){
        $channel = $this->allChannel[$this->pid];
        if(!$channel){
            redirect($this->config['domain']);
        }
        foreach($channel['son'] as $k=> $son){
            $channel['son'][$k]['current'] = $this->cid == $son['id'];

            if($channelType == 'filedownload'){
                $channel['son'][$k]['article'] = MS("file")->where(['cid'=>$son['id']])->count();
            }else{
                $channel['son'][$k]['article'] = MS("article")->where(['is_del'=>0,'audit'=>1,'cid'=>$son['id']])->count();
            }


        }

        return ['channel'=>$channel,'latestNews'=>$this->getLatestNews()];
    }


    /**
     * 获取最新文章
     * @param $cids
     */
    public function getLatestNews(){
        $where = [
            'is_del' => ['eq',0],
            'audit' => ['eq',1]
        ];
        $news = MS("article")->field("id,cid,title,content,create_time,thumb")->where($where)->order("id desc")->limit(6)->select();

        foreach($news as $k=>$v){
            $news[$k] = getThumbImage($v,$this->config['article_thumb']); //获取缩略图
            $news[$k]['pid'] = MS('channel')->where(['id'=>$v['cid']])->getField('parent_id');
        }
        return $news;
    }

    public function getChannelBanner(){
        if(!$this->pid){
            return '';
        }

        $banner = $this->allChannel[$this->pid];

        foreach($this->allChannel[$this->pid]['son'] as $son){
            if(($son['id'] == $this->cid) && $son['banner']){
                $banner = $son;///子级banner图覆盖父级
            }
        }
        if(isMobile()){ //手机端页面太小不显示文字
            $banner['banner_title']  =$banner['banner_content'] = '';
        }
        return $banner;//优先子级banner图


    }
}