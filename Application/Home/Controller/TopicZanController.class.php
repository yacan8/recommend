<?php

namespace Home\Controller;
use Think\Controller;
//话题控制器
class TopicZanController extends Controller{

	public function AjaxZan(){
		if(IS_AJAX){
			if(session('?login')){
				$model = M('');
				$zan_id = I('post.zan_id');
				$type = I('post.type');
				$user_id = session('login');
				$ZanModel = D('TopicZan');
				$list = $ZanModel->checkByZanIdAndUserIdAndType($zan_id,$user_id,$type);
				$model->startTrans();//开启事务
				if(count($list)==0){//如果为第一次点赞
					$data['zan_id']  	= $zan_id;
					$data['user_id'] 	= $user_id;
					$data['type']    	= $type;
					$data['delete_tag'] = (bool)0;
					$result = $ZanModel->add($data);


					//消息
					$id = $ZanModel->getLastInsID();
					$MessageManagerModel = M('MessageManager');
					if($type=='1'){//话题点赞
						$mesData['type'] = 3;
						$receiver = M('Topic')->where(array('id'=>$zan_id))->getField('user_id');
						
					}else{
						$mesData['type'] = 4;
						$receiver = M('TopicComment')->where(array('id'=>$zan_id))->getField('sender');
					}
					if($receiver!=$user_id){//如果为点赞自己
						$mesData['user_id'] = $receiver;
						$mesData['content_id'] = $id;
						$mesData['time'] = date('Y-m-d H:i:s',time());
						$mesData['read_tag'] = (bool)0;
						$messageResult = $MessageManagerModel->add($mesData);
					}else{
						$messageResult = 1;
					}
						



				}else{//曾经点赞过
					if($list[0]['delete_tag'] == '1')
						$data['delete_tag'] = (bool)0;//解决sql语句自动把bit转化为string问题
					else if($list[0]['delete_tag'] == '0')
						$data['delete_tag'] = (bool)1;
					$result = $ZanModel->where("id=".$list[0]['id'])->save($data);
					// echo $ZanModel->getLastSql();
					$messageResult = 1;
				}

				//冗余字段zan_count加减
				if($type == '1')//如果为话题点赞
					$ZanTypeModel =  M('Topic');
				else if($type == '2')//如果为评论点赞
					$ZanTypeModel =  M('TopicComment');

				if(count($list)==0||$list[0]['delete_tag'] == '1'){//如果曾经取消点赞为点赞过
					$resultZanCount = $ZanTypeModel->where(array('id'=>$zan_id))->setInc('zan_count',1);
				}else{//如果为取消点赞
					$resultZanCount = $ZanTypeModel->where(array('id'=>$zan_id))->setDec('zan_count',1);
				}



				
				

				if($result!=0&&$resultZanCount!=0&&$messageResult!=0){//成功 提交
					$json['Code'] = '200';
					$json['Message'] = '操作成功';
					$model->commit();
				}else{//失败 回滚
					$json['Code'] = '201';
					$json['Message'] = '操作失败';
					$model->rollback();
				}
			}else{
				$json['Code'] = '199';
				$json['Message'] = '您还未登录';
			}
			echo json_encode($json);
		}
	}
}