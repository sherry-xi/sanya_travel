<?php
/**
 * 后台首页
 */
namespace Admis\Controller;
use Admis\Controller\BaseController;

/**
 * Class IndexController
 * @package Admis\Controller
 *  Index 后台
 *      index ,welcome 后台首页
 */
class IndexController extends BaseController{

    /**
     * 后台首页
     */
    public function index(){

        $this->display();
    }

    public function welcome(){

        //用户日志
        $where['action'] = ['in',['loginHandle']];
        $logs  = MS("admin_log")->field('ip,create_time,action,admin_id')->where($where)->order('id desc ')->limit(10)->select();
        foreach($logs as $k=>$v){
            $logs[$k]['username'] = MS('admin')->where(['id'=>$v['admin_id']])->getField('username');
        }

        //最新文章
        $latestNews = MS("article")
                ->field("id,cid,title,create_time")
                ->where(['is_del'=>0,'audit'=>1])
                ->order("id desc")->limit(8)->select();
        foreach($latestNews as $k=>$v){
            $latestNews[$k]['pid'] = MS("channel")->where(['id'=>$v['cid']])->getField('parent_id');
        }

        $articleCnt     = MS("article")->where(['is_del'=>0,'audit'=>1])->count(); //当前文章总数
        $articleAuditCnt = MS("article")->where(['is_del'=>0,'audit'=>0])->count(); //audit 待审核文章



        $viewCnt = MS("article_log")->count();//文章总浏览数量
        $viewYestodayCnt = MS("article_log")->where(['datetime'=>date('Ymd',strtotime("-1 days"))])->count();//昨日总浏览数量

        $this->assign('viewCnt',$viewCnt);
        $this->assign('viewYestodayCnt',$viewYestodayCnt);
        $this->assign('articleAuditCnt',$articleAuditCnt);
        $this->assign('articleCnt',$articleCnt);
        $this->assign('latestNews',$latestNews);
        $this->assign('logs',$logs);
        $this->display();
    }

    /**
     * 获取菜单数据
     */
    public function getInitData(){
        $data = [
                'homeInfo' => [
                    'title'=>'首页',
                    'href' => U('welcome')
                    ],
                'logoInfo' => [
                    'title' => '旅游与康体产业学院',
                    'image' => "/Public/image/logo_index.png",
                    'href' => U('Index')
                    ],
                'menuInfo' => [
                    [
                        'title' => '我的功能',
                        "icon" =>  "fa fa-address-book",
                        "href" => "",
                        "target" => "_self",
                        'child' => $this->dbTool->getMenu()
                    ]
                ]
        ];
        echo  json_encode($data);
    }
    
}