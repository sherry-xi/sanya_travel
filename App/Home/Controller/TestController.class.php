<?php
/**
 * 测试功能
 */
namespace Home\Controller;
use Think\Controller;


class TestController extends BaseController{


    public function index(){
        $this->replaceArticle();
    }

    /**
     * 去除重复文章
     */
    public function replaceArticle(){
        $article = MS("article")->query("SELECT title,count(title) cnt FROM article where cid = 199  GROUP by title having cnt>1");
        foreach($article as $v){
            $data = MS("article")->field("id,title,create_time")->where(['title'=>$v['title']])->order("id asc")->select();
            p($data);
            foreach($data as $k=>$value){
                if($k){
                    echo $value['id'],",";
                }
            }
        }
    }

    /**
     * 采集文章内容
     */
    public function collectArticleThumb(){
        $data = MS("artilce_old_web")->field("id,origin_id")->where("content is null ")->limit(1)->select();

        foreach($data as $v){

            $url = "http://www.ucsanya.com/fenyuanshow1.asp?did=345&id=".$v['origin_id'];
            $html = file_get_contents($url);
            $html = mb_convert_encoding($html, "UTF-8", "GBK") ;

            preg_match_all("/<div class=\"zt_right_about_riqi\"><p>(.*)&nbsp;&nbsp;&nbsp;/", $html, $time); //1.匹配时间

            $time = date("Y-m-d H:i:s",strtotime($time[1][0]));

            $begin = strpos($html,"zt_right_about_nr\">")+19;
            $content = trim(substr($html,$begin,strpos($html,"上一篇")-$begin));
            $content = str_replace("/upfile/images","http://www.ucsanya.com/upfile/images",$content);
            MS("artilce_old_web")->where(['id'=>$v['id']])->save(['content'=>$content,'create_time'=>$time]);

        }
        echo $data[0]['id'],',',$data[count($data)-1]['id'];

    }

    function saveFile($file_url, $save_to)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch,CURLOPT_URL,$file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $file_content = curl_exec($ch);
        curl_close($ch);
        $downloaded_file = fopen($save_to, 'w');
        //fwrite($downloaded_file, $file_content);
        print_r($downloaded_file);
        fclose($downloaded_file);
    }

    /**
     * 采集旅游学院老文章
     *
     */
    public function collectArticle(){
        //起始页1 结束页113

        /**
         * 1.采集列表页面
         * http://www.ucsanya.com/fenyuan_news.asp?page=1&did=345
         * http://www.ucsanya.com/fenyuan_news.asp?page=113&did=345
         */
        //header("Content-type: text/html; charset=gb2312");
        $userinfo = "Name: <b>PHP</b> <br> Title: <b>Programming Language</b>";
        preg_match_all ("/<b>(.*)<\/b>/U", $userinfo, $pat_array);

        $result = [];

        $pageId = MS("artilce_old_web")->query("select max(page_id) as page_id from artilce_old_web")[0]['page_id']+1;
        $end = $pageId+4;//10页一次

        echo "$pageId,$end,<br/>";

        for($pageId; ($pageId<=113)&&($pageId<=$end);$pageId++){
            $url = "http://www.ucsanya.com/fenyuan_news.asp?page={$pageId}&did=345";
            $html = file_get_contents($url);
            preg_match_all("/did=345&id=(.*)\" title=/", $html, $articleId); //1.匹配id
            preg_match_all("/\" title=\"(.*)\"  target=/", $html, $articleTitle);//标题匹配
            preg_match_all("/target=\"_blank\"><img src=\"(.*)\" border/", $html, $articleThumb); //匹配缩略图

            $ids = [];
            foreach($articleThumb[1] as $k=>$v){
                $result[] = [
                    'origin_id'    => $articleId[1][2*$k+1],
                    'page_id'      => $pageId,
                    'title'        => mb_convert_encoding($articleTitle[1][2*$k+1], "UTF-8", "GBK") ,
                    'origin_thumb' => $v
                ];
            }
        }
       MS("artilce_old_web")->addAll($result);
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        die;
    }
}

/**
 * 0,1,2,3,4,5,6,7
 * 0,1,2,3
 */