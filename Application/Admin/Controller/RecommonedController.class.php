<?php
namespace Admin\Controller;
use Think\Controller;

class RecommonedController extends Controller{


	public function index(){
		$configList = M('RecommonedConfig')->order('state desc')->select();
		$this->assign('title','推荐配置管理');
		$this->assign('configList',$configList);
		$this->display();
	}

	public function saveOrUpdateView(){
		$id = I('get.id',0);
		if( $id !=0 ){
			$config = M('RecommonedConfig')->find($id);
			$this->assign('r',$config);
		}

		$this->assign('title','推荐配置管理');
		$this->display('addOrUpdate');
	}

	public function saveOrUpadte(){
		$recommonedModel = D('RecommonedConfig');
		$createInfo = $recommonedModel->create();
		if( $createInfo ){
			$id = I('post.id');
			
			if( $id ) {
				$result = $recommonedModel->where(array('id'=>$id))->save();
			}else{
				$recommonedModel->state = false;
				$result = $recommonedModel->add();
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
		$recommonedModel = M('RecommonedConfig');
		$recommonedModel->startTrans();
		$closeResult = $recommonedModel->where('state!=0')->save(array('state'=>(bool)0));
		$openResult = $recommonedModel->where(array('id'=>$id))->save(array('state'=>1));
		if( $closeResult !== false && $openResult !== false ) {
			$recommonedModel->commit();
			$this->success('启动成功');
		}else{
			$recommonedModel->rollback();
			$this->error('操作失败');
		}
	}
	public function deleteConfig(){
		$id = I('get.id',0);
		$recommonedModel = M('RecommonedConfig');
		$deleteResult = $recommonedModel->where(array('id'=>$id))->delete();
		if( $deleteResult !== false ) {
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	}
}