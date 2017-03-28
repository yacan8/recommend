<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class CollectionModel extends RelationModel{
	//关联属性
	protected $_link = array(
	    'News'  =>  array(
	    	'mapping_type' =>self::HAS_ONE,
	        'class_name' => 'News',
	        'foreign_key'=>'collected',
	    )
	);

	/**
	 * [delect 取消收藏]
	 * @param  [Integer] $collected  [NEWS ID]
	 * @param  [String] $collecting [用户手机号码ID]
	 * @return [bool]             [返回是否删除成功]
	 */
	public function delect($collected,$collecting){
		$result = $this->where("collected= $collected and collecting ='$collecting'")->delete();
		if($result!=0)
			return true;
		else
			return false;
	}
}