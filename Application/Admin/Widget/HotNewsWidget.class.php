<?php
namespace Admin\Widget;
use Think\Controller;
class HotNewsWidget extends Controller{
    public function HotNews(){
    	layout(false); // 或者 C('LAYOUT_ON',false);
    	$time = date("Y-m-d H-i-s",strtotime("-1 day"));
    	$date = date("Y-m-d",strtotime("-1 day"));
        $M = M('');
        $List = $M -> query("select b.news_id id,n.title,t.type,count(*) pv
 from cl_browse b, cl_news n , cl_type t
 where time >= '$time' and n.id = b.news_id and n.type = t.id group by news_id order by pv desc limit 0,5");
        $this->assign('List',$List);
        $this->display('Widget:HotNews');
    }
}