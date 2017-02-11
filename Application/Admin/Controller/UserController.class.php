<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	public function index(){
		$LoginModel = M('Login');
		$count      = $LoginModel->count();
		$Page       = new \Think\Page($count,10);
		$show       = $Page->show();
		$List = $LoginModel->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		for ($i=0; $i < count($List); $i++) { 
			if($List[$i]['icon'] == '')
				$List[$i]['icon'] = 'default.jpg';
		}
		$this->assign('title','用户管理');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display();
	}

	//修改权限
	public function setPower(){
		$tel = I('get.id');
		$LoginModel = D('Login');
		$login = $LoginModel->where("tel ='".session('Adminlogin')."'")->getField('power');
		if($login == '2'){
			$result = $LoginModel->setPower($tel);
			if($result)
				$this->success('修改成功');
			else
				$this->error('修改失败');
		}else{
			$this->error('无权限修改');
		}
	}

	//注册用户根据日期统计view
	public function reg_s(){
		$this->display("reg_statistics");
	}
	//注册用户根据日期统计action
	public function reg_statistics(){
		$mouth = I('post.mouth');
		$year = date('Y');
		$days_count = mouth_days($year,$mouth);
	 	$time_start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,1,$year));//获取开始时间
    	$time_end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year));//获取结束时间
        $LoginModel = D('Login');
        $List = $LoginModel->reg_statistics($time_start,$time_end);
        // dump($List);
        $LoginModel->init_days($days_count,$year,$mouth,$List);
        echo json_encode($List);
	}
}