<?php
namespace Home\Controller;
use Think\Controller;

class MessageController extends Controller{


	public function loading(){
		$user_id = session('login');
		$page = I('get.page',1);
		$count = 10;
		$messageModel = D('Message');
		$time = date('Y-m-d H:i:s',time());
		M("Login")->where(array('id'=>$user_id))->save(array('last_message_read_time'=>$time));
		$result = $messageModel->getList($user_id,$page,$count);
		$this -> ajaxReturn($result);
	}

}
