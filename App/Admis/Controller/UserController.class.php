<?php
/**
 * 用户模块
 * Created by PhpStorm.
 * User: Administrator
 * Time: 14:59
 */

namespace Admis\Controller;

use Think\Verify;

/**
 * Class UserController
 * @package Admis\Controller
 *   User 个人资料
 *   profile 查看个人资料
 *  edit，profileEditHandle 编辑个人资料
 *  password,passwordHandle 密码修改
 */
class UserController extends BaseController{

    /**
     * 个人资料查看
     */
    public function profile(){
        $where['action'] = ['in',['loginHandle','loginOut']];
        $logs  = MS("admin_log")->field('ip,create_time,action')->where($where)->order('id desc ')->limit(10)->select();

        $this->assign('logs',$logs);
        $this->display();
    }

    /**
     * 修改账号信息视图
     */
    public function edit(){
        $this->assign('user',$this->userTool->getUser());
        $this->display();
    }

    /**
     * 修改账号信息
     */
    public function profileEditHandle(){
        if(!checkToken() || !IS_POST){
            $this->message();
        }
        $password = I('password');
        $data = array(
            'truename'  => trim(I("post.truename")),
        );
        if($password){
            $data['password'] = md5(I("password"));
        }
        if($password){
            $same = MS("admin")->where(['id'=>session('user')['id'],'password'=>md5($password)])->getField("id");
            if($same){
                $this->ajax(1,"新密码与旧密码一致");
            }
        }
        $res = MS("admin")->where(['id'=>I('id')])->save($data);
        if($password){
            $this->ajax(2,"密码修改成功请重新登陆吧");
        }else{
            $this->ajax(0,"修改成功");
        }

    }

    /**
     * 密码修改
     * @deprecated
     */
    public function password(){
        $this->setPos(array('User-profile'));
        $this->display();
    }

    /**
     * 密码修改 处理
     * @deprecated
     */
    public function passwordHandle(){

        if(!checkToken() || !IS_POST){
            $this->message();
        }
        $oldPassword = I("post.oldPassword");
        $newPassword = I("post.newPassword");
        $where = array('id'=>session('user.id'),'password'=>md5($oldPassword));
        $id = MS("admin")->field("id")->where($where)->find();

        if(!$id){
            $this->message('旧密码错误',1);
        }

        $res = MS("admin")->where($where)->save(array('password'=>md5($newPassword)));
        if($res !== false){
            $this->message('密码修改成功',0,U("User/profile"));
        }else{
            $this->message('密码修改失败,请刷新再试试');
        }
    }
    /**
     * 用户退出登录
     */
    public function loginOut(){
        $this->userTool->loginOut();
        redirect(U('UserLogin/index'));
    }

}