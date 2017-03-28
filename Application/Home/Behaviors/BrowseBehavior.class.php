<?php
namespace Home\Behaviors;
use Think\Behavior;
class BrowseBehavior extends Behavior {
    // 行为参数定义
    protected $options   =  array(
        // 'TEST_PARAM'        => true,   //  行为参数 会转换成TEST_PARAM配置参数
    );
    // 行为扩展的执行入口必须是run
    public function run(&$params){
        if(!isCrawler()){//如果不为网页爬虫
            if(CONTROLLER_NAME == 'News' && ACTION_NAME == 'detail'){//判断是否为访问浏览文章页
                $data['news_id'] = I('get.id'); 
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
}