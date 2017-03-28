<?php
namespace Home\Controller;
use Think\Controller;


class ApplyController extends Controller{

	public function _initialize(){
		if(ACTION_NAME != 'apply'){
			$user_id = session('login');
			$loginModel = M('Login');
			$power = $loginModel->where(array('id'=>$user_id))->getField('power');
			if($power != '0'){
				$this->assign('power',$power);
			}else{
				$this->redirect('Apply/apply');
			}
		}
	}

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
		$browseModel = D('Browse');
		$browse_count = $browseModel->getAllCountByUserId($user_id);
		$news_count = $newsModel->getCountByUserId($user_id);
		$this->assign('news_count',$news_count);
		$this->assign('browse_count',$browse_count);
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
		if( $power != '0' ){
			$newsModel = D('News');
			$result = $newsModel->getIssueList($user_id,$page,$count);
		}else{
			$result = array();
		}
		$this->ajaxReturn($result);
	}


	public function publish(){
		$typeModel = D('Admin/Type');
		$TypeList = $typeModel->getType();
		$this->assign('TypeList',$TypeList);
		$this->assign('IssueContent','IssueContent/publish');
		$this->display('Issue/index');
	}

	public function publishAction(){
		$NewsModel = D("Admin/News");
		$result = $NewsModel->create();
		if(!$result){
			$this->error($NewsModel->getError());
		}else{
			$Model = M('');
			$Model->startTrans();
			if($_FILES['file']['name']!=null){
				if($NewsModel->upload()=='上传失败'){
					$this->error('上传失败');
				}
			}

			$NewsModel->publish_time = date('y-m-d H:i:s',time());
			$NewsModel->state = '0';
			$NewsModel->contributor = session('login');

			$newResult = $NewsModel->add();
			$newsId = $NewsModel->getLastInsID();

			//添加关键字
			$KeywordModel = M('NewsKeyword');
			$KeywordStr = $_POST['keyword'];
			$KeywordArr = json_decode($KeywordStr,TRUE);

			$sign = true;
			foreach ($KeywordArr as &$item) {
				if( $item['id'] == 0) {
					if ( $keyword = $KeywordModel->where(array('keyword'=>$item['keyword']))->find() ){
						$item['id'] = $keyword['id'];
					}else{
						$keywordResult = $KeywordModel->add(array('keyword'=>$item['keyword']));
						if( $keywordResult !== false ){
							$item['id'] = $KeywordModel->getLastInsID();
						}else{
							$sign = false;
						}
					}
				}
			}
			$keywordBelongSign = true;
			if($sign){
				$keywordBelongModel = M('NewsKeywordBelong');
				foreach ($KeywordArr as $item) {
					$keywordBelongResult = $keywordBelongModel->add(array('keyword_id'=>$item['id'],'news_id' => $newsId));
					if( $keywordBelongResult === false ){
						$keywordBelongSign = false;
					}
				}
			}
			if( $keywordBelongSign && $sign && $newResult!==false) {
				$Model->commit();
				$this->success('添加成功');
			}else{
				$Model->rollback();
				$this->error('添加失败');
			}
		}
	}


	public function comment(){
		$user_id = session('login');
		$commentModel = D('Comment');
		$order = I('get.order','newest');
		$all_count = $commentModel->getCommentCountByContributor($user_id);
		$zan_count = D('Zan')->getCountByContributor($user_id);
		$zan_count = $zan_count ? $zan_count:'0';
		$count = 10;
		$Page       = new \Think\Page($all_count,$count);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('all_count',$all_count);
		$this->assign('zan_count',$zan_count);
		$this->assign('order',$order);
		$this->assign('page',$show);
		$this->assign('p',I('get.p',1));
		$this->assign('user_id',$user_id);
		$this->assign('IssueContent','IssueContent/comment');
		$this->display('Issue/index');
	}


	public function fans(){
		$user_id = session('login');
		$followModel = M('Follow');
		$cancelFollowModel = M('CancelFollow');
		$fans_count = $followModel->where(array('follow_id'=>$user_id,'delete_tag'=>false))->count();
		$cancel_follow_count = $cancelFollowModel->where(array('follow_id'=>$user_id,'delete_tag'=>false))->count();
		$this->assign('fans_count',$fans_count);
		$this->assign('cancel_follow_count',$cancel_follow_count);
		$this->assign('IssueContent','IssueContent/fans');
		$this->display('Issue/index');
	}


	public function info(){
		session('login',41);
		$user_id = session('login');
		$applyModel = M('Apply');
		$condition['state'] = 1;
		$condition['user_id'] = $user_id;
		$info = $applyModel->where($condition)->find();
		$this->assign('info',$info);
		$this->assign('IssueContent','IssueContent/info');
		$this->display('Issue/index');
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
		$this->ajaxReturn($result);
	}
	public function cancelFollowLoading(){
		$cancelFollowModel = D('CancelFollow');
		$follow_id = session('login');
		$result = $cancelFollowModel->getGroupByTime($follow_id,I('get.startTime'),I('get.endTime'));
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