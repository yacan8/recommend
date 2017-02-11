<?php
namespace Home\Controller;
use Think\Controller;
class CollectionController extends Controller{


	public function AjaxCollecting(){
		if(IS_AJAX){
			if(session('?login')){
				$collect_id = I('post.collect_id');
				$type = I('post.type');
				$user_id = session('login');
				$CollectionModel = D('Collection');
				$list = $CollectionModel->CheckIsCollected($collect_id,$user_id,$type);
				if(count($list)==0){//如果为第一次收藏
					$data['collected']  	= $collect_id;
					$data['collecting'] 	= $user_id;
					$data['type']    	= $type;
					$data['delete_tag'] = (bool)0;
					$result = $CollectionModel->add($data);
				}else{//曾经点收藏
					if($list[0]['delete_tag'] == '1')
						$data['delete_tag'] = (bool)0;//解决sql语句自动把bit转化为string问题
					else if($list[0]['delete_tag'] == '0')
						$data['delete_tag'] = (bool)1;
					$result = $CollectionModel->where("id=".$list[0]['id'])->save($data);
				}
				if($result !=0){
					if(count($list)==0||$list[0]['delete_tag'] == '1'){
						$json['Code'] = '200';
						$json['Message'] = '收藏成功';
					}else{
						$json['Code'] = '201';
						$json['Message'] = '取消收藏成功';
					}
				}else{
					$json['Code'] = '202';
					$json['Message'] = '操作失败';
				}
				// echo $CollectionModel->getLastSql();
			}else{
				$json['Code'] = '199';
				$json['Message'] = '您还未登录';
			}
			echo json_encode($json);
		}
	}

	//取消新闻收藏
	public function d_new_collection(){
		$collect_id = I('get.id');
		$CollectionModel = M('Collection');
		$result = $CollectionModel->where(array('id'=>$collect_id))->save(array('delete_tag'=>(bool)1));
		if($result===false){
			session('ErrorMessage','修改失败');
		}
	    header('location:'.$_SERVER['HTTP_REFERER']);
	}
}