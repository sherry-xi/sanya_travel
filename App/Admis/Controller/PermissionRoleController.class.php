<?php
namespace Admis\Controller;
use Admis\Controller\BaseController;

/**
 * 权限角色管理
 * Class PermissionRoleController
 * @package Admis\Controller
 *  PermissionRole 角色管理
 *  index  角色列表
 *  add,addHandle ,edit,editHandle添加编辑角色
 *  delete 删除角色
 */
class PermissionRoleController extends BaseController{

    private $table = 'Permission_role';
    /**
     * 角色列表
     */
    public function index(){

        $this->viewDisplay();

        $role = MS($this->table)->where(['status'=>0])->select();

        foreach($role as $k=>$v){
            $nodes = $this->dbTool->getNodesByRoleId($v['id']);

            foreach($nodes as $node){
                if($node['parent_id'] == 0){
                    foreach($nodes as $son){
                        if($son['parent_id'] == $node['id']){
                            $node['son'][] = $son;
                        }
                    }
                    $role[$k]['permission'][] = $node;
                }
            }
        }
        $this->ajaxTable($role);

    }

    /**
     * 添加角色
     */
    public function add(){
        $nodes = MS("permission_node")->where(['parent_id'=>0,'status'=>0])->order("sort",'asc')->select();
        foreach($nodes as $k=>$v){
            $nodes[$k]['son'] = MS("permission_node")->where(['parent_id'=>$v['id'],'status'=>0])->order("sort",'asc')->select();
        }
        $this->assign('nodes',$nodes);
        $this->display();
    }

    /**
     * 处理添加角色
     */
    public function addHandle(){

        $roleId = MS($this->table)->add([
            'name' => $this->param->name,
            'remark' => $this->param->remark,
        ]);

        $data = [];
        foreach($this->param->node as $v){
            $data[] = [
                'role_id' => $roleId,
                'node_id' => $v,
            ];
        }
        MS("permission_role_node")->addAll($data);
        $this->ajax(0,'添加成功');
    }

    /**
     * 编辑角色
     */
    public function edit(){

        $role = MS($this->table)->where(['id'=>$this->param->id])->find();
        $nodeIds = array_column($this->dbTool->getNodesByRoleId($role['id']),'id');

        $nodes = MS("permission_node")->where(['parent_id'=>0,'status'=>0])->order("sort",'asc')->select();
        foreach($nodes as $k=>$v){
            $nodes[$k]['checked'] = in_array($v['id'],$nodeIds);
            $son = MS("permission_node")->where(['parent_id'=>$v['id'],'status'=>0])->order("sort",'asc')->select();
            foreach($son as $j=>$value){
                $son[$j]['checked'] = in_array($value['id'],$nodeIds);
            }
            $nodes[$k]['son'] = $son;
        }

        $this->assign('nodes',$nodes);
        $this->assign('data',$role);

        $this->display('add');
    }

    /**
     * 处理编辑角色
     */
    public function editHandle(){
        MS($this->table)->save([
            'id'   => $this->param->id,
            'name' => $this->param->name,
            'remark' => $this->param->remark,
        ]);

        $data = [];
        foreach($this->param->node as $v){
            $data[] = [
                'role_id' => $this->param->id,
                'node_id' => $v,
            ];
        }
        MS("permission_role_node")->where(['role_id'=>$this->param->id])->delete();
        MS("permission_role_node")->addAll($data);
        $this->ajax(0,'编辑成功');
    }

    /**
     * 删除角色
     */
    public function delete(){
        MS($this->table)->where(['id'=>$this->param->id])->delete();
        MS("permission_role_node")->where(['role_id'=>$this->param->id])->delete();
        $this->ajax(0,'删除成功');
    }
}
?>