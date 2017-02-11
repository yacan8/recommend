<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class NewsLabelModel extends RelationModel{
	//关联属性
	protected $_link = array(
	    'label'  =>  array(
	    	'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Label',
	        'foreign_key'=>'label_id',
	        'mapping_fields'=>'label',
	        'as_fields'=>'label'
	    )
	);
	/**
	 * [addLabel 新闻添加标签]
	 * @param [Integer] $new_id   [传入的新闻的id]
	 * @param [Integer] $label_id [传入的标签的ID]
	 * @return [bool] [添加成功与否]
	 */
	public function addLabel($new_id,$label_id){
		$data['news_id'] = $new_id;
		$data['label_id'] = $label_id;
		$result = $this->add($data);
		if($result!=0)	return true;
		else return false;
	}

	/**
	 * [getLabelByNewsId 获取标签通过新闻ID]
	 * @param  [Integer] $new_id [传入的新闻ID]
	 * @return [List]         [查询到的列表]
	 */
	public function getLabelByNewsId($id){
		return $this->relation(true)->where("news_id=$id")->select();
	}

}