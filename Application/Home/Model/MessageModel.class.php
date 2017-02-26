<?php
namespace Home\Model;
use Think\Model\RelationModel;
class MessageModel extends RelationModel{

	//关联属性
//	protected $_link = array(
//	    'News'  =>  array(
//	    	'mapping_type' =>self::BELONGS_TO,
//	        'class_name' => 'News',
//	        'foreign_key'=>'other_id',
//	        'mapping_fields'=>'id,type,title'
//	    )
//	);


	public function getList($user_id,$page,$count){
		$messages = $this -> where(array('user_id'=>$user_id)) -> order('time desc') -> page($page,$count) ->select();
		$commentModel = D('Comment');
		$zanModel = D('Zan');

		foreach($messages as &$message){
			$messageType = $message['type'];
			switch ($messageType) {
				case '1':
					$contentModel = $zanModel;
					break;
				case '2':
				case '3':
					$contentModel = $commentModel;
					break;
				default:
					$contentModel = null;
					break;
			}
			if($contentModel !== null){
				$message['content'] = call_user_func(array($contentModel,'getMessage'.$messageType),$message['content_id']);
			}
		}
		return $messages;
	}

}
