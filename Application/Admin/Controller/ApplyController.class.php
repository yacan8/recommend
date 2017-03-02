<?php
namespace Admin\Controller;
use Think\Controller;

class ApplyController extends Controller{

	public function index(){
		$state = I('get.state',0);
		$p = I('get.p');
		$count = 10;
		$applyModel = D('Apply');

		$condition['state'] = $state;
		$result = $applyModel->where($condition)->relation(true)->order('time desc')->page($p,$count)->select();
		$all_count = $applyModel->where($condition)->count();
		$Page       = new \Think\Page($all_count,$count);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('applyList',$result);
		$this->assign('all_count',$all_count);
		$this->assign('page',$show);
		$this->display('index');

	}

	public function check(){
		$login_id = session('Adminlogin');
		$loginModel = M('Login');
		$power = $loginModel->where(array('id'=>$login_id))->getField('power');
		if( $power == '2' ){
			$data['state'] = I('get.state');
			$id = I('get.id');
			$model = M('');
			$model->startTrans();
			$applyModel = M('Apply');
			if($data['state'] == '1'){
				$user_id = $applyModel->where(array('id'=>$id))->getField('user_id');
				$loginResult = $loginModel->where(array('id'=>$user_id))->save(array('power'=>3));
			}else{
				$loginResult = 1;
			}
			$result = $applyModel->where(array('id'=>$id))->save($data);
			if( $result !== false && $loginResult !== false) {
				$model->commit();
				$this->success('操作成功');
			}else{
				$model->rollback();
				$this->error('操作失败');
			}
		}else{
			$this->error('你没有权限');
		}
	}

}