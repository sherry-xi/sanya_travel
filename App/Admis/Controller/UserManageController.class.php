<?php
/**
 * 用户管理
 * Created by PhpStorm.
 * User: Aous
 * Date: 2017/8/20
 * Time: 16:36
 */

namespace Admis\Controller;

/**
 * Class UserManageController
 * @package Admis\Controller
 *   UserManage 用户管理
 *    index 用户列表
 *     log 用户日志
 *     add,addHandle 添加和编辑用户
 *
 */
class UserManageController extends  BaseController{

    /**
     * 用户列表
     */
    public function index(){

        if(!I('get')){
            $this->display();
            exit;
        }
        $users  = MS("admin")->join("left join admin_role on admin.role_id = admin_role.id")
                ->field(['admin.id',"admin.username","admin.truename","admin_role.name as role"])
                ->where(['admin.status'=>0])->order("admin.id desc")->select();
        $this->ajaxTable($users);
    }

    /**
     * 用户日志 登录和退出日志
     */
    public function log(){

        if(!I('get')){
            $this->display();
            exit;
        }

        $where = ['admin_log.action' => ['in',['loginHandle','loginOut']]];
        $field = [
            "admin_log.id","admin_log.action","admin_log.create_time",
            "admin_log.ip","admin.username","admin.truename",
            "admin.role_id","admin_role.name as role"
        ];

        $rightJoin = " right join admin on admin_log.admin_id = admin.id";
        $leftJoin  = "left join admin_role on admin.role_id = admin_role.id";
        $count     = M("admin_log")->join($rightJoin)->join($leftJoin)->where($where)->count();
        $page      = getpage($count,I("limit"));
        $logs      = M("admin_log")->join($rightJoin)->join($leftJoin)->where($where)->field($field)
                     ->limit($page->firstRow.','.$page->listRows)->order("admin_log.id desc")->select();

        $i=0;
        foreach($logs as $k=>$v){
            $logs[$k]['id']    = ++$i +$page->listRows*($page->nowPage-1);
            $logs[$k]['action'] =  $v['action']=='loginHandle'?'登录':'退出';
        }

        $this->ajaxTable($logs,$count);
    }


    /**
     * 用户删除
     */
    public function delete(){

        $id = I('id');
        if($id == $this->user['id']){
            $this->ajax(1,"不能删除自己");
        }
        $where = ['id'=>$id,'status'=>0];

        if(MS('admin')->where($where)->getField('role_id') == $this->user['role_id']){
            $this->ajax(1,'您无权限删除同级用户');
        }
        $res  = MS("admin")->where($where)->delete();
        $this->ajax(0,"删除成功");
    }

    /**
     * 编辑/添加用户
     */
    public function add(){
        $id = intval(I('get.id'));
        $user = array();
        if($id){
            $user = MS("admin")->field('password',true)->where(array('id'=>$id))->find();
        }

        $this->assign('roles',MS("admin_role")->order("id desc")->select());
        $this->assign('user',$user);
        $this->assign('userId',$id);
        $this->display();
    }



    /**
     * 编辑/添加用户
     */
    public function editHandle(){

        $res = ['status' =>0,'msg'=>''];

        $id         = intval(I('id'));
        $username   = trim(I('username'));
        $user = array(
            'truename'   => I('truename'),
            'role_id'   => I("role_id")

        );
        if(I('password')){
            $user['password'] = md5(I('password'));
        }

        if($id){
            $user['id'] = $id;
            MS('admin')->where(['id'=>$id])->save($user);
            $res['msg'] = '编辑成功';
        }else{
            //检查账号是否已经存在了
            $exitUser = MS('admin')->where(array('username'=>$username))->find();
            if($exitUser){
                $res['status'] = 1;
                $res['msg']   = '添加失败，账号已存在';
            }else{
                $user['username'] = $username;
                $user['create_time'] = date("Y-m-d H:i:s");
                 MS('admin')->add($user);
                $res['msg'] = '添加成功';
            }
        }
        $this->ajaxReturn($res);

    }

}