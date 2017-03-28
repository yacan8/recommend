<?php
namespace Home\Controller;
use Think\Controller;
class CollectionController extends Controller{


	public function AjaxCollecting(){
		if(IS_AJAX){
			if(session('?login')){
				$collection_id = I('post.collection_id');
				$user_id = session('login');
				$CollectionModel = M('Collection');
				$info = $CollectionModel->where(array('collection_id'=>$collection_id,'user_id'=>$user_id))->find();
				if(!$info){//如果为第一次收藏
					$data['collection_id'] 	= $collection_id;
					$data['user_id'] 	= $user_id;
					$result = $CollectionModel->add($data);
					$code = 200;
				}else{//曾经点收藏
					$result = $CollectionModel->where(array('id'=>$info['id']))->delete();
					$code = 201;
				}
				if($result !== false){
					$json['code'] = $code;//201为取消收藏成功
					$json['success'] = true;
					$json['message'] = '操作成功';
				}else{
					$json['code'] = 500;
					$json['success'] = false;
					$json['message'] = '操作成功';
				}
			}else{
				$json['code'] = 400;
				$json['success'] = false;
				$json['message'] = '你还没登录';
			}
			$this->ajaxReturn($json);
		}
	}


	public function loading(){
		if(session('?login')){
			$user_id = session('login');
			$page = I('get.page',1);
			$count = 10;
			$CollectionModel = D('Collection');
			$json = $CollectionModel->getList($user_id,$page,$count);
			$this->ajaxReturn($json);
		}else{
			$this->ajaxReturn(array());
		}
	}

	public function cancel(){
		if(session('?login')){
			$id = I('post.id');
			$user_id = session('login');
			$collectionModel = M('Collection');
			if( $user_id == $collectionModel->where(array('id'=>$id))->getField('user_id')){
				$result = $collectionModel->where(array('id'=>$id))->delete();
				if($result!== false){
					$json['success'] = true;
				}else{
					$json['success'] = false;
					$json['message'] = '操作失败';
				}
			}else{
				$json['success'] = false;
				$json['message'] = '你还没有权限';
			}
		}else{
			$json['success'] = false;
			$json['message'] = '你还没登录';
		}
		$this->ajaxReturn($json);
	}

}
