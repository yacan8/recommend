<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller{

	public function index(){
		layout(false); // 或者 C('LAYOUT_ON',false);
		$this->display('login');
	}
	public function login_action(){
		$tel = I('post.tel');
		$password = I('post.password');
		$LoginModel = M('Login');
		$userinfo = $LoginModel->where(array('tel'=>$tel))->find();
		if($userinfo['power'] != '0'){
			if($userinfo['password']== md5($password)){
				session('Adminlogin',$userinfo['id']);
				$this->redirect('Index/index');
			}else{
				session('message','用户名或密码错误');
				$this->redirect('Login/index');
			}
		}else{

			session('message','用户名或密码错误');
			$this->redirect('Login/index');
		}
		// echo session('message');
	}
	public function outlogin(){
		session('Adminlogin',null);
		$this->redirect('Login/index');
	}
}
