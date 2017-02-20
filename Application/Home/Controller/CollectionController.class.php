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
	
}
