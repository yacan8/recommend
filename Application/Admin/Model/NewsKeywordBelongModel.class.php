<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class NewsKeywordBelongModel extends RelationModel{
    //关联属性
	protected $_link = array(
	    'keyword'  =>  array(
	    	'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'NewsKeyword',
	        'foreign_key'=>'keyword_id',
	        'mapping_fields'=>'keyword',
	        'as_fields'=>'keyword'
	    )
	);
}
?>
