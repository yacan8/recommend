<?php
namespace Admin\Controller;
use Think\Controller;
class BrowseController extends Controller{
    public function _initialize(){
        layout(false);
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
    }
	/**
     * [area_browse ajax 按日期浏览pv ACTION]
     * @param [Inerger] $[post.mouth] [<传入的月份>]
     * @param [Inerger] $[post.year] [<传入的年份>]
     * @return [json] [查询到的列表]
     */
    public function browse(){
    	$mouth = I('post.mouth'); //获取月份数
    	$year = I('post.year');
        $id = I('post.id',0);//获取id
        $year = intval($year);
        $mouth = intval($mouth);
        $BrowseModel = D('Browse');
        $days_count = mouth_days($year,$mouth);
        $time_start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,1,$year));//获取开始时间
    	$time_end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year));//获取结束时间
    	$browseList = $BrowseModel->browse_time_count($time_start,$time_end,$id);//根据开始时间和结束时间，返回该时间段内每天文章的浏览次数
    	// echo  $BrowseModel->getLastSql();
        // dump($browseList); 
        $BrowseModel->init_days($days_count,$year,$mouth,$browseList);//将无浏览的当月的浏览数设置为0
    	echo json_encode($browseList);
    }

    /**
     * [area_browse ajax 地区浏览pv ACTION]
     * @param [Inerger] $[post.pre] [<前$pre个月>]
     * @return [json] [查询到的列表]
     */
    public function area_browse(){
        $BrowseModel = D('Browse');
        $id = I('post.id',0);//获取id
    	$pre = I('post.pre');
        $data_time = getStartEndTime($pre);
        $area_browseList = D('Browse')->area_browse($data_time['start'],$data_time['end'],$id);   //获取列表
    	echo json_encode($area_browseList);
    }

    /**
     * [area_browse ajax 类型浏览pv ACTION]
     * @param [Inerger] $[post.pre]  [<前$pre个月>-1为当天,0为本月，3为前三个月，12为前一年]
     * @return [json] [查询到的列表]
     */
    public function type_browse(){
        $BrowseModel = D('Browse');
        $pre = I('post.pre');
        $data_time = getStartEndTime($pre);
        $area_browseList = $BrowseModel->type_browse($data_time['start'],$data_time['end']);   //获取列表
        echo json_encode($area_browseList);
    }

    /**
     * [average_pv_uv 根据传入的时间间隔，计算该时间段内发出的文章的平均PV和UV]
     * @param [string] $interval [传入的时间间隔 day week month]
     */
    public function average_pv_uv(){
        $interval = I('get.interval','day');
        $BrowseModel = D('Browse');
        $result = $BrowseModel ->average_pv_uv($interval);
        echo json_encode($result);
    }
}