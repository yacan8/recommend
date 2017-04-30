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
		$count      = $LoginModel->where('power = 2 or power = 1 or power = 0')->count();
		$Page       = new \Think\Page($count,10);
		$show       = $Page->show();
		$List = $LoginModel->where('power = 2 or power = 1 or power = 0')->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		for ($i=0; $i < count($List); $i++) { 
			if($List[$i]['icon'] == '')
				$List[$i]['icon'] = 'default.jpg';
		}
		$this->assign('title','用户管理');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display();
	}

	public function applyUser(){
		$loginModel = M('Login');
		$count      = $loginModel->where(array('power'=>3))->count();
		$Page       = new \Think\Page($count,10);
		$show       = $Page->show();
		$List = $loginModel->where(array('power'=>3))->order('reg_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		for ($i=0; $i < count($List); $i++) {
			if($List[$i]['icon'] == '')
				$List[$i]['icon'] = 'default.jpg';
		}
		$this->assign('title','发布者管理');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display('applyUser');
	}

	//修改权限
	public function setPower(){
		$tel = I('get.id');
		$LoginModel = D('Login');
		$login = $LoginModel->where("id ='".session('Adminlogin')."'")->getField('power');
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


	public function applyContent(){
		$user_id = I('get.id');
		$userInfo = D('Login')->getInfoByid($user_id);

		$count = 10;
		$p = I('get.p',1);
		$NewsModel = D('News');
		$List = $NewsModel->getListByUserId($user_id,$p,$count);
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('userInfo',$userInfo);
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display('applyContent');
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