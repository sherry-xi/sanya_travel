<?php
/**
 * 用户登陆模块
 * Created by PhpStorm.
 * User: Administrator
 * Time: 14:59
 */

namespace Admis\Controller;

use Think\Controller;
use Think\Verify;
use Lib\DBTool;
use Lib\UserTool;

class UserLoginController extends Controller {

    public $dbTool;
    public $userTool;
    public function __construct()
    {
        parent::__construct();
        $this->dbTool = new DBTool();
        $this->userTool = new UserTool();
    }

    /**
     * 登录页面视图
     */
    public function index(){

        if($this->userTool->isLogin()){
            redirect(U('Admis/Index/index'));
        }
        $adminName = MS('admin')->where(['status'=>0,'is_test'=>0,'role_id'=>1])->getField('truename');

        $this->assign("admin",$adminName);
        $this->assign('loginCode',$this->dbTool->getVerificationConf());
        $this->assign('config',$this->dbTool->getConfig());
        $this->display();
    }

    /**
     * 验证码获取
     */
    public function verify(){

        $config =    array(
            'imageW'    => 150,
            'imageH'    => 35,
            'fontSize'  => 20,    // 验证码字体大小
            'length'    => 4,     // 验证码位数
        );
        $Verify = new Verify($config);
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }
    /**
     * 用户登录
     */
    public function loginHandle(){

        if(!checkToken() || !IS_POST){
            $this->error("非法访问",U('index'));
        }

        //验证码校验
        if($this->dbTool->getVerificationConf()){
            $verify = new Verify();
            if(!$verify->check(I('post.code'))){
                $this->error('验证码错误');
            }
        }
        $username = I("username");
        $password = I("password");
        $remember = intval(I("remember"));

        $res = $this->userTool->login($username,$password);
        if($res !== true){ //登录失败
            $this->error($res);
        }
        if($remember){ //保持登录
            $this->userTool->rememberLogin($username); //取消保持登录
        }else{
            $this->userTool->clearRememberLogin($username); //取消保持登录
        }
        redirect(U('Index/index'));
    }
}