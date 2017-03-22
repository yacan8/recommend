<?php
namespace Admin\Controller;
use Think\Controller;

class RecommendController extends Controller{


	public function index(){
		$configList = M('recommendConfig')->order('state desc')->select();
		$this->assign('title','推荐配置管理');
		$this->assign('configList',$configList);
		$this->display();
	}

	public function saveOrUpdateView(){
		$id = I('get.id',0);
		if( $id !=0 ){
			$config = M('recommendConfig')->find($id);
			$this->assign('r',$config);
		}

		$this->assign('title','推荐配置管理');
		$this->display('addOrUpdate');
	}

	public function saveOrUpadte(){
		$recommendModel = D('recommendConfig');
		$createInfo = $recommendModel->create();
		if( $createInfo ){
			$id = I('post.id');
			
			if( $id ) {
				$result = $recommendModel->where(array('id'=>$id))->save();
			}else{
				$recommendModel->state = false;
				$result = $recommendModel->add();
			}

			if( $result !== false){
				$this->success('操作成功','index');
			}else{
				$this->error('操作失败');
			}
		}else{
			$this->error('操作失败');
		}
	}

	public function openConfig(){
		$id = I('get.id',0);
		$recommendModel = M('RecommendConfig');
		$recommendModel->startTrans();
		$closeResult = $recommendModel->where('state!=0')->save(array('state'=>(bool)0));
		$openResult = $recommendModel->where(array('id'=>$id))->save(array('state'=>1));
		if( $closeResult !== false && $openResult !== false ) {
			$recommendModel->commit();
			$this->success('启动成功');
		}else{
			$recommendModel->rollback();
			$this->error('操作失败');
		}
	}
	public function deleteConfig(){
		$id = I('get.id',0);
		$recommendModel = M('RecommendConfig');
		$deleteResult = $recommendModel->where(array('id'=>$id))->delete();
		if( $deleteResult !== false ) {
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	}
}