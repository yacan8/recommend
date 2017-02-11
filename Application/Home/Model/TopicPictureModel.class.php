<?php
namespace Home\Model;
use Think\Model;
class TopicPictureModel extends Model{
	
	/**
	 * [getImgByTypeAndId 通过类型和ID获取图片]
	 * @param  [Integer] $type [类型 1为话题 2为评论]
	 * @param  [Integer] $id   [传入的ID]
	 * @return [List]       [查询到的图片列表]
	 */
	public function getImgByTypeAndId($type,$id){
		$condition['type'] = $type;
		$condition['other_id'] = $id;
		return $this->where($condition)->field('image')->select();
	}
	
	/**
	 * [addDataByTypeAndImgStr 通过类型和图片路径添加字符串添加数据]
	 * @param [Integer] $other_id   [归属id 评论id或话题id]
	 * @param [Integer] $type   [图片类型 1为话题 2为评论]
	 * @param [String] $ImgStr [逗号分隔的图片路径字符串]
	 */
	public function addDataByTypeAndImgStr($other_id,$type,$ImgStr){
		$ImgArray = explode(',',$ImgStr);
		$ImgData = array();
		for ($i=0; $i < count($ImgArray); $i++) { 
			$ImgData[] = array('other_id'=>$other_id,'type'=> $type,'image'=>$ImgArray[$i]);
		}
		$PictrueResult = $this->addAll($ImgData);
		return $PictrueResult;
	}


	/**
	 * [getPicByUserId 获取有图片的话题、评论的ID 时间]
	 * @param  [Integer] $user_id [用户ID]
	 * @param  integer $page    [description]
	 * @param  integer $count   [description]
	 * @return [List]
	 */
	public function getPicByUserId($user_id,$page=1,$count=10){
		$M = M('');
		$firstrow = ($page-1)*$count;
		$List = $M->query("(select time,t.id id,1 type from cl_topic t where t.user_id = $user_id and  (select count(1) from cl_topic_picture where other_id = t.id and type= 1)>0 ) union (select time,c.id id,2 type from cl_topic_comment c  where c.sender = $user_id and (select count(1) from cl_topic_picture where other_id = c.id and type= 2)>0) order by time desc limit $firstrow,$count");
		for ($i=0; $i < count($List); $i++) { 
			$List[$i]['time'] = substr($List[$i]['time'],0,10);
		}
		return $List;
	}


	/**
	 * [getCount 获取用户发表的话题、评论中有图片的个数和]
	 * @param  [Integer] $user_id [用户ID]
	 * @return [Integer]          [话题和评论个数和]
	 */
	public function getCount($user_id){
		$DB_PREFIX = C('DB_PREFIX');
		$M = M('');
		$topic_count   = $M->query("select count(1) count from ".$DB_PREFIX."topic t where t.user_id = $user_id and (select count(1) from ".$DB_PREFIX."topic_picture where other_id = t.id and type= 1)>0");
		$comment_count = $M->query("select count(1) count  from ".$DB_PREFIX."topic_comment c where c.sender = $user_id and  (select count(1) from ".$DB_PREFIX."topic_picture where other_id = c.id and type= 2) >0");
		$count = (int)$topic_count[0]['count']+(int)$comment_count[0]['count'];
		return $count;
	}

	/**
	 * [getPicCount 获取用户的总图片数]
	 * @param  [Integer] $user_id [用户ID]
	 * @return [Integer]          [总图片数]
	 */
	public function getPicCount($user_id){
		$DB_PREFIX = C('DB_PREFIX');
		$M = M('');
		$topic_count = $M->query('select sum((select count(1) from '.$DB_PREFIX.'topic_picture where other_id = t.id and type= 1)) count  from '.$DB_PREFIX.'topic t where t.user_id = '.$user_id);
		$comment_count = $M->query("select sum((select count(1) from ".$DB_PREFIX."topic_picture where other_id = c.id and type= 2)) count  from cl_topic_comment c where c.sender = ".$user_id);
		$count = (int)$topic_count[0]['count']+(int)$comment_count[0]['count'];
		return $count;
	}
}