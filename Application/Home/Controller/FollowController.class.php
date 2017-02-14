<?php
namespace Home\Controller;
use Think\Controller;
class FollowController extends Controller{


	public function AjaxFollow(){
		if(IS_AJAX){
			if(session('?login')){
				$model = M('');
				$model->startTrans();//开启事务
				$follow_id = I('post.follow_id');
				$user_id = session('login');
				$FollowModel = D('Follow');
				$list = $FollowModel->checkFollow($user_id,$follow_id);
				if(count($list)==0){//如果为第一次关注
					$data['follow_id']  = $follow_id;
					$data['user_id'] 	= $user_id;
					$data['delete_tag'] = (bool)0;
					$result = $FollowModel->add($data);
					$content_id = $FollowModel->getLastInsID();
					$mesData['type'] = 5;
					$mesData['user_id'] = $follow_id;
					$mesData['content_id'] = $content_id;
					$mesData['time'] = date('Y-m-d H:i:s',time());
					$mesData['read_tag'] = (bool)0;


				}else{//曾经点收藏
					if($list[0]['delete_tag'] == '1')
						$data['delete_tag'] = (bool)0;//解决sql语句自动把bit转化为string问题
					else if($list[0]['delete_tag'] == '0')
						$data['delete_tag'] = (bool)1;
					$result = $FollowModel->where("id=".$list[0]['id'])->save($data);
				}
				if($result!=0){
					if(count($list)==0||$list[0]['delete_tag'] == '1'){
						$json['Code'] = '200';
						$json['Message'] = '关注成功';
					}else{
						$json['Code'] = '201';
						$json['Message'] = '取消关注成功';
						$model->commit();
					}
				}else{
					$json['Code'] = '202';
					$json['Message'] = '操作失败';
					$model->rollback();
				}
				// echo $CollectionModel->getLastSql();
			}else{
				$json['Code'] = '199';
				$json['Message'] = '您还未登录';
			}
			echo json_encode($json);
		}
	}
}
