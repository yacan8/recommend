<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller{
	//登录view
	public function index(){
		$url = I("get.url",'');
		$NewsId = I('get.NewsId','');
		if($NewsId!='')
			session('url',U('/n/'.$NewsId));
		if($url!='')
			session('url',$url);
		// C('TOKEN_ON',false);
		$this->display();
	}
	//ajax检查用户名为登录或注册
	public function check(){
		$LoginModel = D('Login');
		$username = I('post.tel');
		$bool = $LoginModel->check_username($username);
		if($bool){
			echo '1';
		}else{
			echo '2';

		}
	}

	//注册view
	public function register(){
		$this->display();
	}

	//忘记密码
	public function forget(){
		$this->display();
	}


	public function forget_user(){
		$tel = I('post.tel');
		session('forget',$tel);
		if(session("?forget")){
			echo session("forget");
		}else{
			echo "error";
		}
	}
	//注册action
	public function reg(){
		// C('TOKEN_ON',false);
		if(!session("?SMS")){
			session('message','验证码已过期');
			$this->redirect("Login/register",array());
		}else if(session("SMS")!= I('post.SMS')){
			session('message','短信验证码错误');
			$this->redirect("Login/register",array());
		}else{
			$LoginModel = D('Login');
			$result = $LoginModel->create();
			if(!$result){
				session('message',$LoginModel->getError());
				$this->redirect("Login/register",array());
			}else{
				$LoginModel->password = md5($LoginModel->password);
				$LoginModel->reg_time = date('y-m-d H:i:s',time());
				$result1 = $LoginModel->add();
				if($result1!==false){
					session('SMS',null);
					session('login', $LoginModel->getLastInsID());
					if(!session('?url')){
						session('url',U('Index/index','',false,true));
					}
					$this->redirect("User/edit");
				}else{
					session('message','注册失败');
					$this->redirect("Login/register",array());
				}
			}
		}
		
	}

	//登录action	
	public function login(){
		$LoginModel = D('Login');
		$data['tel'] = I('post.tel');
		$data['password'] = I('post.password');
		$bool = $LoginModel ->login($data);
		if($bool){
			if(session('?url')){
				$url = session('url');
				session('url',null);
				header("location:".$url);
				// $this->redirect("News/detail",array('id'=>session('news')));
			}else $this->redirect("Index/index",array());
		}
		else{
			session('message','用户名或密码不正确');
			$this->redirect("Login/index",array());
		}
	}
	//退出登录
	public function outlogin(){
		session('login',null);
		$this->redirect("Index/index",array());
	}


	public function check_verify(){
		$verify_code = I('post.verify');
		$verify = new \Think\Verify();
		$result = $verify->check($verify_code);
		if($result)
			echo 'true';
		else
			// echo session('verify');
			echo 'false';
	}

	/**
	 * [reset 密码重置]
	 */
	public function reset(){
		$SMS = I('post.SMS');
		if($SMS!=session('SMS')){//验证码不正确
			session('message','短信验证码错误');
			$this->redirect("Login/forget");
		}else{
			$password = I('post.password');
			$repassword = I('post.repassword');
			if($password!=$repassword){//密码与确认密码不一致
				session('message','密码与确认密码不一致');
				$this->redirect("Login/forget");
			}else{
				if(!session("?forget")){//判断是否通过正常方式重置密码，提高安全性
					echo "service error";
				}else{
					$tel = session("forget");
					$data['password'] = md5($password);
					$LoginModel = M('Login');
					$old_password = $LoginModel ->where("tel = '%d'",$tel)->getField('password');
					if($old_password !=$data['password']){//如果与原密码一样
						$result = $LoginModel->where(array('tel'=>$tel))->save($data);
						if($result === false){
							session('message','密码重置失败');
							$this->redirect("Login/forget");
						}
					}
					session('SMS',null);
					session('forget',null);
					session('message','密码重置成功');
					$this->redirect("Login/index");
				}
			}
		}
		
	}


	public function nickname_check(){
		$nickname = I('get.nickname');
		$condition['nickname'] = $nickname;
		$count = M('Login')->where($condition)->count();
		if($count==0){
			$json['Code'] = '200';
		}
		else{
			$json['Code'] = '300';
			$json['Message'] = '用户名已存在';
		}
		echo json_encode($json);
	}
}