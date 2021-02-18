<?php
/**
 * 文章
 * Created by PhpStorm.
 * User: Aous
 * Time: 20:37
 */

namespace Admis\Controller;


use Lib\Upload;

class ArticleController extends BaseController{


    /**
     * 文章列表
     */
    public function index(){
        $this->indexCommon();
    }

    public function indexCommon($isDel=0){
        $where   = [];
        $cid     = I('cid');

        $audit   = intval(I("audit",-1));
        $keyword = trim(I('keyword'));

        $this->assign('channel',$this->getChannel());
        $this->assign('cid',$cid);
        $this->assign('keyword',$keyword);
        $this->assign('audits',C('audit'));
        $this->assign('audit',$audit);


        if(!I('get')){
            //一次性提示信息
            $msg = session("articleHandleMsg");
            unset($_SESSION[C("SESSION_PREFIX")]['articleHandleMsg']);

            $this->assign('articleHandleMsg',$msg);
            $this->display();
            exit;
        }



        if($cid){
            if(strpos($cid,'parent_')!==false){
                $cid = intval(str_replace('parent_','',$cid));
                $cids = MS("channel")->where(array('parent_id'=>$cid))->getField('id',true);
                $where1['cid'] = array('in',$cids?$cids:[-1]);
                $where1['parent_cid'] = ['eq',$cid];
                $where1['_logic'] = 'or';
                $where['_complex'] = $where1;
            }else{
                $where['cid'] = array('eq',$cid);
            }
        }

        $where['is_del'] = ['eq',$isDel];
		if($audit !=-1){
            $where['audit'] = array('eq',$audit);
        }

        if($keyword){
            if(is_numeric($keyword)){
                $where['id'] = array("eq",$keyword);
            }else{
                $where['title'] = array("like","%{$keyword}%");
            }

        }
        $count   = MS("article")->where($where)->count();
        $page    = getpage($count,I('limit'));
        $article = MS('article')->where($where)->limit($page->firstRow.','.$page->listRows)->order("top desc,id desc")->select();

        $channel = $this->getChannelList();

        $top = ['不置顶','置顶'];


        $users = $this->userTool->getAllUser();
        $i = 0;
        foreach($article as $k=>$v){

            $user = $users[$v['admin_id']];
            $v['show_id']    = ++$i +$page->listRows*($page->nowPage-1);
            $v['navigator'] = $channel[$v['cid']]['allname'];
            $v['pid']       = $channel[$v['cid']]['parent_id'];
            $v['cid']       = $v['cid'];
            $v['top'] = $top[$v['top']];
            $v['audit'] = C('audit')[$v['audit']];
            $v['username'] = $user['truename'];
            $v['create_time'] = substr($v['create_time'],0,16);
            $v['viewCnt'] = MS('article_log')->where(['article_id'=>$v['id']])->count();
            $article[$k] = $v;


        }

        $this->ajaxTable($article,$count);

    }
    /*
     * 文章回收站
     */
    public function recycle(){
        $this->indexCommon(1);
    }

    /**
     * 添加文章页面
     */
    public function addArticle(){
        $id = intval(I('id'));

        if($id){
		
            $article = MS('article')->where(array('id'=>$id))->find();
            $this->assign('article',$article);
        }

        //一次性提示信息
        $msg = session("articleHandleMsg");
        unset($_SESSION[C("SESSION_PREFIX")]['articleHandleMsg']);
        $this->assign('articleHandleMsg',$msg);


        $this->assign('from',I('from'));
        $this->assign('channel',$this->getChannel());
        $this->display();
    }

    /**
     * 添加文章处理
     */
    public function addArticleHandle(){
        if(!checkToken() || !IS_POST){
            $this->message();
        }
        $id   = intval(I('id'));
        $copy = intval(I('copy'));

        $data = array(
            'title'     => trim(I('title')),
            'admin_id'  => session('user')['id'],
            'top'       => I('top','') == 'on'?1:0,
            'content' => $_POST['content'],
            'thumb' => I('thumb'),
            'banner' => I('banner'),
        );

        if(!$this->user['isNewsPoster']){ //新闻发布员没有审核功能
            $data['audit'] = I('audit',0);
        }


        $cid  = I("cid");

        //文章挂在一级导航下
        if(strpos($cid,'parent_') !== false){
            $data['parent_cid'] = str_replace('parent_','',$cid);
            $data['cid'] = 0;
        }else{
            $data['cid'] = $cid; //文章挂在2级导航下
            $data['parent_cid'] = 0;
        }


        $url = I("from")?I("from"):"addArticle";
        if($id){
            $res  = MS('article')->where(array('id'=>$id))->save($data);
            session('articleHandleMsg',"编辑成功");
        }else{
            $data['create_time'] = date('Y-m-d H:i:s');
            MS('article')->add($data);
            session('articleHandleMsg',"添加成功");
        }

        $this->redirect($url);

    }

    /**
     * 复制文章
     */
    public function copy(){
        $article = MS("article")->where(["id"=>I('id')])->find();
        unset($article['id']);
        MS("article")->add($article);
    }

    /**
     * 删除文章
     */
    public function delete(){
        $id = explode(',',I("id"));

        $where['id'] = ['in',$id];
        MS("article")->where($where)->save(['is_del'=>I('is_del')]);
        $this->ajax(0,'操作成功');
    }

    /**
     * 审核文章
     */
    public function audit(){
        $id = explode(',',I("id"));

        $where['id'] = ['in',$id];
        MS("article")->where($where)->save(['audit'=>I('audit')]);
        $this->ajax(0,'审核成功');
    }


    /**
     * 上传文件
     */
    public function uploadFile(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle.finish('".json_encode($result)."');</script>";
    }
    /**
     * 上传文件
     */
    public function uploadFile2(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle2.finish('".json_encode($result)."');</script>";
    }

}