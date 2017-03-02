<?php
namespace Home\Model;
use Think\Model;
class VisitorNewsModel extends Model{
	//关联属性
	public function getIndexByTime($news_id,$startTime,$endTime){
		if($news_id != 0){
			$condition['news_id'] = $news_id;
		}
		$condition['_string'] = "date between '{$startTime}' and '{$endTime}'";
		$field = "count(1) count,DATE_FORMAT(date,'%Y-%m-%d') date";
		return $this->where($condition)->field($field)->order('date desc')->group('date')->select();
	}
}