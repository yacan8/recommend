<?php
namespace Home\Behaviors;
use Think\Behavior;
class VisitorNewsBehavior extends Behavior {


	// 行为扩展的执行入口必须是run
    public function run(&$params){
        if(!isCrawler()){//如果不为网页爬虫
        	if(CONTROLLER_NAME == 'News' && ACTION_NAME == 'detail'){//判断是否为访问浏览文章页
        		$id = I('get.id');
                if(is_numeric($id)){
                    $cookie_name = "news".$id;
                    // 导入IpLocation类
                    $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
                    $ip_value = get_client_ip();
                    $area = $Ip->getlocation($ip_value); // 获取某个IP地址所在的位置
                    area_change($area);//地区字段转换
                    $second = date('s');
                    $minute = date('i');
                    $hour   = date('H');
                    $expire = (23-$hour)*3600+(60-$minute)*60+(60-$second);
                    if(cookie('campusleader_visitor')==''){
                        $VisitorData['date'] = date('Y-m-d',time());
                        $VisitorData['read'] = 1;
                        $VisitorData['ip'] = $ip_value;
                        $VisitorModel = M('Visitor');
                        $VisitorData['area'] = $area['country'];
                        $VisitorModel->add($VisitorData); //添加到数据库
                        $insert_id = $VisitorModel->getLastInsID();
                        cookie('campusleader_visitor',$insert_id.',1',$expire);
                    }else{
                        if(cookie($cookie_name)==''){ //判断是否已被阅读
                            $visitor_str = cookie('campusleader_visitor');
                            $visitor_array = explode(',',$visitor_str);
                            $condition['id'] = (int)$visitor_array[0];
                            $data['read'] = (int)$visitor_array[1]+1;
                            $VisitorModel = M('Visitor');
                            $VisitorModel->where($condition)->save($data); //添加到数据库
                            cookie('campusleader_visitor',$condition['id'].','.$data['read'],$expire);
                        }
                    }
                    if(cookie($cookie_name)==''){
                        // $config = array('expire'=>$expire,'path'=>'/','domain'=>'.campusleader.cn');
                        cookie($cookie_name,"true",$expire);
                        $data['ip'] = $ip_value;//ip
                        $data['date'] = date('Y-m-d',time());//时间
                        $data['area'] = $area['country'];//地区
                        $data['news_id'] = I('get.id');//文章Id
                        M('VisitorNews')->add($data);
                    }
                }
        		
        	}
        }    
    }
}