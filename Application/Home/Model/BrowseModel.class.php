<?php
namespace Home\Model;
use Think\Model;
class BrowseModel extends Model{
	//关联属性


	public function getIndexByTime($news_id,$startTime,$endTime){
		$startTime = $startTime . ' 00:00:00';
		$endTime = $endTime . ' 23:59:59';
		if($news_id != 0){
			$condition['news_id'] = $news_id;
		}
		$condition['_string'] = "time between '{$startTime}' and '{$endTime}'";
		$field = "count(1) count ,DATE_FORMAT(time,'%Y-%m-%d') date";
		return $this->where($condition)->field($field)->order('date desc')->group('date')->select();
	}


	public function getAllCountByUserId($user_id){
		$subQuery = M('News')->where(array('contributor'=>$user_id,'delete_tag'=>false))->field('id')->select(false);
		$condition['_string'] = 'news_id in ('.$subQuery.')';
		return $this->where($condition)->count();
	}
}