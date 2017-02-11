<?php
namespace Admin\Controller;
use Think\Controller;

class TopicController extends Controller{



	public function index(){
		$user_id = I('get.user_id',0);
		$TopicModel = D('Topic');
		$count = 10;
		$List = $TopicModel->getList($user_id,1,$count);
		if($user_id!=0){
			$LoginModel = D('Login');
			$userinfo = $LoginModel->getInfoByid($user_id);
			$this->assign('userinfo',$userinfo);
		}

		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('select','t');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display();
	}

	/**
	 * [delete 删除话题或评论]
	 * @param [String] $type ['t'为话题 'c'为评论]
	 * @param [Integer] $id [删除的id]
	 */
	public function delete(){
		$type = I('get.type');
		$id = I('get.id');
		if($type !=''&&$id!=''){
			$data['delete_tag'] = (bool)1;
			if($type == 't'){
				$Model = M('Topic');
			}else if($type == 'c'){
				$Model = M('TopicComment');
			}else
				$this->error('参数错误');
			$Model -> where(array('id'=>$id))->save($data);
			// $_SERVER["HTTP_REFERER"]
			$this->success('删除成功');
		}else{
			echo '参数错误';
		}
	}


	public function type(){
		$TopicTypeModel = M('TopicType');
		$List = $TopicTypeModel->select();
		$this->assign('List',$List);
		$this->display();
	}
	
}