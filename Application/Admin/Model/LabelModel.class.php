<?php
namespace Admin\Model;
use Think\Model;
class LabelModel extends Model{
	//关联属性
	

	/**
	 * [getAll 获取全部标签]
	 * @return [List] [所有标签的列表]
	 */
	public function getAll(){
		return $this->select();
	}

	/**
	 * [addLable 添加标签]
	 * @param [string] $label [传入的id]
	 * @return [Integer] [添加后的ID,添加失败返回0]
	 */
	public function addLable($label){
		$data['label'] = $label;
		$result = $this->add($data);
		if($result!=0){
			return $this->getLastInsID();
		}else{
			return 0;
		}
	}

}