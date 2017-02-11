<?php
namespace Home\Model;
use Think\Model;
class MessageManagerModel extends Model{




	/**
	 * [getMessageByUserId 获取消息]
	 * @param  [integer]  $user_id  [用户的ID]
	 * @param  integer $page     [页数]
	 * @param  integer $count    [每页显示个数]
	 * @param  integer $read_tag [是否已读]
	 * @return [List]            
	 */
	public function getMessageByUserId($user_id,$page=1,$count=10,$read_tag=0){
		$condition['read_tag'] = (bool)$read_tag;
		$condition['user_id']  = $user_id;
		$condition['delete_tag'] = (bool)0;
		$firstrow = ($page-1)*$count;
		$MessageList = $this->where($condition)->limit("$firstrow,$count")->field('id,time,type,content_id')->order('time desc')->select();

		$messageTypeContentList['1'] = 'TopicComment';//1话题被评论了 评论model
		$messageTypeContentList['2'] = 'TopicComment';//2评论被回复了  评论model
		$messageTypeContentList['3'] = 'TopicZan';//3为话题被点赞了 点赞model
		$messageTypeContentList['4'] = 'TopicZan';// 4评论被点赞了 点赞model
		$messageTypeContentList['5'] = 'Follow';//5为被关注了 关注Model
		$messageTypeContentList['6'] = 'TopicComment';//6有人@了你 评论Model
		// code... 拓展消息类型代码

		$Date = new \Org\Util\Date();
		for ($i=0; $i < count($MessageList); $i++) { 
			$type = $MessageList[$i]['type'];
			$content_id = $MessageList[$i]['content_id'];
			$ContentModel = D($messageTypeContentList[$type]);//实例化对应模型
			eval('$contentArray = $ContentModel->getMessageType'.$type.'('.$content_id.');');
			$MessageList[$i]['content'] = $contentArray;

			$MessageList[$i]['time'] = timeDiff($Date,$MessageList[$i]['time']);
		}
		// dump($MessageList);

		return $MessageList;
	}
}