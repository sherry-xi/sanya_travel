<?php
namespace Admis\Controller;
use Admis\Controller\BaseController;


/**
 * 菜单管理
 * Class MenuController
 *  Menu    菜单管理
 *  index 菜单列表
 *   add，addHandle 编辑添加菜单
 *   delete 删除菜单
 */
class MenuController extends BaseController {

    /**
     * 菜单列表
     */
    public function index(){
        if(!I('get')){
            $this->display(); //显示视图
            exit;
        }
    }

    /**
     * 添加/编辑菜单 视图
     */
    public function add(){

    }

    /**
     * 添加编辑菜单 处理
     */
    public function addHanle(){

    }

    /**
     * 删除菜单
     */
    public function delete(){

    }
}