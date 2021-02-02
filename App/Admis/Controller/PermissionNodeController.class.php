<?php
namespace Admis\Controller;
use Admis\Controller\BaseController;

/**
 * 权限节点管理
 * Class PermissionNodeController
 * @package Admis\Controller
 *      PermissionNode 权限节点管理
 *          index 节点列表
 *      add,addHandle,edit,editHandle 添加编辑节点
 */
class PermissionNodeController extends BaseController{

    private $table = 'Permission_node';
    /**
     * 节点列表
     */
    public function index(){
        $this->viewDisplay();
        $parents = MS($this->table)->where(['parent_id'=>0])->order("sort","asc")->select();
        $data = [];
        foreach($parents as $v){
            $data[] = $v;
            $son = MS($this->table)->where(['parent_id'=>$v['id']])->order("sort","asc")->select();
            $data = array_merge($data,$son);
        }
        $this->ajaxTable($data);
    }

    /**
     * 添加节点
     */
    public function add(){
        $this->assign('parent',MS($this->table)->where(['id'=>$this->param->id])->find());
        $this->display();
    }

    /**
     * 处理添加节点
     */
    public function addHandle(){

        $id = MS($this->table)->where(['action'=>$this->param->action,'method'=>$this->param->method])->getField('id');
        if($id){
            $this->ajax(1,'该节点已经存在');
        }

        MS($this->table)->add([
            'name'      => $this->param->name,
            'action'    => $this->param->action,
            'method'    => $this->param->method,
            'sort'      => $this->param->sort,
            'parent_id' => intval($this->param->parent_id),
        ]);

        $this->ajax(0,'添加成功');
    }

    /**
     * 编辑节点
     */
    public function edit(){
        $this->assign('data',MS($this->table)->where(['id'=>$this->param->id])->find());
        $this->display('add');
    }

    /**
     * 处理编辑节点
     */
    public function editHandle(){

        $id = MS($this->table)->where(['action'=>$this->param->action,'method'=>$this->param->method])->getField('id');

        if($id && ($id != $this->param->id)){
            $this->ajax(1,'该节点已经存在');
        }

        MS($this->table)->save([
            'name'      => $this->param->name,
            'action'    => $this->param->action,
            'method'    => $this->param->method,
            'sort'      => $this->param->sort,
            'id' => intval($this->param->id),
        ]);

        $this->ajax(0,'编辑成功');
    }

    /**
     * 删除节点
     */
    public function delete(){
        //删除子节点 删除节点和角色表

        $sonIds = MS($this->table)->where(['parent_id'=>$this->param->id])->field('id')->select();
        $nodeIds = [$this->param->id];

        if($sonIds){
            MS($this->table)->where(['parent_id'=>$this->param->id])->delete();
            $nodeIds = array_merge($nodeIds,array_column($sonIds,'id'));
        }
        MS("permission_role_node")->where(['node_id'=>['in',$nodeIds]])->delete(); //删除角色关联
        MS($this->table)->where(['id'=>$this->param->id])->delete();
        $this->ajax(0,'删除成功');
    }
}
?>