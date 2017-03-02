<?php
namespace Home\Controller;
use Think\Controller;


class ApplyController extends Controller{

	public function apply(){
		$this->assign('IssueContent','IssueContent/apply');
		$this->display('Issue/index');
	}


	public function content(){
		$user_id = session('login');
		$newsModel = D('News');
		$all_count = $newsModel->where(array('contributor'=>$user_id,'delete_tag'=>false))->count();
		$count = 10;
		$Page       = new \Think\Page($all_count,$count);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);
		$this->assign('p',I('get.p',1));
		$this->assign('user_id',$user_id);
		$this->assign('IssueContent','IssueContent/content');
		$this->display('Issue/index');
	}
	public function contentLoading(){
		$user_id = session('login');
		$loginModel = M('Login');
		$page = I('get.page',1);
		$count = 10;
		$power = $loginModel->where(array('id'=>$user_id))->getField('power');
//		if( $power == '3' ){
			$newsModel = D('News');
			$result = $newsModel->getIssueList($user_id,$page,$count);
//		}else{
//			$result = array();
//		}
		$this->ajaxReturn($result);
	}

	//发布者Action
	public function action(){
		if(session('?login')){
			$applyModel = D('Apply');
			$createResult = $applyModel->create();
			$applyModel->user_id = session('login');
			$applyModel->time = date('Y-m-d H:i:s',time());
			if(!$createResult){
				$this->error($applyModel->getError());
			}else{
				if($_FILES['file']['name']!=null){
					$config = array(
						'maxSize' => 6291456,// 设置附件上传大小
						'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
						'savePath'=>'Apply/',// 设置附件上传目录
						'rootPath'=> './Data/',
						'autoSub'    =>    true,
						'subName'    =>    array('date','Y-m-d')
					);
					$upload = new \Think\Upload($config);// 实例化上传类
					$info = $upload->uploadOne($_FILES['file']);
					if(!$info){
						$this->error($upload->getError());
					}else{
						$applyModel->file = $info['savepath'].$info['savename'];
						$result = $applyModel ->add();
						if($result !== false){
							$this->success('提交成功，请耐心等待审核。',U('Index/index'),5);
						}else{
							$this->error('提交失败');
						}
					}
				}else{
					$this->error('请选择证件照片');
				}
			}
		}else{
			$this->error('你还没有登录');
		}
	}


	public function uvPvIndexLoading(){
		$news_id = I('get.news_id');
		$startTime= I('get.startTime');
		$endTime = I('get.endTime');
		$pvList = D('Browse')->getIndexByTime($news_id,$startTime,$endTime);
		$uvList = D('VisitorNews')->getIndexByTime($news_id,$startTime,$endTime);
		$json = array(
			'pv'=>$pvList,
			'uv'=>$uvList
		);
		$this->ajaxReturn($json);
	}


	public function fansIndexLoading(){
		$followModel = D('Follow');
		$follow_id = session('login');
		$result = $followModel->getGroupByTime($follow_id,I('get.startTime'),I('get.endTime'));
		$sql = $followModel->getLastSql();
		$this->ajaxReturn($result);
	}


	public function contentDelete(){
		if(session('?login')){
			$news_id = I('post.news_id');
			$newsModel = M('News');
			$author = $newsModel->where(array('id'=>$news_id,'delete_tag'=>false))->getField('contributor');
			$user_id = session('login');
			if( $user_id == $author ){
				$result = $newsModel->where(array('id'=>$news_id))->save(array('delete_tag'=>true));
				if($result !== false){
					$json['success'] = true;
					$json['message'] = '删除成功';
				}else{
					$json = array(
						'success'=>false,
						'message'=>'删除失败'
					);
				}
			}else{
				$json = array(
					'success'=>false,
					'message'=>'你没有权限操作'
				);
			}
		}else{
			$json = array(
				'success'=>false,
				'message'=>'你还没有登录'
			);
		}
		$this->ajaxReturn($json);
	}

}