<?php
namespace Home\Controller;
use Think\Controller;
class MessageManagerController extends Controller{

	/**
	 * [read 标记为已读]
	 */
	public function read(){
		$id = I('get.id');
		$MessageManegerModel = M('MessageManager');
		$user_id = $MessageManegerModel->where(array('id'=>$id))->getField('user_id');
		if($user_id!=''){
			if($user_id==session('login')){

				$data['read_tag'] = (bool)1;

				$result = $MessageManegerModel->where(array('id'=>$id))->save($data);

				if($result!==false){
					if(isset($_SESSION['HTTP_REFERER']))
						header("location:".$_SESSION['HTTP_REFERER']);
					else
						$this->redirect('User/message');
				}else
					$this->error('操作失败');
			}else{//消息主人不为登录者
				$this->error('你没有操作权限');
			}
		}else{
			$this->error('该消息不存在');
		}
	}


	//全部标记为已读
	public function readAll(){
		$user_id = I('get.id');
		if($id==''){
			if($user_id===session('login')){
				$result = M('MessageManager')->where(array('user_id'=>$user_id))->save(array('read_tag'=>(bool)1));
				if($result!==false){
					if(isset($_SESSION['HTTP_REFERER']))
						header("location:".$_SESSION['HTTP_REFERER']);
					else
						$this->redirect('User/message');
				}else
					$this->error('操作失败');
			}
		}else
			exit('参数错误');
	}
}