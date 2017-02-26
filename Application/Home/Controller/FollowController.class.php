<?php
namespace Home\Controller;
use Think\Controller;

class FollowController extends Controller{

	public function fansLoading(){
		$user_id = I('get.user_id',0);
		$session_login_id = session('?login')?session('login'):0;
		$page = I('get.page',1);
		$count = 10;
		if($user_id == $session_login_id){
			M('Login')->where(array('id'=>$user_id))->save(array('last_fans_read_time'=>date("Y-m-d H:i:s",time())));
		}
		$followModel = D('Follow');
		$result = $followModel->getFansByUserId($user_id,$page,$count);
		foreach($result as &$item){
			$item['is_follow'] = $followModel->where(array('user_id'=>$session_login_id,'follow_id'=>$item['user_id'],'delete_tag'=>(bool)0)) -> count();
			$item['is_fans'] = $followModel->where(array('user_id'=>$item['user_id'],'follow_id'=>$session_login_id,'delete_tag'=>(bool)0)) -> count();
		}
		$this->ajaxReturn($result);
	}

	public function followLoading(){
		$user_id = I('get.user_id',0);
		$session_login_id = session('login');
		$page = I('get.page',1);
		$count = 10;
		$followModel = D('Follow');
		$result = $followModel->getFollowByUserId($user_id,$page,$count);
		foreach($result as &$item){
			$item['is_follow'] = $followModel->where(array('user_id'=>$session_login_id,'follow_id'=>$item['user_id'],'delete_tag'=>(bool)0)) -> count();
			$item['is_fans'] = $followModel->where(array('user_id'=>$item['user_id'],'follow_id'=>$session_login_id,'delete_tag'=>(bool)0)) -> count();
		}
		$this->ajaxReturn($result);
	}

	public function followAction(){
		if(session('?login')) {
			$user_id = I('post.id');
			$follow_id = session('login');
			if ($user_id != $follow_id) {
				$followModel = M('Follow');
				$new_info = $followModel->where(array('user_id' => $follow_id, 'follow_id' => $user_id))->find();
				if ($new_info && $new_info['delete_tag'] == '0') {
					$new_info['delete_tag'] = '1';
					$result = $followModel->where(array('id' => $new_info['id']))->save(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)1));
					$json['id'] = $new_info['id'];
					$json['is_follow'] = '0';
				} else if ($new_info && $new_info['delete_tag'] == '1') {
					$new_info['delete_tag'] = '0';
					$result = $followModel->where(array('id' => $new_info['id']))->save(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)0));
					$json['id'] = $new_info['id'];
					$json['is_follow'] = '1';
				} else {
					$new_info['delete_tag'] = '0';
					$result = $followModel->add(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)1, 'user_id' => $follow_id, 'follow_id' => $user_id));
					$json['id'] = $followModel->getLastInsID();
					$json['is_follow'] = '1';
				}

				if ($result !== false) {
					$json['success'] = true;
					$json['is_fans'] = $followModel->where(array('user_id' => $user_id, 'follow_id' => $follow_id,'delete_tag'=>false))->count();

				} else {
					$json['success'] = false;
					$json['message'] = '操作失败';
				}
			}else{
				$json['success'] = false;
				$json['message'] = '不能关注自己';
			}
		}else{
			$json['success'] = false;
			$json['message'] = '你还没登录';
		}
		$this->ajaxReturn($json);
	}

	public function fansAction(){
		if(session('?login')) {
			$user_id = session('login');
			$follow_id = I('post.id');
			if ($user_id != $follow_id) {
				$followModel = M('Follow');
				$new_info = $followModel->where(array('user_id' => $user_id, 'follow_id' =>$follow_id ))->find();
				if ($new_info && $new_info['delete_tag'] == '0') {
					$new_info['delete_tag'] = '1';
					$result = $followModel->where(array('id' => $new_info['id']))->save(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)1));
					$json['id'] = $new_info['id'];
					$json['is_follow'] = '0';
				} else if ($new_info && $new_info['delete_tag'] == '1') {
					$new_info['delete_tag'] = '0';
					$result = $followModel->where(array('id' => $new_info['id']))->save(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)0));
					$json['id'] = $new_info['id'];
					$json['is_follow'] = '1';
				} else {
					$new_info['delete_tag'] = '0';
					$result = $followModel->add(array('time' => date('Y-m-d H:i:s', time()), 'delete_tag' => (bool)1, 'user_id' => $follow_id, 'follow_id' => $user_id));
					$json['id'] = $followModel->getLastInsID();
					$json['is_follow'] = '1';
				}

				if ($result !== false) {
					$json['success'] = true;
					$json['is_fans'] = $followModel->where(array('user_id' => $follow_id, 'follow_id' => $user_id,'delete_tag'=>false))->count();

				} else {
					$json['success'] = false;
					$json['message'] = '操作失败';
				}
			}else{
				$json['success'] = false;
				$json['message'] = '不能关注自己';
			}
		}else{
			$json['success'] = false;
			$json['message'] = '你还没登录';
		}
		$this->ajaxReturn($json);
	}


}