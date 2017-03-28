<?php
namespace Home\Model;
use Think\Model\RelationModel;

class InterestModel extends RelationModel{
	protected $_link = array(
		"type" =>array(
			'mapping_type' =>  self::BELONGS_TO,
			'class_name'=>'Type',
			'foreign_key' =>  'type_id'
		)
	);
}