<?php
namespace Home\Model;
use Think\Model\RelationModel;
class NewsKeywordBelongModel extends RelationModel
{
	/**
	 * [$_link 关联属性]
	 * @var array
	 */
	protected $_link = array(
		'keyword' => array(
			'mapping_type' => self::BELONGS_TO,
			'class_name' => 'NewsKeyword',
			'foreign_key' => 'keyword_id',
			'mapping_fields' => 'keyword',
			'as_fields' => 'keyword'
		)
	);


	public function getKeywordByNewsId($news_id) {
		return $this->relation('keyword')->where(array('news_id'=>$news_id))->select();
	}
}