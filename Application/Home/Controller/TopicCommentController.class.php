<?php
namespace Home\Controller;
use Think\Controller;
class TopicCommentController extends Controller {

    public function loading(){
        $topic_id = I('get.topic_id');
        $TopicCommentModel = D('TopicComment');
        $List = $TopicCommentModel->getByTopicId($topic_id,1,4);
        if(IS_AJAX)
            echo json_encode($List);
    }

    //发表评论
    public function addComment(){
    	if(session('?login')){
			$model = M('');
			$TopicCommentModel = D('TopicComment');
			$result = $TopicCommentModel->create();
			if($result){
				$user_id = session('login');
				$ImgStr = I('post.imgStr','');
				$TopicCommentModel->sender = session('login');
				$TopicCommentModel->time = date('Y-m-d H-i-s',time());
				$TopicCommentModel->zan_count = 0;
				
				$model->startTrans();//开启事务
				$content = I('post.content','','htmlspecialchars_decode');
				$messageReplyData = $TopicCommentModel->checkAndSendMessage($content);//检查是否符合@回复


				$TopicCommentResult = $TopicCommentModel->add();
				$other_id = $TopicCommentModel->getLastInsID();
				$topic_id = I('post.topic_id');
				$comment_id = I('post.comment_id');
				if($ImgStr != ''){
					$ImgStr = substr($ImgStr,0,-1);
					$TopicPictrueModel = D('TopicPicture');
					$PictrueResult = $TopicPictrueModel->addDataByTypeAndImgStr($other_id,2,$ImgStr);
				}
				$j = 0;
				$messageData = array();
				$topic_user_id = M('Topic')->where(array('id'=>$topic_id))->getField('user_id');
				if($comment_id == 0){//如果为评论信息、评论信息不存在@操作
					$comment_count_up = M('Topic')->where(array('id'=>$topic_id))->setInc('comment_count',1);
					if($topic_user_id!=$user_id){//如果评论者不为自己
						$messageData[$j]['content_id'] = $other_id;//消息数据
						$messageData[$j]['time'] = date('Y-m-d H-i-s',time());
						$messageData[$j]['read_tag'] = (bool)0;
						$messageData[$j]['type'] = 1;
						$messageData[$j]['user_id'] = $topic_user_id;
						$j++;
					}
				}else{//如果为回复信息
					$comment_user_id = $TopicCommentModel->where(array('id'=>$comment_id))->getField('sender');
					if($comment_user_id==$topic_user_id && $comment_user_id!=$user_id){//评论者==话题者!=发送者
						$messageData[$j]['content_id'] = $other_id;//消息数据
						$messageData[$j]['time'] = date('Y-m-d H:i:s',time());
						$messageData[$j]['read_tag'] = (bool)0;
						$messageData[$j]['type'] = 2;
						$messageData[$j]['user_id'] = $topic_user_id;
						$j++;
					}else if($comment_user_id!=$topic_user_id){
			

						if($topic_user_id!=$user_id){//评论者!=话题者==发送者
							$messageData[$j]['content_id'] = $other_id;//话题者接收消息
							$messageData[$j]['time']       = date('Y-m-d H:i:s',time());
							$messageData[$j]['read_tag']   = (bool)0;
							$messageData[$j]['type']       = 2;
							$messageData[$j]['user_id']    = $topic_user_id;
							$j++;
						}
						if($comment_user_id!=$user_id){
							//评论者发送消息
							$messageData[$j]['content_id'] = $other_id;//评论者接收消息
							$messageData[$j]['time']       = date('Y-m-d H:i:s',time());
							$messageData[$j]['read_tag']   = (bool)0;
							$messageData[$j]['type']       = 2;
							$messageData[$j]['user_id']    = $comment_user_id;
							$j++;
						}
					}
					// else if($comment_user_id==$user_id && $topic_user_id!=$user_id){//评论者==发送者!=话题者

					// 	$messageData[$j]['content_id'] = $other_id;//消息数据
					// 	$messageData[$j]['time'] = date('Y-m-d H:i:s',time());
					// 	$messageData[$j]['read_tag'] = (bool)0;
					// 	$messageData[$j]['type'] = 2;
					// 	$messageData[$j]['user_id'] = $topic_user_id;
					// 	$j++;
					// }


					
					// 是否存在@ 如果存在添加@接收信息。 $messageData[1]、$messageData[2]
					/* Code... */
					if($messageReplyData!==false){
						$messageData[$j]['content_id'] = $other_id;//评论者接收消息
						$messageData[$j]['time']       = date('Y-m-d H:i:s',time());
						$messageData[$j]['read_tag']   = (bool)0;
						$messageData[$j]['type']       = 6;
						$messageData[$j]['user_id']    = $messageReplyData['user_id'];

						$j++;
					}

					dump($messageData);

					$comment_count_up = 1;
				}

				
				if(count($messageData)!=0){
					$MessageManagerModel = M('MessageManager');
					$MessageResult = $MessageManagerModel->addAll($messageData);
				}else
					$MessageResult = 'no_message';
				if($TopicCommentResult!=0&&$comment_count_up!=0&&$MessageResult!==false){
					if($ImgStr!= '' ){
						if($PictrueResult!=0){
							$message = '200';
							$model->commit();
						}else{
							$message = '发表失败';
							$model->rollback();
						}
					}else{
						$message = '200';
						$model->commit();
					}
				}else{
					$message = '发表失败';
					$model->rollback();
				}
				session('message',$message);
				if($comment_id=='0'){
					$this->redirect('Topic/detail',array('id'=>$topic_id,'sort_type'=>'time_down'));
				}else{
					// 如果为回复，返回当前评论页面
					$sort_type = I('get.sort_type','');
					$p = I('get.p',1);
					$this->redirect('Topic/detail',array('id'=>$topic_id,'sort_type'=>$sort_type,'p'=>$p));
					
				} 

			}else{
				// echo '数据创建失败';
				session('message',$message);
				$this->redirect('Topic/detail',array('id'=>$topic_id,'sort_type'=>'time_down'));
			}
		}else{
			// 未登录
			$this->redirect('Login/index');
		}
    }








    //查看评论 跳到相关页数
    public function view(){
		$id = I('get.id');
		if($id!=''){
			$TopicCommentModel = M('TopicComment');
			$arr = $TopicCommentModel->where(array('id'=>$id))->field('id,topic_id,comment_id,time')->find();
			if(count($arr)!=0){
				if($arr['comment_id']=='0'){
					$pageId = $arr['id'];
					$time = $arr['time'];
				}else{
					$pageId = $id;
					$time = $TopicCommentModel->where(array('id'=>$arr['id']))->getField('time');
				}
					

				$condition['comment_id'] = array('eq',0);
				$condition['topic_id']   = array('eq',$arr['topic_id']);
				$condition['delete_tag'] = array('eq',(bool)0);
				// $AllCount = $TopicCommentModel->where($condition)->order('time asc')->count();
				$condition['time'] = array('lt',$time);
				$PreCount = $TopicCommentModel->where($condition)->order('time asc')->count();
				if($PreCount%10==0)
					$p = (int)$PreCount/10;
				else
					$p = (int)($PreCount/10)+1;
				$this->redirect('/t/'.$arr['topic_id']."#c".$id,array('p'=>$p));

			}else{
				echo '该评论不存在';
			}
		}else{
			echo '参数错误';
		}
	}
}