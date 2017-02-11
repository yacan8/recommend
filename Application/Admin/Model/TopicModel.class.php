<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class TopicModel extends RelationModel{
	

	/**
	 * [$_link 关联属性]
	 * @var array
	 */
	protected $_link = array(
	    'type'  =>  array(
	    	'mapping_type' =>self::HAS_MANY,
	        'class_name' => 'TopicTypeBelong',
	        'foreign_key'=>'topic_id',
	        'mapping_fields'=>'type_id'
	    ),
	    'userinfo' => array(
			'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Login',
	        'foreign_key'=>'user_id',
	        'mapping_fields'=>'id,nickname,icon'
	    )
	);

	/**
	 * [getList 获取列表]
	 * @param  [Integer] $type     [类型]
	 * @param [Integer] $page [传入的页数]
	 * @param [Integer] $count [引用输出最后的条数]
	 * @return [list]           [查询到的列表]
	 */
	public function getList($user_id,$page,&$count){
		if($user_id!=0){
			$condition['user_id'] = $user_id;
		}
		$condition['delete_tag'] = (bool)0;
		$condition['_logic'] = "AND";
		$firstRow = ($page-1)*$count;
		$List = $this->relation(array('type','userinfo'))
					 ->where($condition)
					 ->limit("$firstRow,10")
					 ->field("id,user_id,title,time,browse,comment_count")
					 ->order('time desc')
					 ->select();
		$count = $this->where($condition)->count();
		$TopicTypeModel = M('TopicType');
		for ($i=0; $i < count($List); $i++) { 
			for ($j=0; $j < count($List[$i]['type']); $j++) { 
				$str = $TopicTypeModel->where(array('id'=> $List[$i]['type'][$j]['type_id']))->getField('type');
				$List[$i]['type'][$j]['type'] = $str;
			}

			if($List[$i]['userinfo']['icon']=='')
				$List[$i]['userinfo']['icon'] = C('__DATA__').'/login_thumb/default.jpg';
			else{
				$List[$i]['userinfo']['icon'] = C('__DATA__')."/login_thumb/".$List[$i]['userinfo']['icon'];
			}
			
		}
	
		return $List;
	}
	

}