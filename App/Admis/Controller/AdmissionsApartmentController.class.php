<?php
namespace Admis\Controller;

/** 招生管理 报名层次管理
 * Class ApartmentController
 *  节点：
 *      AdmissionsApartment 报名层次管理
 *          index 报名层次列表
 *          add addHandle添加报名层次
 *          edit editHandle 编辑报名层次
 *          delete 删除报名层次
 */
class AdmissionsApartmentController extends  BaseController {

    private $table = "student_apartment";
    /**
     * 首页 报名层次
     */
    public function index(){
        $this->viewDisplay();
        $aparment = M($this->table)->where(['status'=>0])->order("id")->select();
        $data = [];
        foreach($aparment as $k=>$v){

            $major = $this->dbTool->getMajorByApartmentId($v['id']);
            $data[] = [
                'id'      => $v['id'],
                'name'    => $v['name'],
                'major'   => implode(',',array_column($major,'name')),
                'number'  => M("student")->where(['apartment_id'=>$v['id'],'status'=>0])->count()
            ];
        }

        $this->ajaxTable($data);

    }

    /**
     * 添加/
     */
    public function add(){
        $this->display('add');
    }

    /**
     * 编辑报考层次
     */
    public function edit(){

        $apartment = M($this->table)->where(['id'=>$this->param->id])->find();
        $major = $this->dbTool->getMajorByApartmentId($this->param->id);
        $this->assign('apartment',$apartment);
        $this->assign('majorIds',implode(',',array_column($major,'id')));
        $this->assign('majorNames',implode(',',array_column($major,'name')));

        $this->display('add');
    }



    /**
     * 编辑添加报名层次 处理
     */
    public function addHandle(){

        $apartment = M($this->table)->where(['name'=>$this->param->name,'status'=>0])->find();
        if($apartment){
            $this->ajax(1,'添加失败，报名层次已经存在');
        }else{
            $id = M($this->table)->add(['name'=>$this->param->name]);
            $this->updateApartmentMajor($this->param->id,$this->param->majorIds);
            $this->ajax(0,'添加成功');
        }

    }

    /**
     * 编辑报名层次
     */
    public function editHandle(){
        $apartment = M($this->table)->where(['name'=>$this->param->name,'status'=>0])->find();

        if($apartment && ($apartment['id'] != $this->param->id)){
            $this->ajax(1,'编辑失败，报名层次已经存在');
        }else{
            M($this->table)->where(['id'=>$this->param->id])->save(['name'=>$this->param->name]);
            $this->updateApartmentMajor($this->param->id,$this->param->majorIds);
            $this->ajax(0,'编辑成功');
        }

    }


    /**
     * 获取专业信息
     */
    public function getMajor(){
        $where['status'] =0;
        $name = I('name');
        if($name){
            $where['name'] = ['like',"%$name%"];
        }
        $this->ajaxTable(M("student_major")->where($where)->select());
    }


    /*
     * 更新 报名层次的专业
     */
    private function updateApartmentMajor($apartmentId,$majorIds){
        if(I('oldMajorIds') == $majorIds){
            return false; //无更新
        }
        $majorIds = explode(',',$majorIds);

        M("student_apartment_major")->where(['apartment_id'=>$apartmentId])->delete();
        $data = [];
        foreach($majorIds as $id){
            $data[] = [
                'apartment_id' => $apartmentId,
                'major_id'     => $id
            ];
        }
        //更新学生报名的专业
        M("student")->where(['apartment_id'=>$apartmentId,'major_id'=>['not in',$majorIds]])->save(['major_id'=>0]);
        M("student_apartment_major")->addAll($data);

    }

    /**
     * 删除报名层次
     */
    public function delete(){
        M($this->table)->where(['id'=>I('id')])->save(['status'=>1]);
        M("student")->where(['apartment_id'=>I('id')])->save(['apartment_id'=>0]);
        $this->ajax(0,'删除成功');
    }

}
?>