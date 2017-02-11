<?php
namespace Home\Controller;
use Think\Controller;
class BrowseController extends Controller{


    public function pv(){
         if(!isCrawler()){//如果不为网页爬虫
            $data['news_id'] = I('post.id'); 
            $data['ip'] = get_client_ip();//获取IP地址
            $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
            $area = $Ip->getlocation($data['ip']); // 获取某个IP地址所在的位置
            //地区字段转换
            area_change($area);
            $data['area'] = $area['country']; //记录地址
            $data['time'] = date('Y-m-d H:i:s',time()); //记录浏览时间
            $data['type'] = $area['type'];
            $BrowseModel = M('Browse');
            if(session('?login'))
                $data['user_id'] = session('login');//记录用户
            $BrowseModel->add($data); //添加到数据库
        }
    }

}