<?php
namespace Home\Model;
use Think\Model\RelationModel;
class VisitorNewsModel extends RelationModel{
	protected $_link = array(
		'type'  =>  array(
			'mapping_type' =>self::BELONGS_TO,
			'class_name' => 'News',
			'foreign_key'=>'news_id',
			'mapping_fields'=>'type',
			'as_fields' =>'type',
			'condition' => 'delete_tag = 0'
		),
	);
	//关联属性
	public function getIndexByTime($news_id,$startTime,$endTime){
		if($news_id != 0){
			$condition['news_id'] = $news_id;
		}
		$condition['_string'] = "date between '{$startTime}' and '{$endTime}'";
		$field = "count(1) count,DATE_FORMAT(date,'%Y-%m-%d') date";
		return $this->where($condition)->field($field)->order('date desc')->group('date')->select();
	}


	public function getVisitorListByUserIdAndBeginTime($user_id,$allowRecommendBeginTime){
		return $this->relation('type')-> where(array( 'user_id' => $user_id ,'date'=> array('gt',$allowRecommendBeginTime))) ->order('date desc') -> field('news_id,date') -> select();
	}
}