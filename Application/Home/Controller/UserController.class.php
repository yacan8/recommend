<?php
//用户信息控制器
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller{
	//构造函数
	public function _initialize(){
		$allowArray = array('collection_news','edit','ChangeUserinfo','safe','ChangePassword','myDynamics');//允许不输入Id的action
		if( in_array (ACTION_NAME,$allowArray))
			$id = session('login');
		else
			$id = I('get.id');
        $this->userinfo($id);
        $FollowModel =  M('Follow');
        $fans_count = $FollowModel->where(array('follow_id'=>$id,'delete_tag'=>(bool)0))->count();//粉丝数
        $follow_count = $FollowModel->where(array('user_id'=>$id,'delete_tag'=>(bool)0))->count();//关注数
        $this->assign('fans_count',$fans_count);
        $this->assign('follow_count',$follow_count);
    }

    // 公共函数 获取个人信息
	public function userinfo($id){
		if($id!=""){
			$LoginModel = D('Login');
			$userinfo = $LoginModel->getUserInfoById((int)$id);
			if($userinfo!=''){
				$this->assign('user_info',$userinfo);
				$this->assign('user_id',$id);
			}else{
				header("Content-type: text/html; charset=utf-8");
				exit('参数错误');
			}
		}else{
			header("Content-type: text/html; charset=utf-8");
			exit('参数错误');
		}
	}

	function getReadInfo($id){
		$loginModel = M('Login');
		$userInfo = $loginModel->find($id);
		$followModel = M('Follow');
		$new_fans_count = $followModel->where(array('time'=>array('gt',$userInfo['last_fans_read_time']),'follow_id'=>$id,'delete_tag'=>(bool)0))->count();
		$new_dynamics_count = M('Dynamics')->where(array('time'=>array('gt',$userInfo['last_dynamics_read_time']),'user_id'=>$id))->count();
		$new_message_count = M('Message')->where(array('time'=>array('gt',$userInfo['last_message_read_time']),'user_id'=>$id))->count();
		return array(
			'new_fans_count'=>$new_fans_count,
			'new_dynamics_count'=>$new_dynamics_count,
			'new_message_count'=>$new_message_count
		);
	}

	public function index(){
		$id = I('get.id');
		$user_id = session('login');
		if($id!=""){
			if($user_id == $id){
				$readInfo = $this->getReadInfo($id);
				$this->assign('readInfo',$readInfo);
			}
			$is_follow = M('Follow')->where(array('user_id'=>$user_id,'follow_id'=>$id,'delete_tag'=>false))->count();
			$is_fans = M('Follow')->where(array('user_id'=>$id,'follow_id'=>$user_id,'delete_tag'=>false))->count();
			$this->assign('user_id',$id);
			$this->assign('is_fans',$is_fans);
			$this->assign('is_follow',$is_follow);
			$this->userinfo($id);
			$this->assign('UserContent','UserContent/dynamics');
			$this->display('index2');
		}else{
			header("Content-type: text/html; charset=utf-8");
			exit('参数错误');
		}
	}

	public function myDynamics(){
		$readInfo = $this->getReadInfo(session('login'));
		$this->assign('readInfo',$readInfo);
		$this->display('myDynamics');
	}
	public function userInfoLoading(){
		$id = I('get.id',0);
		if($id != 0){
			$loginModel = D('Login');
			$userinfo = $loginModel->getUserInfoById((int)$id);
			unset($userinfo['password']);
			unset($userinfo['tel']);
			unset($userinfo['last_message_read_time']);
			unset($userinfo['last_dynamics_read_time']);
			unset($userinfo['last_fans_read_time']);
			$userinfo['is_ower'] = $id == session('login');
			$this->ajaxReturn($userinfo);
		}
	}







	//编辑..完善资料view
	public function edit(){
		$id = session('login');

		$LoginModel = D('Login');
		$userinfo = $LoginModel->getUserInfoById((int)$id);

		$schoolProvinces  = M('ProvinceInfo')->select();
		$this->assign('schoolProvinces',$schoolProvinces);

		if($userinfo['userinfo']['schoolname']!=''){//如果未填写学校信息
			//学校信息获取
			$school = $userinfo['userinfo']['schoolname'];
			$SchoolModel = M('SchoolInfo');
			if($school!=''){
				$schoolCityId = $SchoolModel->where(array('sh_shool'=>$school))->getfield('sh_city');
				$schools = $SchoolModel->where(array('sh_city'=>$schoolCityId))->select();
				$CityInfoModel = M('CityInfo');
				$schoolCity = $CityInfoModel->where(array('ci_id'=>$schoolCityId))->find();


				for ($i=0; $i < count($schoolProvinces) ; $i++) {
					if( $schoolProvinces[$i]['pr_id'] == $schoolCity['ci_province']){
						$schoolProvince = $schoolProvinces[$i]['pr_province'];
						$schoolProvinceId = $schoolProvinces[$i]['pr_id'];
					}
				}
				$schoolCitys = $CityInfoModel->where(array('ci_province='=>$schoolProvinceId))->select();
			}


			$this->assign('schoolProvince',$schoolProvince);
			$this->assign('schoolCity',$schoolCity['ci_city']);
			$this->assign('school',$school);

			$this->assign('schoolCitys',$schoolCitys);
			$this->assign('schools',$schools);
		}


		$ProvinceModel = M('AddressProvinces');
		$provinces = $ProvinceModel->select();
		$this->assign('provinces',$provinces);


		if($userinfo['userinfo']!=''){
			//获取省份
			$ProvinceModel = M('AddressProvinces');
			$provinces = $ProvinceModel->select();

			$province = $userinfo['userinfo']['province'];
			$city = $userinfo['userinfo']['city'];

			$CitiesModel = M('AddressCities');


			for ($i=0; $i < count($provinces); $i++) {
				if($provinces[$i]['province'] == $province)
					$provinceId = $provinces[$i]['provinceid'];

			}
			//获取城市
			$cities = $CitiesModel ->where(array('provinceid'=>$provinceId))->select();
			for ($i=0; $i < count($cities); $i++) {
				if($cities[$i]['city'] == $city)
					$cityId = $cities[$i]['cityid'];
			}
			//获取地区
			$areas = M('AddressAreas')->where(array('cityid'=>$cityId))->select();
			if($userinfo['userinfo']['birthdate']!='0000-00-00'){
				$birthDateInt = strtotime($userinfo['userinfo']['birthdate']);
				$year = date('Y',$birthDateInt);
				$month = date('m',$birthDateInt);
				$day = date('d',$birthDateInt);
			}else{
				$year = 0;
				$month = 0;
				$day = 0;
			}

			$this->assign('year',$year);
			$this->assign('month',$month);
			$this->assign('day',$day);
			$this->assign('cities',$cities);
			$this->assign('areas',$areas);
		}


		$typeList = M('Type')->select();
		$interestArr = array();
		foreach ($userinfo['interest'] as $item) {
			array_push($interestArr,$item['type_id']);
		}

		foreach ($typeList as &$item) {
			if(in_array($item['id'],$interestArr)){
				$item['is_interest'] = 1;
			}else{
				$item['is_interest'] = 0;
			}
		}

		$readInfo = $this->getReadInfo($id);
		$this->assign('readInfo',$readInfo);
		$this->assign('typeList',$typeList);
		$this->assign('user_info',$userinfo);
		$this->assign('user_id',session('login'));
		$this->display('edit');
	}




	//编辑资料action
	public function ChangeUserinfo(){
		$LoginModel = D("Login");
		$login_id = session('login');
		$user_id = $LoginModel->where(array('id'=>$login_id))->getField('userId');

		$model = M('');
		$model->startTrans();//开启事务
		if(I('post.year')!='0'&&I('post.month')!='0'&&I('post.day')!='0'){
			$data['birthDate'] = trim(I('post.year')).'-'.trim(I('post.month')).'-'.trim(I('post.day'));
		}else{
			$data['birthDate'] = '';
		}

		$data['schoolName'] = I('post.school')?trim(I('post.school')):'';
		$data['profession'] = I('post.profession')?trim(I('post.profession')):'';
		$data['province'] = I('post.province')?trim(I('post.province')):'';
		$data['city'] = I('post.city')?trim(I('post.city')):'';
		$data['area'] = I('post.areas')?trim(I('post.areas')):'';

		$UserModel = M('User');
		// code 个人简介字符长度大小判断
		$data['shelfIntroduction'] = trim(I('post.shelfIntroduction'));
		if(is_numeric(trim(I('post.qq'))))
			$data['qq'] = trim(I('post.qq'));
		$data['sex'] = trim(I('post.sex'));
		$data['modifyTime'] = date('Y-m-d H:i:s',time());

		if($user_id == '0' || $user_id == ''){
			$data['createTime'] = $data['modifyTime'];
			$data['deleteTag']  = (bool)0;
			$u_result = $UserModel->add($data);
			$user_id = $UserModel->getLastInsID();
		}else{
			$u_result = $UserModel->where(array('id'=>$user_id))->save($data);
		}


		if($u_result === false){
			session('ErrorMessage','修改失败');
			$model->rollback();

		}else{


			if(I('post.email')!=''){
				$validate = array(
				    array('email','email','输入正确的邮箱'), // 仅仅需要进行验证码的验证
				);
				$LoginModel-> setProperty("_validate",$validate);
			}




			$result = $LoginModel->create();
			$LoginModel->id = $login_id;
			$LoginModel->userId = $user_id;
			if(!$result){
				$message = $LoginModel->getError();
				$model->rollback();
				session('ErrorMessage',$message);

			}else{

				//兴趣标签保存

				$interestStr = I('post.interest');
				$interestArr = explode(',',$interestStr);

				$interestModel = M('Interest');
				$deleteResult = $interestModel->where(array('user_id'=>$login_id))->delete();
				$sign = true;

				foreach($interestArr as $interest){
					$interestResult = $interestModel->add(array('user_id'=>$login_id,'type_id'=>$interest));
					if($interestResult === false){
						$sign = false;
					}
				}




				if($_FILES['file']['name']!=null){
					$errorMSG = $LoginModel->change_icon($login_id);
				}
				$l_result = $LoginModel->where(array('id'=>$login_id))->save();

				if( $errorMSG == '' && $l_result !== false && $deleteResult !== false && $sign){
					session('Message','修改成功');
					$model->commit();
				}else{
					$model->rollback();
				}
			}
		}

		if(session('?url')){
			$url = session('url');
			session('url',null);
			header("location:".$url);
		}else
			$this->redirect('User/edit');
	}

	// 账号安全view
	public function safe(){
		$this->display('edit');
	}
	//修改密码action
	public function ChangePassword(){
		$o_password  = I('post.o_password');
		$n_password  = I('post.n_password');
		$re_password = I('post.re_password');
		if($n_password!=$re_password){
			session('ErrorMessage','新密码与确认密码不一致');
			$this->redirect('User/safe',array());
		}else{
			if(strlen($n_password)<6||strlen($n_password)>20){
				session('ErrorMessage','密码长度必须为6~20');
			}else{
				$LoginModel = D("Login");
				$message = $LoginModel->ChangePassword($o_password,$n_password,session('login'));
				if($message == '密码错误' || $message == '修改失败')
					session('ErrorMessage',$message);
				else
					session('Message',$message);
			}
		}
		$this->redirect('User/safe');
	}


	//粉丝view
//	public function fans(){
//		$id = I('get.id');
//		$p = I('get.p',1);
//		if($id!=""){
//			$showCount = 20;//每页显示个数
//			$FollowModel = D('Follow');
//			$count = $FollowModel->where(array('follow_id'=>$id,'delete_tag'=>(bool)0))->count();
//			if($count%$showCount==0)
//				$TotalPage = intval($count/$showCount);
//			else
//				$TotalPage = intval($count/$showCount)+1;
//			$List = $FollowModel->getFansByUserId((int)$id,$p,$showCount);
//			$this->assign('p',$p);//分页
//			$this->assign('TotalPage',$TotalPage);//总页数
//			$this->assign('count',$count);//总数
//			$this->assign('List',$List);
//			$this->assign('user_id',$id);
//			$this->assign('UserContent','UserContent/fans2');
//			$this->display('index2');
//		}else{
//			header("Content-type: text/html; charset=utf-8");
//			exit('参数错误');
//		}
//
//	}

	//关注view
//	public function follow(){
//		$id = I('get.id');
//		$p = I('get.p',1);
//		if($id!=""){
//			$showCount = 20;//每页显示个数
//			$FollowModel = D('Follow');
//			$count = $FollowModel->where(array('user_id'=>$id,'delete_tag'=>(bool)0))->count();
//			if($count%$showCount==0)
//				$TotalPage = intval($count/$showCount);
//			else
//				$TotalPage = intval($count/$showCount)+1;
//			$List = $FollowModel->getFollowByUserId((int)$id,$p,$showCount);
//			$this->assign('p',$p);//分页
//			$this->assign('TotalPage',$TotalPage);//总页数
//			$this->assign('count',$count);//总数
//			$this->assign('List',$List);
//			$this->assign('user_id',$id);
//			$this->assign('UserContent','UserContent/follow');
//			$this->display('index');
//		}else{
//			header("Content-type: text/html; charset=utf-8");
//			exit('参数错误');
//		}
//	}
	//登录验证
    public function login_check(){
    	if (!isset($_SESSION['login'])) {
            $this->error('请先登录', U('Index/index'), 2);
        }
    }
	//我的收藏-资讯
	public function collection_news(){
		$this->login_check();
		$id = session('login');
		$DB_PREFIX = C('DB_PREFIX');
		$p = I('get.p',1);
		$ShowCount = 10;
		$firstrow = ($p-1)*$ShowCount;
		$Model = M('');
		$CollectionModel = M('Collection');
		$NewsModel = D('News');
		$CollectionNewIdList = $Model->table($DB_PREFIX.'collection co') ->join($DB_PREFIX.'news n on co.collected = n.id')->where(array('co.collecting'=>$id,'co.type'=>1,'co.delete_tag'=>(bool)0))->field('co.id id,co.collected collected')->limit("$firstrow,$ShowCount")->order('co.id desc')->select();
		for ($i=0; $i < count($CollectionNewIdList); $i++) {
			$List[$i] =  $NewsModel->relation('type')->relation('sections') ->where(array('id'=>$CollectionNewIdList[$i]['collected']))->field('id,title,intro,publish_time,type,browse,image,image_thumb,sections')->find();
			$List[$i]['collection_id'] = $CollectionNewIdList[$i]['id'];
		}
		$List = $NewsModel->GenerateNews($List);
		$count      = $CollectionModel->where(array('collecting'=>$id,'type'=>1,'delete_tag'=>(bool)0))->count();// 查询满足要求的总记录数
		$Page       = new  \Think\Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('user_id',$id);
		$this->assign('UserContent','UserContent/collectionAttention');
		$this->assign('List',$List);
		$this->assign('page',$show);
		$this->display('index');
	}


	public function message(){
			$this->assign('UserContent','UserContent/message');
			$this->display('index2');
	}
	public function test(){
		$this->ajaxReturn($_GET);
	}
}
