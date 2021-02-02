<?php
/**基类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 14:50
 */

namespace Admis\Controller;


use Think\Controller;
use Lib\DBTool;
use Lib\UserTool;

class BaseController extends Controller{

    public $config;
    public $menuId;

    public $dbTool;
    public $userTool;
    public $user;

    public $param; //请求数


    /**
     * 初始化
     */
    public function __construct(){

        parent::__construct();

        $this->dbTool   = new DBTool();
        $this->userTool = new UserTool();


        $this->userTool->userLog();
        $this->saleCheck(); //安全检查
        $this->init(); //初始化公共数据

    }

    /**
     * 安全校验  登陆验证 权限校验
     */
    public function saleCheck(){
        if(!$this->userTool->isLogin()){
            redirect(U('Admis/UserLogin/index'));
        }
        if(!$this->userTool->permissionCheck()){
            $this->error('您无权限访问',U('Admis/User/profile'));
        }
    }

    /**
     * 初时候公共数据
     */
    public function  init(){
        $this->pid = intval(I('pid'));
        $this->mid = intval(I('mid'));
        $this->config = $this->dbTool->getConfig();
        $this->menuId = array('mid'=>$this->mid,'pid'=>$this->pid);
        $this->user = $this->userTool->getUser();



        //设置请求参数
        foreach(I("") as $key=>$value){
            if(in_array($key,['token'])){

            }
            $this->param->$key = $value;
        }

        if(!IS_GET){
            return true;
        }
        if(I('get') == 1){ //layui表格数据 请求 layui 使用page 参数，但thinkphp 分页使用p 这里自动转换
            if(I('page') && !I('p')){
                $_GET['p'] = I('page');
            }

        }

        //get 请求时候 初始化数据
        //token设置
        if(!I("get.token") && ACTION_NAME!='verify'){    //请求链接中有token不再设置
            $this->assign('token',makeToken());
        }
        //提示信息设置
        $this->assign('msg',session('msg'));
        session('msg',array());

        //分配请求参数
        $this->assign('get',$_GET);
        $this->assign('mid',$this->mid);
        $this->assign('pid',$this->pid);
        $this->assign('menuId',$this->menuId);
        //分配配置参数

        $this->assign('config',$this->config);
        $this->assign('perPageCount',10); //默认分页数据 每页10条

        $this->assign("theme",C("theme")); //主题风格
        $this->assign('user',$this->user);
    }


    /**
     * 获取频道
     */
    public function getChannel(){
        $where['status'] = 0;

        $result = MS('channel')->field(array('id,parent_id,name,classify'))->where($where)->order("parent_id asc,sort asc,id asc")->select();
        $channel = array();
        foreach($result as $k=>$v){
            if(!$v['parent_id']){
                $channel[$v['id']] = $v;
            }else{

                $channel[$v['parent_id']]['son'][] = $v;

            }
        }
        return $channel;
    }
    /**
     * 获取频道 列表 返回一级数据
     */
    public function getChannelList(){

        $result = MS('channel')->field(array('id,parent_id,name,classify'))->where(['status'=>0])->order("parent_id asc,sort asc,id asc")->select();
        $result = array_column($result,null,'id');

        foreach($result as $k=>$v){
            if($v['parent_id']){
                $result[$k]['allname'] = $result[$v['parent_id']]['name'].' > ' .$v['name'];
            }else{
                $result[$k]['allname'] = $v['name'];
            }
        }
        return $result;
    }


    /**
     * 设置 我的位置
     * @param $key  关键字 为空自动获取当前控制器和方法
     * @param $prePosi 前面的位置信息
     * @deprecated
     */
    public function setPos($prePosi = array()){
        $position = array();

        //前面位置
        foreach($prePosi as $k=>$v){
            $key   = $v;        //关键字
            $param = array();   //参数

            if(is_array($v)){   //数组类型
                $key   = $k;
                $param = $v;
            }
            $link = '';

            $controller = str_replace('-','/',$key);
            $link = U($controller,$param);
            $position[] = array('name'=>C('pos.'.$key),'link'=>$link);
        }

        //当前位置
        $action = CONTROLLER_NAME.'-'.ACTION_NAME;
        $position[] = array(
            'name'=>C('pos.'.$action),'link'=>U(CONTROLLER_NAME.'/'.ACTION_NAME,$_GET)
        );
        $this->assign("pos",$position);
    }

    /**
     * 页面提示 用户操作后跳转并设置提示信息
     * @param $msg  提示信息
     * @param $errcode 状态码 0成功 1失败
     * @param $url  $url
     */
    public function message($msg='',$errcode=0,$url=''){
        if($msg == ''){
            $errcode = 1; //没有提示信息 怎是失败
        }
        if(!$msg){
            $msg = $errcode==0?'操作成功':C('MESSAGE_ERR');
        }
        if(!$url){
            $url =  $_SERVER['HTTP_REFERER']; //上一页
        }
        if(!$url){
            $this->error("页面不存在",U("Home/Index/index"));
        }

        session("msg",array('msg'=>$msg,'errcode'=>$errcode));
        header("Location:{$url}");
        exit;
    }


    /**
     * 获取报名层次
     * @deprecated
     */
    public function getApartment(){
        $apartment = MS("apartment")->where(['status'=>0])->order("id desc")->select();
        $apartment = array_column($apartment,null,'id');
        foreach($apartment as $id=>$v){
            $major = MS("major")->where(['status'=>0,'apartment_id'=>$v['id']])->order("id desc")->select();
            $major = array_column($major,null,'id');
            $apartment[$id]['major'] = $major;
        }
        return $apartment;
    }

    /**
     * 返回ajax
     * @param $status 0 成功 1失败....
     * @param $msg
     * @param array $data
     */
    public function ajax($status=0,$msg='操作成功',$data=[]){
        $res = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data
        ];
        $this->ajaxReturn($res);

    }

    /**
     * 返回表格插件数据
     * @param $data
     * @param int $count
     * @param int $code
     * @param string $msg
     */
    public function ajaxTable($data,$count=0,$code=0,$msg=''){

        $this->ajaxReturn(
            [
                "code"  => $code,
                "msg"   => $msg,
                "count" => $count?$count:count($data),
                'data'  => $data
            ]
        );
    }

    /**
     * 显示视图页面
     */
    public function viewDisplay(){
        if(IS_GET && (I('get') != 1)){ //只有在 layertable 页面请求
            $this->display();
            exit;
        }
    }


}





