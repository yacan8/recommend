<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        session('app_nav',null);
    	$Model = D('News');
        $TypeModel = D('Type');
        $TypeList = $TypeModel ->getOnType();
        $HeadLines = $Model ->getHeadLines();
        $List = $Model -> getTop10();
        $this->assign('TypeList',$TypeList);
        $this->assign('HeadLines',$HeadLines);

        $this->assign('List',$List);
    	$this->assign('title',"campusleader_打造最强高校领袖");

        $this->display();
    }
    Public function verify(){
        $Verify =  new \Think\Verify();
        $Verify->fontSize = 200;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->entry();
    }

    public function upl(){
        $this->display('upl');
    }
    public function QRcode(){
        $data = "http://".$_SERVER['HTTP_HOST'].U('Index/download','',false,false);
        vendor("phpqrcode.phpqrcode");
        // 纠错级别：L、M、Q、H
        $level = 'H';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 10;
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        //$path = "images/";
        // 生成的文件名
        //$fileName = $path.$size.'.png';
        $QRcode = new \QRcode();
        $QRcode->png($data, false, $level, $size);

    }

    public function download(){

        $Agent = $_SERVER['HTTP_USER_AGENT'];
        preg_match('/android|iphone/i',$Agent,$matches);
        if(strtolower($matches[0])=='android'){
            if(!is_weixin())
                header("Location: "."http://".$_SERVER['HTTP_HOST']."/Data/tuanzi.apk");
            else{

                echo '<h1>微信扫一扫暂不支持下载，请使QQ扫一扫或者其他二维码扫描。</h1>';
            }
        }
        else if(strtolower($matches[0])=='iphone'){
            if(!is_weixin())
                header("Location:https://itunes.apple.com/cn/app/tuan-zi-gao-xiao-tuan-guan/id1106865889?l=en&mt=8");
            else{
                echo "<img src ='http://".$_SERVER['HTTP_HOST']."/Public/img/weixindown1.jpg' style='width:100%'><img src ='http://".$_SERVER['HTTP_HOST']."/Public/img/weixindown2.jpg' style='width:100%'>";
            }
        }else{
            header("Content-type: text/html; charset=utf-8");
            echo '<h1>无法识别手机操作系统，请使用qq扫一扫获取其他二维码扫描器。</h1>';
        }
    }
}
