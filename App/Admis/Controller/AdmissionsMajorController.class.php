<?php
namespace Admis\Controller;

/**
 * 专业管理
 * Class ApartmentController
 *   AdmissionsMajor 专业管理
 *      index   专业列表
 *      add,addHandle 添加专业
 *      edit,editHandle 编辑专业
 *      delete 删除专业
 *      
 */
class AdmissionsMajorController extends  BaseController {

    private $table = "student_major";

    /**
     * 专业列表
     */
    public function index(){
        $this->viewDisplay();

        $major = M($this->table)->where(['status'=>0])->order("id")->select();

        foreach($major as $k=>$v){
            $major[$k]['number']  = M("student")->where(['major_id'=>$v['id'],'status'=>0])->count();
        }
        $this->ajaxTable($major);


    }

    /**
     * 添加编辑专业
     */
    public function add(){
        $this->display();
    }

    /**
     * 添加专业 处理
     */
    public function addHandle(){

        $major = M($this->table)->where(['name'=>$this->param->name,'status'=>0])->find();
        //添加
        if($major){
            $this->ajax(1,'添加失败，专业已经存在');
        }else{
            M($this->table)->add(['name'=>$this->param->name]);
            $this->ajax(0,'添加成功');
        }

    }

    //编辑专业
    public function edit(){
        $this->assign($this->table,M("major")->where(["id"=>I('id')])->find());
        $this->display('add');
    }

    //编辑专业
    public function editHandle(){

        $major = M($this->table)->where(['name'=>$this->param->name,'status'=>0])->find();
        if($major && ($major['id'] != $this->param->id)){
            $this->ajax(1,'编辑失败，专业已经存在');
        }else{
            M($this->table)->where(['id'=>$this->param->id])->save(['name'=>$this->param->name]);
            $this->ajax(0,'编辑成功');
        }
    }

    /**
     * 删除专业
     */
    public function delete(){
        M($this->table)->where(['id'=>I('id')])->save(['status'=>1]);
        M("student")->where(['major_id'=>I('id')])->save(['major_id'=>0]);
        $this->ajax(0,'删除成功');
    }


}
?>