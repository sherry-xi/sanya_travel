<?php
namespace Admis\Controller;

use Lib\Upload;
use mysql_xdevapi\Session;

/**
 * Class FileController
 * @package Admis\Controller
 *  File 资料下载管理
 *      index 资料列表
 *      delete 删除资料
 *      upload ,uploadHandle上传资料
 */
class FileController extends BaseController{

    private $table = "file";

    /**
     * 资料下载管理
     */
    public function index(){

        if(!I("get")){
            $this->display();
            exit;
        }

        $count   = MS($this->table)->count();
        $page    = getpage($count,I('limit'));

        $files = MS($this->table)->limit($page->firstRow.','.$page->listRows)->order("id desc")->select();
        foreach($files as $k=>$file){
            $user    = MS("admin")->find($file->user_id);

            $files[$k]['show_id']   = ($k+1) +$page->listRows*($page->nowPage-1);
            $files[$k]['size'] = file_size_format($file['size']);
            $files[$k]['user'] = $user?$user['truename']:'';
            $files[$k]['url']  = $this->config['domain'].$file['path'];
            $files[$k]['channel'] = MS("channel")->where(['id'=>$file['cid']])->getField('name');
        }
        $this->ajaxTable($files,$count);

    }

    /**
     * 删除资料
     */
    public function delete(){
        $file = MS($this->table)->find(I("id"));
        MS($this->table)->delete($file['id']);
        unlink('./'.$file['path']);
        $this->ajax(0,'删除成功');
    }

    /**
     * 上传文件 页面
     */
    public function upload(){
        $channel = MS("channel")->where(['parent_id'=>C("channelId")['download']])->select();
        $this->assign('channel',$channel);
        $this->display();
    }

    /**
     * 编辑
     */
    public function edit(){
        $channel = MS("channel")->where(['parent_id'=>C("channelId")['download']])->select();
        $this->assign('channel',$channel);
        $this->assign('file',MS($this->table)->find($this->param->id));
        $this->display();
    }

    public function editHandle(){
        MS($this->table)->save([
            'id' => $this->param->id,
            'show'  => $this->param->show,
            'name'  => $this->param->name,
            'cid'  => $this->param->cid,
        ]);
        $this->ajax(0,'编辑成功');
    }

    /**
     * 上传文件
     */
    public function uploadFile(){
        $result = (new Upload())->upload();
        echo "<script>parent.uploadHandle.finish('".json_encode($result)."');</script>";
    }

    /**
     * 文件上传
     */
    public function uploadHandle(){

        MS($this->table)->add(
            [
                'channel_id' => 0,
                'name' => $this->param->name,
                'path' => $this->param->path,
                'user_id' => session('user')['id'],
                'type' => $this->param->type,
                'size' => $this->param->size,
                'show' => $this->param->show,
                'cid'  => $this->param->cid,
            ]
        );
        $this->ajax(0,'上传成功');
    }

}