<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class ApplyModel extends RelationModel{


	protected $_link = array(
		'userInfo' => array(
			'mapping_type' =>self::BELONGS_TO,
			'class_name' => 'Login',
			'foreign_key'=>'user_id',
			'mapping_fields' => 'nickname,icon'
		)
	);




}