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
   预约报名
 * @package Admis\Controlle
 *
 */
class HomeApplyController extends  BaseController{

    /**
     * 用户列表
     */
    public function index(){

        if(!I('get')){
            $this->display();
            exit;
        }
        $where = ['status'=>0];
        $count   = MS("customer_apply")->where($where)->count();
        $page    = getpage($count,I('limit'));
        $data = MS('customer_apply')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->ajaxTable($data,$count);
    }


    /**
     * 删除
     */
    public function delete(){
        $res  = MS("customer_apply")->where(['id'=>I("id")])->save(['status'=>1]);
        $this->ajax(0,"删除成功");
    }

    /**
     * 编辑/添加用户
     */
    public function add(){
        $id = I("id");
        $user = MS("customer_apply")->where(['id'=>$id])->find();
        $this->assign('user',$user);
        $this->display();
    }



    /**
     * 编辑/添加用户
     */
    public function editHandle(){
        $user = [
            'name' => I("name"),
            'mobile' => I("mobile"),
            'city' => I("city"),
            'remark' => I("remark"),
        ];

        MS('customer_apply')->where(['id'=>I("id")])->save($user);

        $this->ajaxReturn(['status' =>0,'msg'=>'编辑成功']);

    }

}