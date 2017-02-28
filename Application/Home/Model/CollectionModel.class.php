<?php
namespace Home\Model;
use Think\Model\RelationModel;
class CollectionModel extends RelationModel{
	//关联属性
	protected $_link = array(
	    'News'  =>  array(
	    	'mapping_type' =>self::HAS_ONE,
	        'class_name' => 'News',
	        'foreign_key'=>'collection_id',
	        'mapping_fields'=>'id,type,title,intro,publish_time,contributor'
	    )
	);

	/**
	 * [DeleteById 取消收藏]
	 * @param  [Integer] $collected  [NEWS ID]
	 * @param  [String] $collecting [用户手机号码ID]
	 * @return [bool]             [返回是否删除成功]
	 */
	public function DeleteById($collected,$collecting){
		
		$result = $this->where(array('collected'=>$collected,'collecting' =>$collecting))->delete();
		if($result!=0)
			return true;
		else
			return false;
	}
	/**
	 * [CheckIsCollected 检测是否已被收藏]
	 * @param [Integer]  $other_id [被收藏ID]
	 * @param [Integer]  $user     [用户ID]
	 * @param [integer] $type     [收藏类型 1为新闻收藏 2为话题收藏]
	 * @return [List] [返回查询列表 如果存在长度且删除标识delete_tag为0 为已被收藏 否则为未收藏]
	 */
	public function CheckIsCollected($other_id,$user,$type=1){
		$data['collected'] = $other_id;
		$data['collecting'] = $user;
		$data['type'] = $type;
		$condition['_logic'] = 'AND';
		$list = $this->where($data)->field('id,delete_tag')->select();
		return $list;
	}

	public function getList($user_id,$page,$count){
		$condition['user_id'] = $user_id;
		$result = $this->where($condition)->page($page,$count)->select();
		$newsModel = D('News');
		foreach ($result as &$item) {
			$item['content'] = $newsModel->getNewsById($item['collection_id']);
		}
		return $result;


	}
}