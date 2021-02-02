<?php
namespace Lib;
/**
 * 用户相关操作方法
 * Class DataTool
 *
 */
class UserTool{

    /**
     * 登录检查
     */
    public function isLogin(){
        if(!isset($_SESSION)){
            session_start(); //开启session
        }

        if(session('user')){
           return true;
        }

        //没登录查看是否有保持登录信息
        $username = $this->getRememberLogin();
        if(!$username){
            return false;
        }

        //有的话自动登录
        $user = MS("admin")->where(['username'=>$username])->find();
        if(!$user){
            return false;
        }

        //登录成功记录session信息
        unset($user['password']);
        session('user',$user);
        $this->userLog();
        return true;
    }

    /**
     * 权限校验
     */
    public function permissionCheck(){

         $id = MS("menu")->where(['mode'=>CONTROLLER_NAME,'method'=>ACTION_NAME])->getField('parent_id');
         if(!$id){
             return true; //没有权限现在的模块
         }
         return in_array($id,$this->getUser()['menu']);
    }

    /**
     * 获取用户信息 不用登录session里面的信息防止 信息变更 用户没有重新登录
     * @return mixed
     */
    public function getUser(){
        $user = MS('admin')->field('password',true)->where(["id" => session('user')['id']])->find();
        $user['role'] = MS('admin_role')->where(['id'=>$user['role_id']])->find();
        $user['menu'] = explode(',',$user['role']['menu']);
        $user['isNewsPoster'] = $user['role']['level'] == 3;
        return $user;
    }

    /**
     * 获取所有用户
     */
    public function getAllUser(){
        $user = MS("admin")->join("left join admin_role on admin.role_id = admin_role.id ")
            ->field("admin.id,admin.username,admin.truename,admin_role.name as role")->select();

        return array_column($user,null,'id');
    }

    /**
     * 是否有新闻审核权限
     */
    public function haveArticleAudit(){
        $user = $this->getUser();
        return in_array($user['role_id'],[1,2]);
    }

    /**
     * 用户日志记录
     * @return bool
     */
    public function userLog(){
        if(ACTION_NAME == 'verify'){
            return false; //验证码不记录日志
        }
        $param = array_merge($_GET,$_POST);
        foreach($param as $k=>$v){
            if($k >100){
                //参数数量太大也不要了
                break;
            }
            if(in_array($k,array('password'))){
                unset($param[$k]) ; //不记录一些敏感数据
                continue;
            }
            if(strlen($v) > 200){ //太长的参数不要了
                unset($param[$k]);
            }

        }
        $log = array(
            'admin_id'  => intval(session('user.id')),
            'ip'        => get_client_ip(),
            'controller'=> CONTROLLER_NAME,
            'action'    => ACTION_NAME,
            'param'     => json_encode($param),
        );

        MS('admin_log')->add($log);
    }

    /**
     * @param $roleId
     */
    public function getRoleById($roleId){
        return MS("permission_role")->where("id",$roleId)->find();
    }

    /**
     * 登录操作
     * @param $username 账号
     * @param $password 密码
     * @return  false 验证失败 string 失败原因
     */
    public function login($username,$password){
        if(!checkUsername($username) || !checkPassword($password)){
            return "登陆失败,账号或密码错误";
        }
        $where = array("username"=>$username);
        $user  = MS("admin")->where($where)->find();

        if(!$user ||$user['password'] != md5($password)){
            return "登陆失败,账号或密码错误";

        }
        if($user['status']==1){
            return   "登陆失败,您的账号已被禁用";

        }
        //登录成功记录session信息
        unset($user['password']);
        session('user',$user);
        $this->userLog();

        return true;
    }

    /**
     * 保持登录
     * @param $username
     */

    public function  rememberLogin($username){
        $cookieName = 'rememberLogin_'.md5(get_client_ip());
        cookie($cookieName,strrev($username),86400*1); //1天免登录
    }

    /**
     * 取消保持登录
     * @param $username
     */
    public function clearRememberLogin($username){
        $cookieName = 'rememberLogin_'.md5(get_client_ip());
        cookie($cookieName,null); //删除cookie
    }

    /**
     * 获取保持登录信息
     */
    public function getRememberLogin(){
        $cookieName = 'rememberLogin_'.md5(get_client_ip());
        return strrev(cookie($cookieName));
    }

    /**
     * 退出登陆
     */
    public function loginOut(){
        $this->clearRememberLogin(session('user')['username']); //清除保持登录信息

        session_destroy();      //清空以创建的所有SESSION
        session_unset("user");  //清空指定的session

        if(isset($_SESSION) && isset($_SESSION['user'])){
            unset($_SESSION["user"]);//清空指定的session
        }
        return true;
    }
}
