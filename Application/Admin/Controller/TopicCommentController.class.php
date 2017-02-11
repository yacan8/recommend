<?php
namespace Admin\Controller;
use Think\Controller;
class TopicCommentController extends Controller{


	public function index(){
		$user_id = I('get.user_id',0);
		$p = I('get.p',1);
		$TopicCommentModel = D('TopicComment');
		$count = 10;
		$List = $TopicCommentModel->getList($user_id,$p,$count);
		if($user_id!=0){
			$LoginModel = D('Login');
			$userinfo = $LoginModel->getInfoByid($user_id);
			$this->assign('userinfo',$userinfo);
		}

		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('select','c');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display('Topic:index');
	}


	public function view(){
		$id = I('get.id');
		if($id!=''){
			$TopicCommentModel = M('TopicComment');
			$arr = $TopicCommentModel->where(array('id'=>$id))->field('id,topic_id,comment_id,time')->find();
			if(count($arr)!=0){
				if($arr['comment_id']=='0'){
					$pageId = $arr['id'];
					$time = $arr['time'];
				}else{
					$pageId = $id;
					$time = $TopicCommentModel->where(array('id'=>$arr['id']))->getField('time');
				}
					

				$condition['comment_id'] = array('eq',0);
				$condition['topic_id']   = array('eq',$arr['topic_id']);
				$condition['delete_tag'] = array('eq',(bool)0);
				// $AllCount = $TopicCommentModel->where($condition)->order('time asc')->count();
				$condition['time'] = array('lt',$time);
				$PreCount = $TopicCommentModel->where($condition)->order('time asc')->count();
				if($PreCount%10==0)
					$p = (int)$PreCount/10;
				else
					$p = (int)($PreCount/10)+1;
				$this->redirect('/t/'.$arr['topic_id']."#c".$id,array('p'=>$p));

			}else{
				echo '该评论不存在';
			}
		}else{
			echo '参数错误';
		}
	}
}