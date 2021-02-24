<?php
namespace Lib;
/**
 * 数据库工具类 专门查询数据库 减少重复某个操作
 * Class DataTool
 *
 */
class DBTool{

    /**
     * 获取后台登陆 验证码配置
     * @return int 0 登陆不需要验证码 1登陆需要验证码
     */
    public function getVerificationConf(){
        return MS("config")->where(['keyword'=>'verification_code'])->getField('value');
    }

    /**
     * 获取菜单
     */
    public function getMenu(){

         $roleMenu = explode(',',(new UserTool())->getUser()['role']['menu']);

        $parents = MS("menu")->where(['parent_id'=>0])->order("sort asc")->where(array('status'=>0))->select();
        $result = array();
        foreach($parents as $k=>$v){

            if(!in_array($v['id'],$roleMenu)){
                continue;
            }
            $child = [
                "title" => $v['name'],
                "href" => "",
                "icon" => $v['icon'],
                "target" =>  "_self",
                'mode' => $v['mode'],
                'child' => []
            ];
            $sons = MS("menu")->where(['parent_id'=>$v['id']])->order("sort asc")->where(array('status'=>0))->select();
            foreach($sons as $son){
                if(!in_array($son['id'],$roleMenu)){
                   // continue;
                }
                $child['child'][]  = [
                    "title" => $son['name'],
                    "href" => U('Admis/'.$son['mode'].'/'.$son['method'],array('pid'=>$son['parent_id'],'mid'=>$son['id'],'from'=>'menu')),
                    "icon" => $son['icon'],
                    "target" =>  "_self",
                    'mode' => $son['mode'],
                    'method' => $son['method'],
                ];
            }
            if($child['child']){
                $result[] = $child;//无子级不要
            }

        }
        return $result;
    }

    /**
     * 获取系统配置
     * @return array
     */
    public function getConfig(){
        $config = formatConfig(MS('config')->select());
        return $config;
    }

    /**
     * 获取报名层次下的所有专业
     * @param $apartmentId
     * @return mixed
     */
    public function getMajorByApartmentId($apartmentId){
        return  M("student_apartment_major")->join("right join student_major on student_apartment_major.major_id = student_major.id")
            ->field(["student_major.*"])
            ->where(['student_apartment_major.apartment_id'=>$apartmentId,"student_major.status"=>0])
            ->select();
    }

    /**
     * 获取专业和报读层次
     * @param $apartmentId
     * @param $majorId
     */
    public function getApartmentMajorById($apartmentId,$majorId){
        $apartmentName = M("student_apartment")->where(['id'=>$apartmentId,'status'=>0])->getField('name');
        $majorName     = M("student_major")->where(['id'=>$majorId,'status'=>0])->getField('name');

        return [
            'apartment_id'   => $apartmentName?$apartmentId:0,
            'apartment_name' => $apartmentName?$apartmentName:'',
            'major_id'       => $majorName?$majorId:$majorName,
            'major_name'     => $majorName?$majorName:'',
        ];
    }

    /**
     * 获取角色权限节点
     * @param $roleId
     * @return mixed
     */
    public function getNodesByRoleId($roleId){
        return  MS("permission_role_node")->join("left join permission_node on permission_role_node.node_id = permission_node.id")
            ->where(['permission_role_node.role_id'=>$roleId])->field('permission_node.*')->select();
    }

    /**
     * 获取所有报读层次和专业
     */
    public function getApartmentMajor(){
        $aparment = M("student_apartment")->where(['status'=>0])->order("id")->select();
        $data = [];
        foreach($aparment as $k=>$v){
            $aparment[$k]['major'] = $this->getMajorByApartmentId($v['id']);
        }
        return $aparment;
    }

}
