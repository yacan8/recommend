<?php
namespace Home\Model;
use Think\Model;
class BrowseModel extends Model{
	//关联属性


	public function getIndexByTime($news_id,$startTime,$endTime){
		if($news_id != 0){
			$condition['news_id'] = $news_id;
		}
		$condition['_string'] = "time between '{$startTime}' and '{$endTime}'";
		$field = "count(1) count ,DATE_FORMAT(time,'%Y-%m-%d') date";
		return $this->where($condition)->field($field)->order('date desc')->group('date')->select();
	}
}