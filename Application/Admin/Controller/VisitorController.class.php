<?php
namespace Admin\Controller;
use Think\Controller;
class VisitorController extends Controller{
    public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
    }

	/**
     * [area_Visitor ajax 类型浏览pv ACTION]
     * @param [Inerger] $[post.pre] [<前$pre个月>-1为当天,0为本月，3为前三个月，12为前一年]
     * @return [json] [查询到的列表]
     */
	public function area_uv(){
		$VisitorModel = D('Visitor');
        $pre = I('post.pre');
        $id = I('post.id',0);
        $data_time = getStartEndTime($pre);//获取开始时间和结束时间
        $area_VisitorList = $VisitorModel->area_all_uv($data_time['start'],$data_time['end'],$id);   //获取列表
        // $this->ajaxReturn($area_VisitorList,'json');
        echo json_encode($area_VisitorList);
	}



    public function active_uv(){
        $mouth = I('post.mouth'); //获取月份数
        $year = date('Y');
        $mouth = intval($mouth);
        $VisitorModel = D('Visitor');
        $days_count = mouth_days($year,$mouth);
        $time_start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,1,$year));//获取开始时间
        $time_end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year));//获取结束时间
        $ActiveUvList = $VisitorModel->active_uv($time_start,$time_end);//根据开始时间和结束时间，返回该时间段内每天文章的浏览次数
        $VisitorModel->init_active_days($days_count,$year,$mouth,$ActiveUvList);//将无浏览的当月的浏览数设置为0
        // echo json_encode($ActiveUvList);
        $this->ajaxReturn($ActiveUvList,'json');
    }
}