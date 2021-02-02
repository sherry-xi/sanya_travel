<?php
namespace Admis\Controller;
/**
 * 招生管理 -学生列表
 * Class AdmissionsStudentController
 * @package Admis\Controller
 *  AdmissionsStudent 学生管理
 *      index 学生列表
 *      delete 删除学生
 *      show 查看学生信息
 *      export 导出学生信息
 *      edit editHandle编辑学生信息
 */
class AdmissionsStudentController extends BaseController{

    private $table = "student";

    public function index(){

        $this->viewDisplay();

        $where = ['status'=>0];

        $count   = M($this->table)->where($where)->count();
        $page    = getpage($count,I('limit'));
        $student = M($this->table)->where($where)->order("id desc")->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
        $data = [];
        foreach($student as $k=>$v){
            $apartmentMajor = $this->dbTool->getApartmentMajorById($v['apartment_id'],$v['major_id']);

            $v['show_id']   = ($k+1) +$page->listRows*($page->nowPage-1);
            $v['sex']       = $v['sex']?'女':'男';
            $v['is_job']    = $v['is_job']?'已就业':'未就业';
            if($apartmentMajor['apartment_name']){
                $v['apartment'] = $apartmentMajor['apartment_name'];
            }
            if($apartmentMajor['major_name']){
                $v['apartment'] .= ' > '.$apartmentMajor['major_name'];
            }
            $v['created_at']= substr($v['created_at'],0,16);
            $student[$k] = $v;
        }
        $this->ajaxTable($student,$count);

    }

    /**
     * 删除
     */
    public function delete(){
        M($this->table)->where(['id'=>I('id')])->save(['status'=>1]);
        $this->ajax(0,'删除成功');
    }

    /**
     * 导出数据
     */
    public function export(){
        $field = [
            'id'            => 'ID',
            'name'          => '姓名',
            'sex'           => '性别',
            'mobile'        =>'系联手机',
            'apartment_id'     =>'报名层次',
            'major_id'         =>'名报专业',
            'province' 	    =>'在所省份',
            'birthday'      => '生日',
            'nation' 	    =>'族民',
            'idcard' 	    =>'份证身',
            'is_job'        =>'是否就业',
            'residence'     =>'户口所在地',
            'gradute_school' =>'业毕学校',
            'education'     => '学历',
            'graduate_date' => '毕业日期',
            'graduate_major' => '业毕专业',
            'graduate_id'   => '毕业号',
             'email'        => '邮件',
             'address'      => '信通地址',
            'phone'         => '号码',
              'company'     => '作工单位',
              'position'    => '位职',
              'company_phone '=>'位单电话',
              'company_address' =>'位单地址',
              'remark'       =>'注备',
              'created_at'   => '报名时间'
        ];

        $where = ['status'=>0];

        $data   = M($this->table)->field(array_keys($field))->where($where)->select();
        foreach($data as $k=>$v){
            $data[$k]['sex'] = $v['sex']?'女':'男';
            $apartmentMajor = $this->dbTool->getMajorByApartmentId($v['apartment_id'],$v['major_id']);
            $data[$k]['apartment_id'] = $apartmentMajor['apartmentName'];
            $data[$k]['major_id']     = $apartmentMajor['majorName'];
        }
        exportExcelData($data,$field,'学生网上报名表_'.date("Y年m月d日"));
    }

    /*
     * 查看
     */
    public function show(){
        $data = M($this->table)->find($this->param->id);
        $data['apartmentMajor'] = $this->dbTool->getApartmentMajorById($data['apartment_id'],$data['major_id']);
        $this->assign('data',$data);
        $this->display();
    }

    /*
    * 编辑
    */
    public function edit(){
        $data = M($this->table)->find($this->param->id);
        $apartment = $this->dbTool->getApartmentMajor();

        $this->assign("apartment",$apartment);
        $this->assign("apartmentJson",json_encode($apartment));
        $this->assign("major",$this->dbTool->getMajorByApartmentId($data['apartment_id']));
        $this->assign('data',$data);
        $this->display();
    }

    /*
    * 编辑
    */
    public function editHandle(){
        M("student")->save(I(''));
        $this->ajax(0,'编辑成功');
    }
}
?>